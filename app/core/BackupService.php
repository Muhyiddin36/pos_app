<?php
/**
 * Backup & Restore database murni PHP (tanpa shell_exec/mysqldump),
 * agar tetap kompatibel dengan shared hosting yang tidak menyediakan SSH.
 */

declare(strict_types=1);

final class BackupService
{
    public static function directory(): string
    {
        return STORAGE_PATH . '/backups';
    }

    /** Buat file backup SQL berisi struktur (CREATE TABLE) + data (INSERT) seluruh tabel. */
    public static function createBackup(): string
    {
        $db = Database::connection();
        $filename = 'backup_' . date('Ymd_His') . '.sql';
        $path = self::directory() . '/' . $filename;

        $handle = fopen($path, 'w');
        if ($handle === false) {
            throw new RuntimeException('Tidak dapat membuat file backup. Periksa hak akses folder storage/backups.');
        }

        fwrite($handle, "-- Backup " . APP_NAME . " - " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $createRow = $db->query('SHOW CREATE TABLE `' . $table . '`')->fetch();
            $createSql = $createRow['Create Table'] ?? null;
            if ($createSql === null) {
                continue;
            }
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n{$createSql};\n\n");

            $rowCount = (int) $db->query('SELECT COUNT(*) FROM `' . $table . '`')->fetchColumn();
            if ($rowCount === 0) {
                continue;
            }

            $stmt = $db->query('SELECT * FROM `' . $table . '`');
            $batch = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $values = array_map(fn ($v) => self::quote($db, $v), array_values($row));
                $batch[] = '(' . implode(',', $values) . ')';
                if (count($batch) >= 200) {
                    self::writeInsert($handle, $table, array_keys($row), $batch);
                    $batch = [];
                }
            }
            if ($batch !== []) {
                $columns = array_keys($db->query('SELECT * FROM `' . $table . '` LIMIT 1')->fetch(PDO::FETCH_ASSOC) ?: []);
                self::writeInsert($handle, $table, $columns, $batch);
            }
            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        return $filename;
    }

    private static function writeInsert($handle, string $table, array $columns, array $rows): void
    {
        $columnList = '`' . implode('`,`', $columns) . '`';
        fwrite($handle, "INSERT INTO `{$table}` ({$columnList}) VALUES\n" . implode(",\n", $rows) . ";\n");
    }

    private static function quote(PDO $db, mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        return $db->quote((string) $value);
    }

    /** @return array<int,array{name:string,size:int,created_at:int}> */
    public static function listBackups(): array
    {
        $files = glob(self::directory() . '/*.sql') ?: [];
        $list = [];
        foreach ($files as $file) {
            $list[] = ['name' => basename($file), 'size' => filesize($file), 'created_at' => filemtime($file)];
        }
        usort($list, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);
        return $list;
    }

    /** Jalankan file SQL backup untuk memulihkan database (menimpa data yang ada). */
    public static function restoreFromFile(string $path): void
    {
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException('File backup tidak dapat dibaca.');
        }
        self::restoreFromSql($sql);
    }

    public static function restoreFromSql(string $sql): void
    {
        $db = Database::connection();
        $statements = self::splitStatements($sql);

        $db->beginTransaction();
        try {
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if ($statement === '') {
                    continue;
                }
                $db->exec($statement);
            }
            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw new RuntimeException('Restore gagal: ' . $e->getMessage());
        }
    }

    /** Pisahkan file SQL menjadi statement individual berdasarkan titik-koma di luar string literal. */
    private static function splitStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];

            if ($inString) {
                $current .= $char;
                if ($char === '\\') {
                    // sertakan karakter escape berikutnya apa adanya
                    if ($i + 1 < $length) {
                        $current .= $sql[$i + 1];
                        $i++;
                    }
                    continue;
                }
                if ($char === $stringChar) {
                    $inString = false;
                }
                continue;
            }

            if ($char === "'" || $char === '"' || $char === '`') {
                $inString = true;
                $stringChar = $char;
                $current .= $char;
                continue;
            }

            if ($char === ';') {
                $statements[] = $current;
                $current = '';
                continue;
            }

            $current .= $char;
        }
        if (trim($current) !== '') {
            $statements[] = $current;
        }

        // buang baris komentar "-- ..." yang berdiri sendiri per statement
        return array_map(function (string $stmt): string {
            $lines = explode("\n", $stmt);
            $lines = array_filter($lines, fn ($line) => !str_starts_with(trim($line), '--'));
            return implode("\n", $lines);
        }, $statements);
    }
}
