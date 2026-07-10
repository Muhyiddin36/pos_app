<?php

declare(strict_types=1);

final class CashSourceModel extends Model
{
    protected static string $table = 'cash_sources';
    protected static bool $softDelete = true;
    protected static array $fillable = ['branch_id', 'name', 'type', 'opening_balance', 'is_active', 'created_by'];

    public static function activeForBranch(int $branchId): array
    {
        $sql = 'SELECT * FROM cash_sources WHERE branch_id = :branch_id AND is_active = 1 AND deleted_at IS NULL ORDER BY name';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['branch_id' => $branchId]);
        return $stmt->fetchAll();
    }

    public static function allWithBranch(): array
    {
        $sql = 'SELECT cs.*, b.name AS branch_name FROM cash_sources cs
                INNER JOIN branches b ON b.id = cs.branch_id
                WHERE cs.deleted_at IS NULL ORDER BY b.name, cs.name';
        return Database::connection()->query($sql)->fetchAll();
    }
}
