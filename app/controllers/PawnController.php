<?php

declare(strict_types=1);

final class PawnController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('pawn.view');
        $status = $this->get('status');
        $this->view('pawn/index', [
            'title' => 'Gadai Barang',
            'pawns' => PawnModel::listForBranch($this->currentBranchId(), $status),
            'status' => $status,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('pawn.create');
        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Super Admin tidak dapat bertransaksi langsung. Gunakan akun cabang.', 'pawn/index');
        }
        $this->view('pawn/form', [
            'title'         => 'Gadai Baru',
            'customers'     => CustomerModel::listForBranch($branchId),
            'defaultRate'   => SettingModel::get('pawn_default_interest_rate', '5'),
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('pawn.create');
        $this->requireCsrf();

        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Cabang tidak valid.', 'pawn/index');
        }

        $customerId = (int) $this->post('customer_id');
        $data = [
            'branch_id'        => $branchId,
            'customer_id'      => $customerId,
            'pawn_no'          => PawnModel::nextPawnNumber(),
            'item_name'        => $this->post('item_name'),
            'item_description' => $this->post('item_description'),
            'estimated_value'  => (float) $this->post('estimated_value'),
            'loan_amount'      => (float) $this->post('loan_amount'),
            'interest_rate'    => (float) $this->post('interest_rate'),
            'pawn_date'        => $this->post('pawn_date') ?: date('Y-m-d'),
            'due_date'         => $this->post('due_date'),
            'status'           => 'active',
            'created_by'       => Auth::id(),
        ];

        $errors = Validator::make($data, [
            'item_name'   => 'required|max:150',
            'loan_amount' => 'required|numeric',
            'due_date'    => 'required',
        ])->errors();
        if ($customerId <= 0) {
            $errors['customer_id'] = 'Pelanggan wajib dipilih.';
        }

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', 'pawn/create');
        }

        $id = PawnModel::create($data);
        AuditLogger::log('pawn', 'create', 'Gadai baru: ' . $data['pawn_no'], null, $data);
        $this->withSuccess('Data gadai berhasil disimpan.', 'pawn/show/' . $id);
    }

    public function show(string $id): void
    {
        $this->requirePermission('pawn.view');
        $pawn = PawnModel::withCustomer((int) $id);
        if ($pawn === null) {
            $this->withError('Data gadai tidak ditemukan.', 'pawn/index');
        }
        $this->view('pawn/show', [
            'title'    => 'Detail Gadai ' . $pawn['pawn_no'],
            'pawn'     => $pawn,
            'payments' => PawnPaymentModel::forPawn((int) $id),
        ]);
    }

    /** Bayar bunga / perpanjangan. */
    public function pay(string $id): void
    {
        $this->requirePermission('pawn.manage');
        $this->requireCsrf();

        $pawn = PawnModel::find((int) $id);
        if ($pawn === null || $pawn['status'] !== 'active') {
            $this->withError('Gadai tidak ditemukan atau sudah tidak aktif.', 'pawn/index');
        }

        $amount = (float) $this->post('amount');
        $type = $this->post('type') ?: 'interest';
        if ($amount <= 0) {
            $this->withError('Jumlah pembayaran tidak valid.', 'pawn/show/' . $id);
        }

        PawnPaymentModel::create([
            'pawn_id'      => (int) $id,
            'payment_date' => date('Y-m-d'),
            'type'         => $type,
            'amount'       => $amount,
            'note'         => $this->post('note'),
            'created_by'   => Auth::id(),
        ]);

        if ($type === 'extension') {
            $newDue = $this->post('new_due_date');
            if ($newDue !== '') {
                PawnModel::update((int) $id, ['due_date' => $newDue]);
            }
        }

        AuditLogger::log('pawn', 'update', 'Pembayaran gadai ' . $pawn['pawn_no'] . ': ' . rupiah($amount));
        $this->withSuccess('Pembayaran berhasil dicatat.', 'pawn/show/' . $id);
    }

    /** Tebus / lunasi gadai. */
    public function redeem(string $id): void
    {
        $this->requirePermission('pawn.manage');
        $this->requireCsrf();

        $pawn = PawnModel::find((int) $id);
        if ($pawn === null || $pawn['status'] !== 'active') {
            $this->withError('Gadai tidak ditemukan atau sudah tidak aktif.', 'pawn/index');
        }

        $amount = (float) $this->post('amount');
        PawnPaymentModel::create([
            'pawn_id'      => (int) $id,
            'payment_date' => date('Y-m-d'),
            'type'         => 'redemption',
            'amount'       => $amount,
            'note'         => $this->post('note') ?: 'Penebusan barang gadai',
            'created_by'   => Auth::id(),
        ]);
        PawnModel::markStatus((int) $id, 'redeemed');

        AuditLogger::log('pawn', 'update', 'Penebusan gadai ' . $pawn['pawn_no']);
        $this->withSuccess('Barang gadai berhasil ditebus.', 'pawn/show/' . $id);
    }

    public function print(string $id): void
    {
        $this->requirePermission('pawn.view');
        $pawn = PawnModel::withCustomer((int) $id);
        if ($pawn === null) {
            $this->withError('Data gadai tidak ditemukan.', 'pawn/index');
        }
        $this->view('pawn/print', ['title' => 'Surat Gadai ' . $pawn['pawn_no'], 'pawn' => $pawn], 'print');
    }
}
