<?php

declare(strict_types=1);

final class SaleModel extends Model
{
    protected static string $table = 'sales';
    protected static array $fillable = [
        'branch_id', 'customer_id', 'invoice_no', 'sale_date', 'subtotal',
        'discount_amount', 'total_amount', 'paid_amount', 'change_amount',
        'payment_method', 'status', 'void_reason', 'voided_by', 'voided_at', 'created_by',
    ];

    public static function listForBranch(?int $branchId, string $from = '', string $to = ''): array
    {
        $sql = 'SELECT s.*, c.name AS customer_name, u.full_name AS created_by_name
                FROM sales s
                LEFT JOIN customers c ON c.id = s.customer_id
                LEFT JOIN users u ON u.id = s.created_by
                WHERE 1=1';
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND s.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        if ($from !== '') {
            $sql .= ' AND s.sale_date >= :from';
            $params['from'] = $from . ' 00:00:00';
        }
        if ($to !== '') {
            $sql .= ' AND s.sale_date <= :to';
            $params['to'] = $to . ' 23:59:59';
        }
        $sql .= ' ORDER BY s.sale_date DESC, s.id DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function items(int $saleId): array
    {
        $sql = 'SELECT si.*, p.name AS product_name, p.unit FROM sale_items si
                INNER JOIN products p ON p.id = si.product_id
                WHERE si.sale_id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $saleId]);
        return $stmt->fetchAll();
    }

    public static function withCustomer(int $id): ?array
    {
        $sql = 'SELECT s.*, c.name AS customer_name, c.phone AS customer_phone, u.full_name AS created_by_name
                FROM sales s
                LEFT JOIN customers c ON c.id = s.customer_id
                LEFT JOIN users u ON u.id = s.created_by
                WHERE s.id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Simpan transaksi penjualan + item, kurangi stok, catat pergerakan stok - dalam satu transaction DB.
     *
     * @param array<int,array{product_id:int,qty:int,price:float,cost_price:float,subtotal:float}> $items
     */
    public static function createWithItems(array $header, array $items): int
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $saleId = self::create($header);

            $itemStmt = $db->prepare(
                'INSERT INTO sale_items (sale_id, product_id, qty, price, cost_price, subtotal)
                 VALUES (:sale_id, :product_id, :qty, :price, :cost_price, :subtotal)'
            );
            $moveStmt = $db->prepare(
                'INSERT INTO stock_movements (branch_id, product_id, type, qty, reference_type, reference_id, note, created_by)
                 VALUES (:branch_id, :product_id, "out", :qty, "sale", :ref_id, :note, :created_by)'
            );
            $updateStock = $db->prepare('UPDATE products SET stock_qty = stock_qty - :qty WHERE id = :id');
            $lockStmt = $db->prepare('SELECT stock_qty FROM products WHERE id = :id FOR UPDATE');

            foreach ($items as $item) {
                $lockStmt->execute(['id' => $item['product_id']]);
                $current = $lockStmt->fetch();
                if ($current === false || (int) $current['stock_qty'] < $item['qty']) {
                    throw new RuntimeException('Stok tidak mencukupi untuk salah satu produk.');
                }

                $itemStmt->execute([
                    'sale_id'    => $saleId,
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'price'      => $item['price'],
                    'cost_price' => $item['cost_price'],
                    'subtotal'   => $item['subtotal'],
                ]);
                $updateStock->execute(['qty' => $item['qty'], 'id' => $item['product_id']]);
                $moveStmt->execute([
                    'branch_id'  => $header['branch_id'],
                    'product_id' => $item['product_id'],
                    'qty'        => -$item['qty'],
                    'ref_id'     => $saleId,
                    'note'       => 'Penjualan ' . $header['invoice_no'],
                    'created_by' => $header['created_by'],
                ]);
            }

            $db->commit();
            return $saleId;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /** Batalkan transaksi + kembalikan stok. */
    public static function void(int $saleId, int $voidedBy, string $reason): void
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $sale = self::find($saleId);
            if ($sale === null || $sale['status'] === 'void') {
                throw new RuntimeException('Transaksi tidak ditemukan atau sudah dibatalkan.');
            }

            $items = self::items($saleId);
            $updateStock = $db->prepare('UPDATE products SET stock_qty = stock_qty + :qty WHERE id = :id');
            $moveStmt = $db->prepare(
                'INSERT INTO stock_movements (branch_id, product_id, type, qty, reference_type, reference_id, note, created_by)
                 VALUES (:branch_id, :product_id, "in", :qty, "sale_void", :ref_id, :note, :created_by)'
            );
            foreach ($items as $item) {
                $updateStock->execute(['qty' => $item['qty'], 'id' => $item['product_id']]);
                $moveStmt->execute([
                    'branch_id'  => $sale['branch_id'],
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'ref_id'     => $saleId,
                    'note'       => 'Pembatalan ' . $sale['invoice_no'],
                    'created_by' => $voidedBy,
                ]);
            }

            $stmt = $db->prepare(
                "UPDATE sales SET status = 'void', void_reason = :reason, voided_by = :voided_by, voided_at = NOW() WHERE id = :id"
            );
            $stmt->execute(['reason' => $reason, 'voided_by' => $voidedBy, 'id' => $saleId]);

            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function nextInvoiceNumber(): string
    {
        return generate_doc_number('INV');
    }

    /** Ringkasan penjualan hari ini: jumlah transaksi, total omzet, total laba (harga jual - modal). */
    public static function todaySummary(?int $branchId): array
    {
        $sql = "SELECT COUNT(*) AS trx_count, COALESCE(SUM(total_amount),0) AS total_omzet
                FROM sales WHERE status = 'completed' AND DATE(sale_date) = CURDATE()";
        $profitSql = "SELECT COALESCE(SUM(si.subtotal - si.cost_price * si.qty),0) AS total_profit
                FROM sale_items si INNER JOIN sales s ON s.id = si.sale_id
                WHERE s.status = 'completed' AND DATE(s.sale_date) = CURDATE()";
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND branch_id = :branch_id';
            $profitSql .= ' AND s.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        $profitStmt = Database::connection()->prepare($profitSql);
        $profitStmt->execute($params);
        $profitRow = $profitStmt->fetch();

        return [
            'trx_count'    => (int) ($row['trx_count'] ?? 0),
            'total_omzet'  => (float) ($row['total_omzet'] ?? 0),
            'total_profit' => (float) ($profitRow['total_profit'] ?? 0),
        ];
    }
}
