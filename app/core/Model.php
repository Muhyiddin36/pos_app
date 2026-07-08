<?php
/**
 * Base Model - CRUD generik di atas PDO dengan dukungan soft delete.
 * Model turunan cukup mengatur $table, $primaryKey, $softDelete, $fillable.
 * Untuk query kompleks (join, laporan), model turunan boleh memakai
 * db() langsung dengan prepared statement.
 */

declare(strict_types=1);

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';
    protected static bool $softDelete = false;
    /** @var string[] kolom yang boleh diisi lewat create()/update() */
    protected static array $fillable = [];

    protected static function db(): PDO
    {
        return Database::connection();
    }

    protected static function baseWhere(): string
    {
        return static::$softDelete ? ' WHERE deleted_at IS NULL' : '';
    }

    public static function all(string $orderBy = ''): array
    {
        $sql = 'SELECT * FROM ' . static::$table . static::baseWhere();
        if ($orderBy !== '') {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        return static::db()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = 'SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id';
        $sql .= static::$softDelete ? ' AND deleted_at IS NULL' : '';
        $stmt = static::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * @param array<string,mixed> $conditions kolom => nilai (disambung AND, exact match)
     */
    public static function findWhere(array $conditions): ?array
    {
        $rows = static::whereAll($conditions);
        return $rows[0] ?? null;
    }

    /**
     * @param array<string,mixed> $conditions
     */
    public static function whereAll(array $conditions, string $orderBy = '', int $limit = 0): array
    {
        [$clause, $params] = static::buildWhere($conditions);
        $sql = 'SELECT * FROM ' . static::$table . $clause;
        if ($orderBy !== '') {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit;
        }
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count(array $conditions = []): int
    {
        [$clause, $params] = static::buildWhere($conditions);
        $stmt = static::db()->prepare('SELECT COUNT(*) AS c FROM ' . static::$table . $clause);
        $stmt->execute($params);
        return (int) $stmt->fetch()['c'];
    }

    /**
     * @param array<string,mixed> $conditions
     * @return array{0:string,1:array<string,mixed>}
     */
    protected static function buildWhere(array $conditions): array
    {
        $parts = [];
        $params = [];
        if (static::$softDelete) {
            $parts[] = 'deleted_at IS NULL';
        }
        foreach ($conditions as $col => $val) {
            $key = str_replace('.', '_', $col);
            if ($val === null) {
                $parts[] = "$col IS NULL";
            } else {
                $parts[] = "$col = :$key";
                $params[$key] = $val;
            }
        }
        $clause = $parts === [] ? '' : ' WHERE ' . implode(' AND ', $parts);
        return [$clause, $params];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function create(array $data): int
    {
        $data = static::onlyFillable($data);
        $columns = array_keys($data);
        $placeholders = array_map(fn ($c) => ':' . $c, $columns);
        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = static::db()->prepare($sql);
        $stmt->execute($data);
        return (int) static::db()->lastInsertId();
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function update(int $id, array $data): bool
    {
        $data = static::onlyFillable($data);
        if ($data === []) {
            return false;
        }
        $set = implode(',', array_map(fn ($c) => "$c = :$c", array_keys($data)));
        $sql = 'UPDATE ' . static::$table . " SET $set WHERE " . static::$primaryKey . ' = :__id';
        $data['__id'] = $id;
        $stmt = static::db()->prepare($sql);
        return $stmt->execute($data);
    }

    /** Soft delete jika didukung, jika tidak lakukan hard delete. */
    public static function delete(int $id): bool
    {
        if (static::$softDelete) {
            $sql = 'UPDATE ' . static::$table . ' SET deleted_at = NOW() WHERE ' . static::$primaryKey . ' = :id';
        } else {
            $sql = 'DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id';
        }
        $stmt = static::db()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    protected static function onlyFillable(array $data): array
    {
        if (static::$fillable === []) {
            return $data;
        }
        return array_intersect_key($data, array_flip(static::$fillable));
    }
}
