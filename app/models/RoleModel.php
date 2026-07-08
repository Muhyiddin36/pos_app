<?php

declare(strict_types=1);

final class RoleModel extends Model
{
    protected static string $table = 'roles';
    protected static array $fillable = ['slug', 'name', 'description'];

    public static function allWithPermissionCount(): array
    {
        $sql = 'SELECT r.*, COUNT(rp.permission_id) AS permission_count
                FROM roles r LEFT JOIN role_permissions rp ON rp.role_id = r.id
                GROUP BY r.id ORDER BY r.id';
        return Database::connection()->query($sql)->fetchAll();
    }
}
