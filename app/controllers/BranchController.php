<?php

declare(strict_types=1);

final class BranchController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('branches.manage');
        $this->view('branches/index', [
            'title'    => 'Cabang',
            'branches' => BranchModel::all('name'),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('branches.manage');
        $this->view('branches/form', ['title' => 'Tambah Cabang', 'item' => null]);
    }

    public function store(): void
    {
        $this->requirePermission('branches.manage');
        $this->requireCsrf();
        $this->saveFromRequest(null);
    }

    public function edit(string $id): void
    {
        $this->requirePermission('branches.manage');
        $item = BranchModel::find((int) $id);
        if ($item === null) {
            $this->withError('Cabang tidak ditemukan.', 'branches/index');
        }
        $this->view('branches/form', ['title' => 'Ubah Cabang', 'item' => $item]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('branches.manage');
        $this->requireCsrf();
        $this->saveFromRequest((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requirePermission('branches.manage');
        $this->requireCsrf();
        $branch = BranchModel::find((int) $id);
        if ($branch === null) {
            $this->withError('Cabang tidak ditemukan.', 'branches/index');
        }
        BranchModel::delete((int) $id);
        AuditLogger::log('branches', 'delete', 'Hapus cabang: ' . $branch['name'], $branch);
        $this->withSuccess('Cabang berhasil dihapus.', 'branches/index');
    }

    private function saveFromRequest(?int $id): void
    {
        $data = [
            'code'      => $this->post('code'),
            'name'      => $this->post('name'),
            'address'   => $this->post('address'),
            'phone'     => $this->post('phone'),
            'is_active' => $this->postRaw('is_active') ? 1 : 0,
        ];

        $validator = Validator::make($data, [
            'code' => 'required|max:20',
            'name' => 'required|max:100',
        ]);
        $errors = $validator->errors();
        if (!isset($errors['code']) && BranchModel::codeExists($data['code'], $id)) {
            $errors['code'] = 'Kode cabang sudah digunakan.';
        }

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', $id === null ? 'branches/create' : 'branches/edit/' . $id);
        }

        if ($id === null) {
            $newId = BranchModel::create($data);
            AuditLogger::log('branches', 'create', 'Tambah cabang: ' . $data['name'], null, $data);
        } else {
            $old = BranchModel::find($id);
            BranchModel::update($id, $data);
            AuditLogger::log('branches', 'update', 'Ubah cabang: ' . $data['name'], $old, $data);
        }

        $this->withSuccess('Data cabang berhasil disimpan.', 'branches/index');
    }
}
