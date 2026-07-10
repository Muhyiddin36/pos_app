<?php

declare(strict_types=1);

final class BankTransactionModel extends Model
{
    protected static string $table = 'bank_transactions';
    protected static array $fillable = [
        'branch_id', 'bank_id', 'trx_no', 'transaction_type', 'account_number', 'account_name',
        'customer_id', 'customer_phone', 'amount', 'cost_fee', 'admin_fee', 'profit_amount',
        'status', 'void_reason', 'voided_by', 'voided_at', 'note', 'created_by',
    ];

    public static function listForBranch(?int $branchId, string $from = '', string $to = ''): array
    {
        $sql = 'SELECT t.*, b.name AS bank_name, u.full_name AS created_by_name
                FROM bank_transactions t
                INNER JOIN banks b ON b.id = t.bank_id
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

    public static function withBank(int $id): ?array
    {
        $sql = 'SELECT t.*, b.name AS bank_name, u.full_name AS created_by_name
                FROM bank_transactions t
                INNER JOIN banks b ON b.id = t.bank_id
                LEFT JOIN users u ON u.id = t.created_by
                WHERE t.id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function nextTrxNumber(): string
    {
        return generate_doc_number('BNK');
    }

    public static function void(int $id, int $voidedBy, string $reason): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE bank_transactions SET status = 'void', void_reason = :reason, voided_by = :voided_by, voided_at = NOW()
             WHERE id = :id AND status != 'void'"
        );
        $stmt->execute(['reason' => $reason, 'voided_by' => $voidedBy, 'id' => $id]);
    }

    public static function todaySummary(?int $branchId): array
    {
        $sql = "SELECT COUNT(*) AS trx_count, COALESCE(SUM(amount),0) AS total_amount,
                       COALESCE(SUM(profit_amount),0) AS total_profit
                FROM bank_transactions WHERE status = 'success' AND DATE(created_at) = CURDATE()";
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
            'total_amount' => (float) ($row['total_amount'] ?? 0),
            'total_profit' => (float) ($row['total_profit'] ?? 0),
        ];
    }
}
