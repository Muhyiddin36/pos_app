<?php

declare(strict_types=1);

final class StockMovementModel extends Model
{
    protected static string $table = 'stock_movements';
    protected static array $fillable = [
        'branch_id', 'product_id', 'type', 'qty', 'reference_type',
        'reference_id', 'note', 'created_by',
    ];

    public static function historyForProduct(int $productId, int $limit = 50): array
    {
        $sql = 'SELECT sm.*, u.full_name AS created_by_name FROM stock_movements sm
                LEFT JOIN users u ON u.id = sm.created_by
                WHERE sm.product_id = :pid ORDER BY sm.created_at DESC LIMIT ' . $limit;
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll();
    }
}
