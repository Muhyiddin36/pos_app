<?php

declare(strict_types=1);

final class AuditLogModel extends Model
{
    protected static string $table = 'audit_logs';

    public static function search(array $filters, int $limit = 100): array
    {
        $sql = 'SELECT al.*, u.full_name AS user_name, b.name AS branch_name
                FROM audit_logs al
                LEFT JOIN users u ON u.id = al.user_id
                LEFT JOIN branches b ON b.id = al.branch_id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['module'])) {
            $sql .= ' AND al.module = :module';
            $params['module'] = $filters['module'];
        }
        if (!empty($filters['branch_id'])) {
            $sql .= ' AND al.branch_id = :branch_id';
            $params['branch_id'] = $filters['branch_id'];
        }
        if (!empty($filters['from'])) {
            $sql .= ' AND al.created_at >= :from';
            $params['from'] = $filters['from'] . ' 00:00:00';
        }
        if (!empty($filters['to'])) {
            $sql .= ' AND al.created_at <= :to';
            $params['to'] = $filters['to'] . ' 23:59:59';
        }

        $sql .= ' ORDER BY al.created_at DESC LIMIT ' . $limit;
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
