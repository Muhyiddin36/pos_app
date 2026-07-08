<?php

declare(strict_types=1);

final class PurchaseModel extends Model
{
    protected static string $table = 'purchases';
    protected static bool $softDelete = true;
    protected static array $fillable = ['branch_id', 'supplier_id', 'invoice_no', 'purchase_date', 'total_amount', 'note', 'created_by'];

    public static function listForBranch(?int $branchId, string $from = '', string $to = ''): array
    {
        $sql = 'SELECT p.*, s.name AS supplier_name, u.full_name AS created_by_name
                FROM purchases p
                LEFT JOIN suppliers s ON s.id = p.supplier_id
                LEFT JOIN users u ON u.id = p.created_by
                WHERE p.deleted_at IS NULL';
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND p.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        if ($from !== '') {
            $sql .= ' AND p.purchase_date >= :from';
            $params['from'] = $from;
        }
        if ($to !== '') {
            $sql .= ' AND p.purchase_date <= :to';
            $params['to'] = $to;
        }
        $sql .= ' ORDER BY p.purchase_date DESC, p.id DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function items(int $purchaseId): array
    {
        $sql = 'SELECT pi.*, p.name AS product_name, p.unit FROM purchase_items pi
                INNER JOIN products p ON p.id = pi.product_id
                WHERE pi.purchase_id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $purchaseId]);
        return $stmt->fetchAll();
    }

    /**
     * Simpan pembelian beserta item-nya dalam satu transaksi database,
     * lalu update stok produk dan catat pergerakan stok.
     *
     * @param array<int,array{product_id:int,qty:int,price:float}> $items
     */
    public static function createWithItems(array $header, array $items): int
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $purchaseId = self::create($header);

            $itemStmt = $db->prepare(
                'INSERT INTO purchase_items (purchase_id, product_id, qty, price, subtotal)
                 VALUES (:purchase_id, :product_id, :qty, :price, :subtotal)'
            );
            $moveStmt = $db->prepare(
                'INSERT INTO stock_movements (branch_id, product_id, type, qty, reference_type, reference_id, note, created_by)
                 VALUES (:branch_id, :product_id, "in", :qty, "purchase", :ref_id, :note, :created_by)'
            );
            $updateStock = $db->prepare('UPDATE products SET stock_qty = stock_qty + :qty WHERE id = :id');

            foreach ($items as $item) {
                $subtotal = $item['qty'] * $item['price'];
                $itemStmt->execute([
                    'purchase_id' => $purchaseId,
                    'product_id'  => $item['product_id'],
                    'qty'         => $item['qty'],
                    'price'       => $item['price'],
                    'subtotal'    => $subtotal,
                ]);
                $updateStock->execute(['qty' => $item['qty'], 'id' => $item['product_id']]);
                $moveStmt->execute([
                    'branch_id'  => $header['branch_id'],
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'ref_id'     => $purchaseId,
                    'note'       => 'Pembelian ' . $header['invoice_no'],
                    'created_by' => $header['created_by'],
                ]);
            }

            $db->commit();
            return $purchaseId;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
