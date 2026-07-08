<?php

declare(strict_types=1);

final class LoanController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('loan.view');
        $status = $this->get('status');
        $this->view('loan/index', [
            'title' => 'Pinjam Uang',
            'loans' => LoanModel::listForBranch($this->currentBranchId(), $status),
            'status' => $status,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('loan.create');
        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Super Admin tidak dapat bertransaksi langsung. Gunakan akun cabang.', 'loan/index');
        }
        $this->view('loan/form', [
            'title'       => 'Pinjaman Baru',
            'customers'   => CustomerModel::listForBranch($branchId),
            'defaultRate' => SettingModel::get('loan_default_interest_rate', '5'),
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('loan.create');
        $this->requireCsrf();

        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Cabang tidak valid.', 'loan/index');
        }

        $customerId = (int) $this->post('customer_id');
        $termMonths = (int) $this->post('term_months');
        $data = [
            'branch_id'        => $branchId,
            'customer_id'      => $customerId,
            'loan_no'          => LoanModel::nextLoanNumber(),
            'principal_amount' => (float) $this->post('principal_amount'),
            'interest_rate'    => (float) $this->post('interest_rate'),
            'term_months'      => $termMonths,
            'loan_date'        => $this->post('loan_date') ?: date('Y-m-d'),
            'due_date'         => '',
            'status'           => 'active',
            'created_by'       => Auth::id(),
        ];

        $errors = Validator::make($data, [
            'principal_amount' => 'required|numeric',
            'term_months'      => 'required|integer|min_value:1',
        ])->errors();
        if ($customerId <= 0) {
            $errors['customer_id'] = 'Pelanggan wajib dipilih.';
        }

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', 'loan/create');
        }

        $dueDate = new DateTime($data['loan_date']);
        $dueDate->modify('+' . $termMonths . ' month');
        $data['due_date'] = $dueDate->format('Y-m-d');

        $id = LoanModel::createWithInstallments($data);
        AuditLogger::log('loan', 'create', 'Pinjaman baru: ' . $data['loan_no'], null, $data);
        $this->withSuccess('Data pinjaman berhasil disimpan beserta jadwal angsuran.', 'loan/show/' . $id);
    }

    public function show(string $id): void
    {
        $this->requirePermission('loan.view');
        $loan = LoanModel::withCustomer((int) $id);
        if ($loan === null) {
            $this->withError('Pinjaman tidak ditemukan.', 'loan/index');
        }
        $this->view('loan/show', [
            'title'        => 'Detail Pinjaman ' . $loan['loan_no'],
            'loan'         => $loan,
            'installments' => LoanInstallmentModel::forLoan((int) $id),
            'payments'     => LoanPaymentModel::forLoan((int) $id),
        ]);
    }

    public function pay(string $id): void
    {
        $this->requirePermission('loan.manage');
        $this->requireCsrf();

        $loan = LoanModel::find((int) $id);
        if ($loan === null) {
            $this->withError('Pinjaman tidak ditemukan.', 'loan/index');
        }

        $installmentId = (int) $this->post('installment_id');
        $amount = (float) $this->post('amount');
        $installment = LoanInstallmentModel::find($installmentId);
        if ($installment === null || (int) $installment['loan_id'] !== (int) $id || $amount <= 0) {
            $this->withError('Angsuran tidak valid.', 'loan/show/' . $id);
        }

        LoanPaymentModel::pay((int) $id, $installmentId, $amount, $this->post('note'), Auth::id());
        AuditLogger::log('loan', 'update', 'Pembayaran angsuran pinjaman ' . $loan['loan_no'] . ': ' . rupiah($amount));

        $this->withSuccess('Pembayaran angsuran berhasil dicatat.', 'loan/show/' . $id);
    }

    public function print(string $id): void
    {
        $this->requirePermission('loan.view');
        $loan = LoanModel::withCustomer((int) $id);
        if ($loan === null) {
            $this->withError('Pinjaman tidak ditemukan.', 'loan/index');
        }
        $this->view('loan/print', [
            'title'        => 'Surat Pinjaman ' . $loan['loan_no'],
            'loan'         => $loan,
            'installments' => LoanInstallmentModel::forLoan((int) $id),
        ], 'print');
    }
}
