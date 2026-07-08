<?php

declare(strict_types=1);

final class PawnModel extends Model
{
    protected static string $table = 'pawns';
    protected static array $fillable = [
        'branch_id', 'customer_id', 'pawn_no', 'item_name', 'item_description',
        'estimated_value', 'loan_amount', 'interest_rate', 'pawn_date', 'due_date',
        'status', 'redeemed_at', 'created_by',
    ];

    public static function listForBranch(?int $branchId, string $status = ''): array
    {
        $sql = 'SELECT pw.*, c.name AS customer_name, c.phone AS customer_phone
                FROM pawns pw INNER JOIN customers c ON c.id = pw.customer_id
                WHERE 1=1';
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND pw.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        if ($status !== '') {
            $sql .= ' AND pw.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY pw.pawn_date DESC, pw.id DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function withCustomer(int $id): ?array
    {
        $sql = 'SELECT pw.*, c.name AS customer_name, c.phone AS customer_phone, c.address AS customer_address,
                       c.id_card_number, u.full_name AS created_by_name
                FROM pawns pw
                INNER JOIN customers c ON c.id = pw.customer_id
                LEFT JOIN users u ON u.id = pw.created_by
                WHERE pw.id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function nextPawnNumber(): string
    {
        return generate_doc_number('GDI');
    }

    public static function countByStatus(?int $branchId, string $status): int
    {
        $sql = 'SELECT COUNT(*) c FROM pawns WHERE status = :status';
        $params = ['status' => $status];
        if ($branchId !== null) {
            $sql .= ' AND branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['c'];
    }

    public static function totalOutstanding(?int $branchId): float
    {
        $sql = "SELECT COALESCE(SUM(loan_amount),0) t FROM pawns WHERE status IN ('active','overdue')";
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetch()['t'];
    }

    public static function markStatus(int $id, string $status): void
    {
        $data = ['status' => $status];
        if ($status === 'redeemed') {
            $data['redeemed_at'] = date('Y-m-d H:i:s');
        }
        self::update($id, $data);
    }
}
