<?php

declare(strict_types=1);

final class PermissionModel extends Model
{
    protected static string $table = 'permissions';

    public static function codesForRole(int $roleId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT p.code FROM permissions p
             INNER JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = :role_id'
        );
        $stmt->execute(['role_id' => $roleId]);
        return array_column($stmt->fetchAll(), 'code');
    }

    public static function allGroupedByModule(): array
    {
        $rows = Database::connection()->query('SELECT * FROM permissions ORDER BY module, id')->fetchAll();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['module']][] = $row;
        }
        return $grouped;
    }

    public static function syncRolePermissions(int $roleId, array $permissionIds): void
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare('DELETE FROM role_permissions WHERE role_id = :role_id');
            $stmt->execute(['role_id' => $roleId]);

            $insert = $db->prepare('INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)');
            foreach ($permissionIds as $permissionId) {
                $insert->execute(['role_id' => $roleId, 'permission_id' => (int) $permissionId]);
            }
            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
