<?php

declare(strict_types=1);

final class ServiceOrderModel extends Model
{
    protected static string $table = 'service_orders';
    protected static array $fillable = [
        'branch_id', 'customer_id', 'service_no', 'device_type', 'device_imei',
        'issue_description', 'accessories_note', 'estimated_cost', 'final_cost',
        'down_payment', 'paid_amount', 'status', 'technician_notes',
        'received_date', 'estimated_finish_date', 'completed_at', 'picked_up_at', 'created_by',
    ];

    public static function listForBranch(?int $branchId, string $status = ''): array
    {
        $sql = 'SELECT s.*, c.name AS customer_name, c.phone AS customer_phone
                FROM service_orders s INNER JOIN customers c ON c.id = s.customer_id
                WHERE 1=1';
        $params = [];
        if ($branchId !== null) {
            $sql .= ' AND s.branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        if ($status !== '') {
            $sql .= ' AND s.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY s.received_date DESC, s.id DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function withCustomer(int $id): ?array
    {
        $sql = 'SELECT s.*, c.name AS customer_name, c.phone AS customer_phone, c.address AS customer_address,
                       u.full_name AS created_by_name
                FROM service_orders s
                INNER JOIN customers c ON c.id = s.customer_id
                LEFT JOIN users u ON u.id = s.created_by
                WHERE s.id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function nextServiceNumber(): string
    {
        return generate_doc_number('SVC');
    }

    public static function countByStatus(?int $branchId, string $status): int
    {
        $sql = 'SELECT COUNT(*) c FROM service_orders WHERE status = :status';
        $params = ['status' => $status];
        if ($branchId !== null) {
            $sql .= ' AND branch_id = :branch_id';
            $params['branch_id'] = $branchId;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['c'];
    }

    /** Terima servis baru + catat log status awal + catat DP jika ada, dalam satu transaksi. */
    public static function createWithIntake(array $header, float $downPayment, string $paymentMethod, int $userId): int
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $header['down_payment'] = $downPayment;
            $header['paid_amount'] = $downPayment;
            $id = self::create($header);

            ServiceStatusLogModel::create([
                'service_order_id' => $id,
                'status'            => 'received',
                'note'              => 'Servis diterima',
                'created_by'        => $userId,
            ]);

            if ($downPayment > 0) {
                ServicePaymentModel::create([
                    'service_order_id' => $id,
                    'payment_date'      => date('Y-m-d'),
                    'type'              => 'down_payment',
                    'amount'            => $downPayment,
                    'payment_method'    => $paymentMethod,
                    'note'              => 'Uang muka saat penerimaan',
                    'created_by'        => $userId,
                ]);
            }

            $db->commit();
            return $id;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /** Ubah status servis (in_progress/waiting_parts/completed/cancelled) + catat log. */
    public static function changeStatus(int $id, string $status, string $note, int $userId): void
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $data = ['status' => $status];
            if ($status === 'completed') {
                $data['completed_at'] = date('Y-m-d H:i:s');
            }
            self::update($id, $data);
            ServiceStatusLogModel::create([
                'service_order_id' => $id,
                'status'            => $status,
                'note'              => $note,
                'created_by'        => $userId,
            ]);
            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /** Serah terima ke pelanggan: catat pembayaran akhir, tandai diambil. */
    public static function pickup(int $id, float $finalCost, float $paymentAmount, string $paymentMethod, string $note, int $userId): void
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $order = self::find($id);
            if ($order === null) {
                throw new RuntimeException('Data servis tidak ditemukan.');
            }

            self::update($id, [
                'final_cost'   => $finalCost,
                'paid_amount'  => (float) $order['paid_amount'] + $paymentAmount,
                'status'       => 'picked_up',
                'picked_up_at' => date('Y-m-d H:i:s'),
            ]);

            if ($paymentAmount > 0) {
                ServicePaymentModel::create([
                    'service_order_id' => $id,
                    'payment_date'      => date('Y-m-d'),
                    'type'              => 'final_payment',
                    'amount'            => $paymentAmount,
                    'payment_method'    => $paymentMethod,
                    'note'              => $note ?: 'Pelunasan saat pengambilan',
                    'created_by'        => $userId,
                ]);
            }

            ServiceStatusLogModel::create([
                'service_order_id' => $id,
                'status'            => 'picked_up',
                'note'              => 'Barang diambil pelanggan',
                'created_by'        => $userId,
            ]);

            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
