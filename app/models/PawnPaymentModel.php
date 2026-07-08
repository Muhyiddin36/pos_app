<?php

declare(strict_types=1);

final class PawnPaymentModel extends Model
{
    protected static string $table = 'pawn_payments';
    protected static array $fillable = ['pawn_id', 'payment_date', 'type', 'amount', 'note', 'created_by'];

    public static function forPawn(int $pawnId): array
    {
        $sql = 'SELECT pp.*, u.full_name AS created_by_name FROM pawn_payments pp
                LEFT JOIN users u ON u.id = pp.created_by
                WHERE pp.pawn_id = :id ORDER BY pp.payment_date DESC, pp.id DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $pawnId]);
        return $stmt->fetchAll();
    }
}
