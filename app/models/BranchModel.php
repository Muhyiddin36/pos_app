<?php

declare(strict_types=1);

final class BranchModel extends Model
{
    protected static string $table = 'branches';
    protected static bool $softDelete = true;
    protected static array $fillable = ['code', 'name', 'address', 'phone', 'is_active'];

    public static function codeExists(string $code, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) c FROM branches WHERE code = :code AND deleted_at IS NULL';
        $params = ['code' => $code];
        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $exceptId;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['c'] > 0;
    }

    public static function activeList(): array
    {
        return self::whereAll(['is_active' => 1], 'name');
    }
}
