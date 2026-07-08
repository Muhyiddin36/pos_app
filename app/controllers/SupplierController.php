<?php

declare(strict_types=1);

final class SupplierController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('suppliers.manage');
        $this->view('suppliers/index', [
            'title'     => 'Supplier',
            'suppliers' => SupplierModel::all('name'),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('suppliers.manage');
        $this->view('suppliers/form', ['title' => 'Tambah Supplier', 'item' => null]);
    }

    public function store(): void
    {
        $this->requirePermission('suppliers.manage');
        $this->requireCsrf();
        $this->saveFromRequest(null);
    }

    public function edit(string $id): void
    {
        $this->requirePermission('suppliers.manage');
        $item = SupplierModel::find((int) $id);
        if ($item === null) {
            $this->withError('Supplier tidak ditemukan.', 'suppliers/index');
        }
        $this->view('suppliers/form', ['title' => 'Ubah Supplier', 'item' => $item]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('suppliers.manage');
        $this->requireCsrf();
        $this->saveFromRequest((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requirePermission('suppliers.manage');
        $this->requireCsrf();
        $item = SupplierModel::find((int) $id);
        if ($item === null) {
            $this->withError('Supplier tidak ditemukan.', 'suppliers/index');
        }
        SupplierModel::delete((int) $id);
        AuditLogger::log('suppliers', 'delete', 'Hapus supplier: ' . $item['name'], $item);
        $this->withSuccess('Supplier berhasil dihapus.', 'suppliers/index');
    }

    private function saveFromRequest(?int $id): void
    {
        $data = [
            'name'           => $this->post('name'),
            'contact_person' => $this->post('contact_person'),
            'phone'          => $this->post('phone'),
            'address'        => $this->post('address'),
        ];

        $validator = Validator::make($data, ['name' => 'required|max:150']);
        if ($validator->fails()) {
            $_SESSION['_errors'] = $validator->errors();
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', $id === null ? 'suppliers/create' : 'suppliers/edit/' . $id);
        }

        if ($id === null) {
            SupplierModel::create($data);
            AuditLogger::log('suppliers', 'create', 'Tambah supplier: ' . $data['name']);
        } else {
            SupplierModel::update($id, $data);
            AuditLogger::log('suppliers', 'update', 'Ubah supplier: ' . $data['name']);
        }

        $this->withSuccess('Supplier berhasil disimpan.', 'suppliers/index');
    }
}
