<?php

declare(strict_types=1);

final class ServiceStatusLogModel extends Model
{
    protected static string $table = 'service_status_logs';
    protected static array $fillable = ['service_order_id', 'status', 'note', 'created_by'];

    public static function forOrder(int $serviceOrderId): array
    {
        $sql = 'SELECT l.*, u.full_name AS created_by_name FROM service_status_logs l
                LEFT JOIN users u ON u.id = l.created_by
                WHERE l.service_order_id = :id ORDER BY l.created_at ASC, l.id ASC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $serviceOrderId]);
        return $stmt->fetchAll();
    }
}
