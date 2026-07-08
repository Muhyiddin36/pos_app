<?php
/**
 * Pencatat audit log untuk semua aksi penting (create/update/delete/void/login/backup/dll).
 */

declare(strict_types=1);

final class AuditLogger
{
    public static function log(
        string $module,
        string $action,
        string $description = '',
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        $user = Auth::user();
        $stmt = Database::connection()->prepare(
            'INSERT INTO audit_logs (user_id, branch_id, module, action, description, ip_address, user_agent, old_data, new_data)
             VALUES (:user_id, :branch_id, :module, :action, :description, :ip, :ua, :old_data, :new_data)'
        );
        $stmt->execute([
            'user_id'     => $user['id'] ?? null,
            'branch_id'   => $user['branch_id'] ?? null,
            'module'      => $module,
            'action'      => $action,
            'description' => $description,
            'ip'          => $_SERVER['REMOTE_ADDR'] ?? null,
            'ua'          => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'old_data'    => $oldData !== null ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
            'new_data'    => $newData !== null ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}
