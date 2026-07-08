<?php

declare(strict_types=1);

final class LoanPaymentModel extends Model
{
    protected static string $table = 'loan_payments';
    protected static array $fillable = ['loan_id', 'installment_id', 'payment_date', 'amount', 'note', 'created_by'];

    public static function forLoan(int $loanId): array
    {
        $sql = 'SELECT lp.*, u.full_name AS created_by_name FROM loan_payments lp
                LEFT JOIN users u ON u.id = lp.created_by
                WHERE lp.loan_id = :id ORDER BY lp.payment_date DESC, lp.id DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $loanId]);
        return $stmt->fetchAll();
    }

    /** Catat pembayaran + update angsuran terkait dalam satu transaksi DB. */
    public static function pay(int $loanId, int $installmentId, float $amount, string $note, int $userId): int
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $paymentId = self::create([
                'loan_id'        => $loanId,
                'installment_id' => $installmentId,
                'payment_date'   => date('Y-m-d'),
                'amount'         => $amount,
                'note'           => $note,
                'created_by'     => $userId,
            ]);
            LoanInstallmentModel::applyPayment($installmentId, $amount);

            $outstanding = LoanInstallmentModel::outstandingTotal($loanId);
            if ($outstanding <= 0.0) {
                LoanModel::update($loanId, ['status' => 'paid_off']);
            }

            $db->commit();
            return $paymentId;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
