<?php

declare(strict_types=1);

final class LoanModel extends Model
{
    protected static string $table = 'loans';
    protected static array $fillable = [
        'branch_id', 'customer_id', 'loan_no', 'principal_amount', 'interest_rate',
        'term_months', 'loan_date', 'due_date', 'status', 'created_by',
    ];

    public static function listForBranch(?int $branchId, string $status = ''): array
    {
        $sql = 'SELECT l.*, c.name AS customer_name, c.phone AS customer_phone
                FROM loans l INNER JOIN customers c ON c.id = l.customer_id
                WHERE 1=1';
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND l.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        if ($status !== '') {
            $sql .= ' AND l.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY l.loan_date DESC, l.id DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function withCustomer(int $id): ?array
    {
        $sql = 'SELECT l.*, c.name AS customer_name, c.phone AS customer_phone, c.address AS customer_address,
                       c.id_card_number, u.full_name AS created_by_name
                FROM loans l
                INNER JOIN customers c ON c.id = l.customer_id
                LEFT JOIN users u ON u.id = l.created_by
                WHERE l.id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function nextLoanNumber(): string
    {
        return generate_doc_number('PJM');
    }

    public static function countByStatus(?int $branchId, string $status): int
    {
        $sql = 'SELECT COUNT(*) c FROM loans WHERE status = :status';
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
        $sql = "SELECT COALESCE(SUM(li.total_amount - li.paid_amount),0) t
                FROM loan_installments li INNER JOIN loans l ON l.id = li.loan_id
                WHERE l.status IN ('active','overdue')";
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND l.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetch()['t'];
    }

    /**
     * Buat pinjaman + jadwal angsuran flat (pokok rata + bunga tetap per bulan) dalam satu transaksi.
     */
    public static function createWithInstallments(array $header): int
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $loanId = self::create($header);

            $termMonths = (int) $header['term_months'];
            $principal = (float) $header['principal_amount'];
            $rate = (float) $header['interest_rate'] / 100;

            $principalPerMonth = round($principal / $termMonths, 2);
            $interestPerMonth = round($principal * $rate, 2);
            $loanDate = new DateTime($header['loan_date']);

            $stmt = $db->prepare(
                'INSERT INTO loan_installments
                 (loan_id, installment_no, due_date, principal_amount, interest_amount, total_amount, status)
                 VALUES (:loan_id, :no, :due_date, :principal, :interest, :total, "unpaid")'
            );

            for ($i = 1; $i <= $termMonths; $i++) {
                $due = (clone $loanDate)->modify("+{$i} month");
                $principalAmt = $i === $termMonths
                    ? round($principal - $principalPerMonth * ($termMonths - 1), 2)
                    : $principalPerMonth;
                $stmt->execute([
                    'loan_id'   => $loanId,
                    'no'        => $i,
                    'due_date'  => $due->format('Y-m-d'),
                    'principal' => $principalAmt,
                    'interest'  => $interestPerMonth,
                    'total'     => $principalAmt + $interestPerMonth,
                ]);
            }

            $db->commit();
            return $loanId;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
