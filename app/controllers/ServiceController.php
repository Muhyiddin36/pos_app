<?php

declare(strict_types=1);

final class ServiceController extends Controller
{
    private const STATUSES = ['received', 'in_progress', 'waiting_parts', 'completed', 'picked_up', 'cancelled'];

    public function index(): void
    {
        $this->requirePermission('service.view');
        $status = $this->get('status');
        $this->view('service/index', [
            'title'    => 'Servis HP',
            'orders'   => ServiceOrderModel::listForBranch($this->currentBranchId(), $status),
            'status'   => $status,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('service.create');
        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Super Admin tidak dapat bertransaksi langsung. Gunakan akun cabang.', 'service/index');
        }
        $this->view('service/form', [
            'title'     => 'Terima Servis Baru',
            'customers' => CustomerModel::listForBranch($branchId),
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('service.create');
        $this->requireCsrf();

        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Cabang tidak valid.', 'service/index');
        }

        $customerId = (int) $this->post('customer_id');
        $data = [
            'branch_id'             => $branchId,
            'customer_id'           => $customerId,
            'service_no'            => ServiceOrderModel::nextServiceNumber(),
            'device_type'           => $this->post('device_type'),
            'device_imei'           => $this->post('device_imei'),
            'issue_description'     => $this->post('issue_description'),
            'accessories_note'      => $this->post('accessories_note'),
            'estimated_cost'        => (float) $this->post('estimated_cost'),
            'final_cost'            => 0,
            'status'                => 'received',
            'received_date'         => $this->post('received_date') ?: date('Y-m-d'),
            'estimated_finish_date' => $this->post('estimated_finish_date') ?: null,
            'created_by'            => Auth::id(),
        ];

        $errors = Validator::make($data, [
            'device_type'       => 'required|max:100',
            'issue_description' => 'required|max:500',
        ])->errors();
        if ($customerId <= 0) {
            $errors['customer_id'] = 'Pelanggan wajib dipilih.';
        }

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', 'service/create');
        }

        $downPayment = (float) $this->post('down_payment');
        $paymentMethod = $this->post('payment_method') ?: 'cash';

        $id = ServiceOrderModel::createWithIntake($data, $downPayment, $paymentMethod, Auth::id());
        AuditLogger::log('service', 'create', 'Servis baru: ' . $data['service_no'], null, $data);
        $this->withSuccess('Servis berhasil diterima dan tercatat.', 'service/show/' . $id);
    }

    public function show(string $id): void
    {
        $this->requirePermission('service.view');
        $order = ServiceOrderModel::withCustomer((int) $id);
        if ($order === null) {
            $this->withError('Data servis tidak ditemukan.', 'service/index');
        }
        $this->view('service/show', [
            'title'    => 'Detail Servis ' . $order['service_no'],
            'order'    => $order,
            'logs'     => ServiceStatusLogModel::forOrder((int) $id),
            'payments' => ServicePaymentModel::forOrder((int) $id),
            'statuses' => self::STATUSES,
        ]);
    }

    public function changeStatus(string $id): void
    {
        $this->requirePermission('service.manage');
        $this->requireCsrf();

        $order = ServiceOrderModel::find((int) $id);
        if ($order === null) {
            $this->withError('Data servis tidak ditemukan.', 'service/index');
        }

        $status = $this->post('status');
        if (!in_array($status, self::STATUSES, true) || $status === 'picked_up') {
            $this->withError('Status tidak valid.', 'service/show/' . $id);
        }

        ServiceOrderModel::changeStatus((int) $id, $status, $this->post('note'), Auth::id());
        AuditLogger::log('service', 'update', 'Ubah status servis ' . $order['service_no'] . ' menjadi ' . $status);
        $this->withSuccess('Status servis berhasil diperbarui.', 'service/show/' . $id);
    }

    public function pickup(string $id): void
    {
        $this->requirePermission('service.manage');
        $this->requireCsrf();

        $order = ServiceOrderModel::find((int) $id);
        if ($order === null || $order['status'] === 'picked_up' || $order['status'] === 'cancelled') {
            $this->withError('Servis tidak dapat diserahkan pada status ini.', 'service/show/' . $id);
        }

        $finalCost = (float) $this->post('final_cost');
        $paymentAmount = (float) $this->post('payment_amount');
        $method = $this->post('payment_method') ?: 'cash';

        ServiceOrderModel::pickup((int) $id, $finalCost, $paymentAmount, $method, $this->post('note'), Auth::id());
        AuditLogger::log('service', 'update', 'Serah terima servis ' . $order['service_no']);
        $this->withSuccess('Servis berhasil diserahkan ke pelanggan.', 'service/show/' . $id);
    }

    public function print(string $id): void
    {
        $this->requirePermission('service.view');
        $order = ServiceOrderModel::withCustomer((int) $id);
        if ($order === null) {
            $this->withError('Data servis tidak ditemukan.', 'service/index');
        }
        $this->view('service/print', ['title' => 'Bukti Servis ' . $order['service_no'], 'order' => $order], 'print');
    }
}
