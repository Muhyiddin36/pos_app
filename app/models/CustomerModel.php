<?php

declare(strict_types=1);

final class CustomerModel extends Model
{
    protected static string $table = 'customers';
    protected static bool $softDelete = true;
    protected static array $fillable = ['branch_id', 'name', 'phone', 'address', 'id_card_number', 'note'];

    public static function search(string $keyword, ?int $branchId, int $limit = 20): array
    {
        $sql = 'SELECT * FROM customers WHERE deleted_at IS NULL AND (name LIKE :kw1 OR phone LIKE :kw2)';
        $params = ['kw1' => '%' . $keyword . '%', 'kw2' => '%' . $keyword . '%'];
        if ($branchId !== null) {
            $sql .= ' AND (branch_id = :branch_id OR branch_id IS NULL)';
            $params['branch_id'] = $branchId;
        }
        $sql .= ' ORDER BY name LIMIT ' . $limit;
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function listForBranch(?int $branchId): array
    {
        if ($branchId === null) {
            return self::all('name');
        }
        $sql = 'SELECT * FROM customers WHERE deleted_at IS NULL AND (branch_id = :branch_id OR branch_id IS NULL) ORDER BY name';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['branch_id' => $branchId]);
        return $stmt->fetchAll();
    }
}
