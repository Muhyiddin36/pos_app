<?php

declare(strict_types=1);

final class LoanInstallmentModel extends Model
{
    protected static string $table = 'loan_installments';
    protected static array $fillable = ['loan_id', 'installment_no', 'due_date', 'principal_amount', 'interest_amount', 'total_amount', 'paid_amount', 'paid_date', 'status'];

    public static function forLoan(int $loanId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM loan_installments WHERE loan_id = :id ORDER BY installment_no');
        $stmt->execute(['id' => $loanId]);
        return $stmt->fetchAll();
    }

    public static function outstandingTotal(int $loanId): float
    {
        $stmt = Database::connection()->prepare(
            'SELECT COALESCE(SUM(total_amount - paid_amount),0) AS o FROM loan_installments WHERE loan_id = :id'
        );
        $stmt->execute(['id' => $loanId]);
        return (float) $stmt->fetch()['o'];
    }

    /** Terapkan pembayaran ke satu cicilan (partial/paid) dan kembalikan sisa. */
    public static function applyPayment(int $installmentId, float $amount): void
    {
        $installment = self::find($installmentId);
        if ($installment === null) {
            throw new RuntimeException('Angsuran tidak ditemukan.');
        }
        $newPaid = (float) $installment['paid_amount'] + $amount;
        $status = 'partial';
        if ($newPaid >= (float) $installment['total_amount']) {
            $status = 'paid';
            $newPaid = (float) $installment['total_amount'];
        }
        self::update($installmentId, [
            'paid_amount' => $newPaid,
            'paid_date'   => $status === 'paid' ? date('Y-m-d') : $installment['paid_date'],
            'status'      => $status,
        ]);
    }
}
