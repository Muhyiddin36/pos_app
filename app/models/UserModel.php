<?php

declare(strict_types=1);

final class UserModel extends Model
{
    protected static string $table = 'users';
    protected static bool $softDelete = true;
    protected static array $fillable = [
        'branch_id', 'role_id', 'username', 'password_hash', 'full_name',
        'email', 'phone', 'is_active', 'last_login_at',
        'failed_login_count', 'locked_until',
    ];

    public static function findActiveByUsername(string $username): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM users WHERE username = :u AND is_active = 1 AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute(['u' => $username]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function usernameExists(string $username, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) c FROM users WHERE username = :u AND deleted_at IS NULL';
        $params = ['u' => $username];
        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $exceptId;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['c'] > 0;
    }

    public static function allWithRoleBranch(?int $branchId = null): array
    {
        $sql = 'SELECT u.*, r.name AS role_name, b.name AS branch_name
                FROM users u
                LEFT JOIN roles r ON r.id = u.role_id
                LEFT JOIN branches b ON b.id = u.branch_id
                WHERE u.deleted_at IS NULL';
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND u.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        $sql .= ' ORDER BY u.full_name';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
