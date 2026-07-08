<?php

declare(strict_types=1);

final class PulsaTransactionModel extends Model
{
    protected static string $table = 'pulsa_transactions';
    protected static array $fillable = [
        'branch_id', 'pulsa_product_id', 'trx_no', 'customer_phone',
        'cost_price', 'sale_price', 'profit_amount', 'status', 'note', 'created_by',
    ];

    public static function listForBranch(?int $branchId, string $from = '', string $to = ''): array
    {
        $sql = 'SELECT t.*, pp.name AS product_name, pp.provider, pp.category, u.full_name AS created_by_name
                FROM pulsa_transactions t
                INNER JOIN pulsa_products pp ON pp.id = t.pulsa_product_id
                LEFT JOIN users u ON u.id = t.created_by
                WHERE 1=1';
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND t.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        if ($from !== '') {
            $sql .= ' AND t.created_at >= :from';
            $params['from'] = $from . ' 00:00:00';
        }
        if ($to !== '') {
            $sql .= ' AND t.created_at <= :to';
            $params['to'] = $to . ' 23:59:59';
        }
        $sql .= ' ORDER BY t.created_at DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function nextTrxNumber(): string
    {
        return generate_doc_number('PLS');
    }

    public static function todaySummary(?int $branchId): array
    {
        $sql = "SELECT COUNT(*) AS trx_count, COALESCE(SUM(sale_price),0) AS total_omzet,
                       COALESCE(SUM(profit_amount),0) AS total_profit
                FROM pulsa_transactions WHERE status = 'success' AND DATE(created_at) = CURDATE()";
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return [
            'trx_count'    => (int) ($row['trx_count'] ?? 0),
            'total_omzet'  => (float) ($row['total_omzet'] ?? 0),
            'total_profit' => (float) ($row['total_profit'] ?? 0),
        ];
    }
}
