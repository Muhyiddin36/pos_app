<?php

declare(strict_types=1);

final class RoleController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('roles.manage');
        $this->view('roles/index', [
            'title' => 'Role & Hak Akses',
            'roles' => RoleModel::allWithPermissionCount(),
        ]);
    }

    public function edit(string $id): void
    {
        $this->requirePermission('roles.manage');
        $role = RoleModel::find((int) $id);
        if ($role === null) {
            $this->withError('Role tidak ditemukan.', 'roles/index');
        }
        $assigned = PermissionModel::codesForRole((int) $id);
        $this->view('roles/form', [
            'title'          => 'Hak Akses: ' . $role['name'],
            'role'           => $role,
            'permissionGroups' => PermissionModel::allGroupedByModule(),
            'assignedCodes'  => $assigned,
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('roles.manage');
        $this->requireCsrf();

        $role = RoleModel::find((int) $id);
        if ($role === null) {
            $this->withError('Role tidak ditemukan.', 'roles/index');
        }

        if ($role['slug'] === 'super_admin') {
            $this->withError('Hak akses Super Admin tidak dapat diubah (selalu penuh).', 'roles/index');
        }

        $permissionIds = array_map('intval', (array) $this->postRaw('permissions', []));
        PermissionModel::syncRolePermissions((int) $id, $permissionIds);
        AuditLogger::log('roles', 'update', 'Ubah hak akses role: ' . $role['name']);

        $this->withSuccess('Hak akses role berhasil diperbarui.', 'roles/index');
    }
}
