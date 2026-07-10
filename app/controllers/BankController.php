<?php

declare(strict_types=1);

final class BankController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('bank.view');
        $from = $this->get('from');
        $to = $this->get('to');

        $this->view('bank/index', [
            'title'        => 'Transfer / Setor Tunai',
            'transactions' => BankTransactionModel::listForBranch($this->currentBranchId(), $from, $to),
            'banks'        => BankModel::activeList(),
            'defaultFee'   => SettingModel::get('bank_default_admin_fee', '5000'),
            'from'         => $from,
            'to'           => $to,
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('bank.create');
        $this->requireCsrf();

        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Super Admin tidak dapat bertransaksi langsung. Gunakan akun cabang.', 'bank/index');
        }

        $bankId = (int) $this->post('bank_id');
        $bank = BankModel::find($bankId);
        $amount = (float) $this->post('amount');
        $adminFee = (float) $this->post('admin_fee');

        if ($bank === null || $amount <= 0) {
            $this->withError('Bank dan nominal wajib diisi dengan benar.', 'bank/index');
        }

        $customerIdRaw = $this->post('customer_id');
        $data = [
            'branch_id'        => $branchId,
            'bank_id'          => $bankId,
            'trx_no'           => BankTransactionModel::nextTrxNumber(),
            'transaction_type' => $this->post('transaction_type') ?: 'transfer',
            'account_number'   => $this->post('account_number'),
            'account_name'     => $this->post('account_name'),
            'customer_id'      => $customerIdRaw === '' ? null : (int) $customerIdRaw,
            'customer_phone'   => $this->post('customer_phone'),
            'amount'           => $amount,
            'cost_fee'         => (float) $this->post('cost_fee'),
            'admin_fee'        => $adminFee,
            'profit_amount'    => $adminFee - (float) $this->post('cost_fee'),
            'status'           => 'success',
            'note'             => $this->post('note'),
            'created_by'       => Auth::id(),
        ];

        $id = BankTransactionModel::create($data);
        AuditLogger::log('bank', 'create', 'Transaksi bank: ' . $data['trx_no'] . ' (' . $data['transaction_type'] . ')', null, $data);

        redirect('bank/print/' . $id);
    }

    public function print(string $id): void
    {
        $this->requirePermission('bank.view');
        $trx = BankTransactionModel::withBank((int) $id);
        if ($trx === null) {
            $this->withError('Transaksi tidak ditemukan.', 'bank/index');
        }
        $this->view('bank/print', ['title' => 'Struk ' . $trx['trx_no'], 'trx' => $trx], 'print');
    }

    public function void(string $id): void
    {
        $this->requirePermission('bank.void');
        $this->requireCsrf();

        $trx = BankTransactionModel::find((int) $id);
        if ($trx === null || $trx['status'] === 'void') {
            $this->withError('Transaksi tidak ditemukan atau sudah dibatalkan.', 'bank/index');
        }

        $reason = $this->post('reason');
        if ($reason === '') {
            $this->withError('Alasan pembatalan wajib diisi.', 'bank/index');
        }

        BankTransactionModel::void((int) $id, Auth::id(), $reason);
        AuditLogger::log('bank', 'void', 'Pembatalan transaksi bank #' . $id . ': ' . $reason);
        $this->withSuccess('Transaksi berhasil dibatalkan.', 'bank/index');
    }

    // -------------------- Kelola Daftar Bank --------------------

    public function banks(): void
    {
        $this->requirePermission('bank.manage');
        $this->view('bank/banks', [
            'title' => 'Daftar Bank',
            'banks' => BankModel::all('name'),
        ]);
    }

    public function bankCreate(): void
    {
        $this->requirePermission('bank.manage');
        $this->view('bank/bank_form', ['title' => 'Tambah Bank', 'item' => null]);
    }

    public function bankStore(): void
    {
        $this->requirePermission('bank.manage');
        $this->requireCsrf();
        $this->saveBank(null);
    }

    public function bankEdit(string $id): void
    {
        $this->requirePermission('bank.manage');
        $item = BankModel::find((int) $id);
        if ($item === null) {
            $this->withError('Bank tidak ditemukan.', 'bank/banks');
        }
        $this->view('bank/bank_form', ['title' => 'Ubah Bank', 'item' => $item]);
    }

    public function bankUpdate(string $id): void
    {
        $this->requirePermission('bank.manage');
        $this->requireCsrf();
        $this->saveBank((int) $id);
    }

    public function bankDestroy(string $id): void
    {
        $this->requirePermission('bank.manage');
        $this->requireCsrf();
        $item = BankModel::find((int) $id);
        if ($item === null) {
            $this->withError('Bank tidak ditemukan.', 'bank/banks');
        }
        BankModel::delete((int) $id);
        AuditLogger::log('bank', 'delete', 'Hapus bank: ' . $item['name'], $item);
        $this->withSuccess('Bank berhasil dihapus.', 'bank/banks');
    }

    private function saveBank(?int $id): void
    {
        $data = [
            'code'      => strtoupper($this->post('code')),
            'name'      => $this->post('name'),
            'is_active' => $this->postRaw('is_active') ? 1 : 0,
        ];

        $errors = Validator::make($data, ['code' => 'required|max:20', 'name' => 'required|max:100'])->errors();
        if (!isset($errors['code']) && BankModel::codeExists($data['code'], $id)) {
            $errors['code'] = 'Kode bank sudah digunakan.';
        }

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', $id === null ? 'bank/bankCreate' : 'bank/bankEdit/' . $id);
        }

        if ($id === null) {
            BankModel::create($data);
            AuditLogger::log('bank', 'create', 'Tambah bank: ' . $data['name']);
        } else {
            BankModel::update($id, $data);
            AuditLogger::log('bank', 'update', 'Ubah bank: ' . $data['name']);
        }

        $this->withSuccess('Data bank berhasil disimpan.', 'bank/banks');
    }
}
