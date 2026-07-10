<?php

declare(strict_types=1);

final class ServicePaymentModel extends Model
{
    protected static string $table = 'service_payments';
    protected static array $fillable = ['service_order_id', 'payment_date', 'type', 'amount', 'payment_method', 'note', 'created_by'];

    public static function forOrder(int $serviceOrderId): array
    {
        $sql = 'SELECT p.*, u.full_name AS created_by_name FROM service_payments p
                LEFT JOIN users u ON u.id = p.created_by
                WHERE p.service_order_id = :id ORDER BY p.payment_date DESC, p.id DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $serviceOrderId]);
        return $stmt->fetchAll();
    }
}
