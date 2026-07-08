<?php

declare(strict_types=1);

final class UserController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('users.manage');
        $this->view('users/index', [
            'title' => 'Pengguna',
            'users' => UserModel::allWithRoleBranch(),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('users.manage');
        $this->view('users/form', [
            'title'    => 'Tambah Pengguna',
            'item'     => null,
            'roles'    => RoleModel::all('name'),
            'branches' => BranchModel::activeList(),
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('users.manage');
        $this->requireCsrf();
        $this->saveFromRequest(null);
    }

    public function edit(string $id): void
    {
        $this->requirePermission('users.manage');
        $item = UserModel::find((int) $id);
        if ($item === null) {
            $this->withError('Pengguna tidak ditemukan.', 'users/index');
        }
        $this->view('users/form', [
            'title'    => 'Ubah Pengguna',
            'item'     => $item,
            'roles'    => RoleModel::all('name'),
            'branches' => BranchModel::activeList(),
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('users.manage');
        $this->requireCsrf();
        $this->saveFromRequest((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requirePermission('users.manage');
        $this->requireCsrf();

        if ((int) $id === Auth::id()) {
            $this->withError('Anda tidak dapat menghapus akun Anda sendiri.', 'users/index');
        }

        $user = UserModel::find((int) $id);
        if ($user === null) {
            $this->withError('Pengguna tidak ditemukan.', 'users/index');
        }
        UserModel::delete((int) $id);
        AuditLogger::log('users', 'delete', 'Hapus pengguna: ' . $user['username'], $user);
        $this->withSuccess('Pengguna berhasil dihapus.', 'users/index');
    }

    private function saveFromRequest(?int $id): void
    {
        $branchIdRaw = $this->post('branch_id');
        $data = [
            'username'  => $this->post('username'),
            'full_name' => $this->post('full_name'),
            'email'     => $this->post('email'),
            'phone'     => $this->post('phone'),
            'role_id'   => (int) $this->post('role_id'),
            'branch_id' => $branchIdRaw === '' ? null : (int) $branchIdRaw,
            'is_active' => $this->postRaw('is_active') ? 1 : 0,
        ];

        $rules = [
            'username'  => 'required|max:50',
            'full_name' => 'required|max:100',
            'email'     => 'max:100',
            'role_id'   => 'required|integer',
        ];
        $validator = Validator::make($data, $rules);
        $errors = $validator->errors();

        if (!isset($errors['username']) && UserModel::usernameExists($data['username'], $id)) {
            $errors['username'] = 'Username sudah digunakan.';
        }

        $password = (string) $this->postRaw('password', '');
        if ($id === null && $password === '') {
            $errors['password'] = 'Password wajib diisi untuk pengguna baru.';
        } elseif ($password !== '' && strlen($password) < 6) {
            $errors['password'] = 'Password minimal 6 karakter.';
        }

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', $id === null ? 'users/create' : 'users/edit/' . $id);
        }

        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($id === null) {
            UserModel::create($data);
            AuditLogger::log('users', 'create', 'Tambah pengguna: ' . $data['username'], null, ['username' => $data['username'], 'role_id' => $data['role_id']]);
        } else {
            $old = UserModel::find($id);
            UserModel::update($id, $data);
            AuditLogger::log('users', 'update', 'Ubah pengguna: ' . $data['username'], $old, $data);
        }

        $this->withSuccess('Data pengguna berhasil disimpan.', 'users/index');
    }
}
