<?php

declare(strict_types=1);

final class ProductModel extends Model
{
    protected static string $table = 'products';
    protected static bool $softDelete = true;
    protected static array $fillable = [
        'branch_id', 'category_id', 'sku', 'barcode', 'name', 'unit',
        'purchase_price', 'sale_price', 'stock_qty', 'min_stock', 'is_active',
    ];

    public static function listForBranch(int $branchId): array
    {
        $sql = 'SELECT p.*, c.name AS category_name FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.branch_id = :branch_id AND p.deleted_at IS NULL
                ORDER BY p.name';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['branch_id' => $branchId]);
        return $stmt->fetchAll();
    }

    public static function search(int $branchId, string $keyword, int $limit = 15): array
    {
        $sql = 'SELECT * FROM products
                WHERE branch_id = :branch_id AND is_active = 1 AND deleted_at IS NULL
                AND (name LIKE :kw1 OR sku LIKE :kw2 OR barcode = :exact)
                ORDER BY name LIMIT ' . $limit;
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            'branch_id' => $branchId,
            'kw1'       => '%' . $keyword . '%',
            'kw2'       => '%' . $keyword . '%',
            'exact'     => $keyword,
        ]);
        return $stmt->fetchAll();
    }

    public static function skuExists(int $branchId, string $sku, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) c FROM products WHERE branch_id = :b AND sku = :sku AND deleted_at IS NULL';
        $params = ['b' => $branchId, 'sku' => $sku];
        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $exceptId;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['c'] > 0;
    }

    public static function lowStock(?int $branchId): array
    {
        $sql = 'SELECT p.*, b.name AS branch_name FROM products p
                LEFT JOIN branches b ON b.id = p.branch_id
                WHERE p.deleted_at IS NULL AND p.is_active = 1 AND p.stock_qty <= p.min_stock';
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND p.branch_id = :b';
            $params['b'] = $branchId;
        }
        $sql .= ' ORDER BY p.stock_qty ASC LIMIT 20';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Tambah/kurangi stok secara atomik (mencegah race condition di request bersamaan). */
    public static function adjustStock(int $productId, int $delta): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE products SET stock_qty = stock_qty + :delta WHERE id = :id'
        );
        $stmt->execute(['delta' => $delta, 'id' => $productId]);
    }
}
