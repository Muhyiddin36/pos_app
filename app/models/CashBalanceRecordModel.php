<?php

declare(strict_types=1);

final class CashBalanceRecordModel extends Model
{
    protected static string $table = 'cash_balance_records';
    protected static array $fillable = [
        'branch_id', 'cash_source_id', 'record_date', 'closing_balance', 'note', 'recorded_by', 'updated_by',
    ];

    /**
     * Ambil seluruh sumber dana aktif cabang beserta catatan saldo akhir pada
     * tanggal tertentu (LEFT JOIN - sumber tanpa catatan tetap tampil dengan closing_balance NULL).
     */
    public static function forBranchAndDate(int $branchId, string $date): array
    {
        $sql = 'SELECT cs.id AS cash_source_id, cs.name, cs.type, cs.opening_balance,
                       r.id AS record_id, r.closing_balance, r.note, r.updated_at,
                       u.full_name AS updated_by_name
                FROM cash_sources cs
                LEFT JOIN cash_balance_records r ON r.cash_source_id = cs.id AND r.record_date = :date
                LEFT JOIN users u ON u.id = r.updated_by
                WHERE cs.branch_id = :branch_id AND cs.is_active = 1 AND cs.deleted_at IS NULL
                ORDER BY cs.name';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['branch_id' => $branchId, 'date' => $date]);
        return $stmt->fetchAll();
    }

    public static function history(?int $branchId, string $from = '', string $to = ''): array
    {
        $sql = 'SELECT r.*, cs.name AS source_name, cs.type, b.name AS branch_name, u.full_name AS updated_by_name
                FROM cash_balance_records r
                INNER JOIN cash_sources cs ON cs.id = r.cash_source_id
                INNER JOIN branches b ON b.id = r.branch_id
                LEFT JOIN users u ON u.id = r.updated_by
                WHERE 1=1';
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND r.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        if ($from !== '') {
            $sql .= ' AND r.record_date >= :from';
            $params['from'] = $from;
        }
        if ($to !== '') {
            $sql .= ' AND r.record_date <= :to';
            $params['to'] = $to;
        }
        $sql .= ' ORDER BY r.record_date DESC, cs.name';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Simpan (buat/perbarui) saldo akhir sumber dana pada tanggal tertentu. */
    public static function upsert(int $branchId, int $cashSourceId, string $date, float $amount, string $note, int $userId): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO cash_balance_records (branch_id, cash_source_id, record_date, closing_balance, note, recorded_by, updated_by)
             VALUES (:branch_id, :cash_source_id, :record_date, :amount, :note, :user_id, :user_id2)
             ON DUPLICATE KEY UPDATE closing_balance = VALUES(closing_balance), note = VALUES(note), updated_by = VALUES(updated_by)'
        );
        $stmt->execute([
            'branch_id'      => $branchId,
            'cash_source_id' => $cashSourceId,
            'record_date'    => $date,
            'amount'         => $amount,
            'note'           => $note,
            'user_id'        => $userId,
            'user_id2'       => $userId,
        ]);
    }
}
