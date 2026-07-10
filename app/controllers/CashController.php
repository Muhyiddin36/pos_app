<?php

declare(strict_types=1);

final class CashController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('cash.view');
        $branchId = $this->resolveBranchId();
        $date = $this->get('record_date') ?: date('Y-m-d');
        if ($date !== date('Y-m-d') && !Auth::can('cash.correct')) {
            $date = date('Y-m-d');
        }
        $from = $this->get('from');
        $to = $this->get('to');

        $this->view('cash/index', [
            'title'      => 'Saldo Kas Harian',
            'records'    => $branchId !== null ? CashBalanceRecordModel::forBranchAndDate($branchId, $date) : [],
            'history'    => CashBalanceRecordModel::history($branchId, $from, $to),
            'date'       => $date,
            'from'       => $from,
            'to'         => $to,
            'branches'   => Auth::isSuperAdmin() ? BranchModel::activeList() : [],
            'selectedBranchId' => $branchId,
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('cash.record');
        $this->requireCsrf();

        $branchId = Auth::isSuperAdmin() ? (int) $this->post('branch_id') : $this->currentBranchId();
        if ($branchId === null || $branchId <= 0) {
            $this->withError('Cabang tidak valid. Pilih cabang terlebih dahulu.', 'cash/index');
        }

        $date = $this->post('record_date') ?: date('Y-m-d');
        if ($date !== date('Y-m-d') && !Auth::can('cash.correct')) {
            $this->withError('Anda hanya dapat mencatat saldo kas untuk tanggal hari ini. Hubungi Admin Cabang atau Super Admin untuk memperbaiki tanggal yang sudah lewat.', 'cash/index');
        }

        $note = $this->post('note');
        $balances = (array) $this->postRaw('closing_balance', []);

        $validSources = array_column(CashSourceModel::activeForBranch($branchId), 'id');
        $saved = 0;

        foreach ($balances as $sourceId => $amount) {
            $sourceId = (int) $sourceId;
            if (!in_array($sourceId, $validSources, true) || $amount === '') {
                continue;
            }
            CashBalanceRecordModel::upsert($branchId, $sourceId, $date, (float) $amount, $note, Auth::id());
            $saved++;
        }

        if ($saved === 0) {
            $this->withError('Tidak ada saldo yang disimpan. Isi minimal satu sumber dana.', 'cash/index?record_date=' . $date);
        }

        $action = $date === date('Y-m-d') ? 'record' : 'correct';
        AuditLogger::log('cash', $action, "Catat saldo kas harian tanggal {$date} ({$saved} sumber dana)");
        $redirect = 'cash/index?record_date=' . $date;
        if (Auth::isSuperAdmin()) {
            $redirect .= '&branch_id=' . $branchId;
        }
        $this->withSuccess('Saldo kas berhasil disimpan.', $redirect);
    }

    // -------------------- Kelola Sumber Dana (khusus Super Admin) --------------------

    public function sources(): void
    {
        $this->requirePermission('cash.manage');
        $this->view('cash/sources', [
            'title'   => 'Sumber Dana Saldo Kas',
            'sources' => CashSourceModel::allWithBranch(),
        ]);
    }

    public function sourceCreate(): void
    {
        $this->requirePermission('cash.manage');
        $this->view('cash/source_form', [
            'title'    => 'Tambah Sumber Dana',
            'item'     => null,
            'branches' => BranchModel::activeList(),
        ]);
    }

    public function sourceStore(): void
    {
        $this->requirePermission('cash.manage');
        $this->requireCsrf();
        $this->saveSource(null);
    }

    public function sourceEdit(string $id): void
    {
        $this->requirePermission('cash.manage');
        $item = CashSourceModel::find((int) $id);
        if ($item === null) {
            $this->withError('Sumber dana tidak ditemukan.', 'cash/sources');
        }
        $this->view('cash/source_form', [
            'title'    => 'Ubah Sumber Dana',
            'item'     => $item,
            'branches' => BranchModel::activeList(),
        ]);
    }

    public function sourceUpdate(string $id): void
    {
        $this->requirePermission('cash.manage');
        $this->requireCsrf();
        $this->saveSource((int) $id);
    }

    public function sourceDestroy(string $id): void
    {
        $this->requirePermission('cash.manage');
        $this->requireCsrf();
        $item = CashSourceModel::find((int) $id);
        if ($item === null) {
            $this->withError('Sumber dana tidak ditemukan.', 'cash/sources');
        }
        CashSourceModel::delete((int) $id);
        AuditLogger::log('cash', 'delete', 'Hapus sumber dana: ' . $item['name'], $item);
        $this->withSuccess('Sumber dana berhasil dihapus.', 'cash/sources');
    }

    private function resolveBranchId(): ?int
    {
        if (!Auth::isSuperAdmin()) {
            return $this->currentBranchId();
        }
        $requested = $this->get('branch_id');
        return $requested !== '' ? (int) $requested : null;
    }

    private function saveSource(?int $id): void
    {
        $data = [
            'branch_id'       => (int) $this->post('branch_id'),
            'name'            => $this->post('name'),
            'type'            => $this->post('type') ?: 'cash',
            'opening_balance' => (float) $this->post('opening_balance'),
            'is_active'       => $this->postRaw('is_active') ? 1 : 0,
        ];

        $errors = Validator::make($data, [
            'name' => 'required|max:100',
            'type' => 'required|in:cash,bank,ewallet,other',
        ])->errors();
        if ($data['branch_id'] <= 0) {
            $errors['branch_id'] = 'Cabang wajib dipilih.';
        }

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', $id === null ? 'cash/sourceCreate' : 'cash/sourceEdit/' . $id);
        }

        if ($id === null) {
            $data['created_by'] = Auth::id();
            CashSourceModel::create($data);
            AuditLogger::log('cash', 'create', 'Tambah sumber dana: ' . $data['name'], null, $data);
        } else {
            unset($data['branch_id']); // cabang tidak boleh berubah setelah dibuat
            $old = CashSourceModel::find($id);
            CashSourceModel::update($id, $data);
            AuditLogger::log('cash', 'update', 'Ubah sumber dana: ' . $data['name'], $old, $data);
        }

        $this->withSuccess('Sumber dana berhasil disimpan.', 'cash/sources');
    }
}
