<?php

declare(strict_types=1);

final class CustomerController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('customers.manage');
        $this->view('customers/index', [
            'title'     => 'Pelanggan',
            'customers' => CustomerModel::listForBranch($this->currentBranchId()),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('customers.manage');
        $this->view('customers/form', ['title' => 'Tambah Pelanggan', 'item' => null]);
    }

    public function store(): void
    {
        $this->requirePermission('customers.manage');
        $this->requireCsrf();
        $this->saveFromRequest(null);
    }

    public function edit(string $id): void
    {
        $this->requirePermission('customers.manage');
        $item = CustomerModel::find((int) $id);
        if ($item === null) {
            $this->withError('Pelanggan tidak ditemukan.', 'customers/index');
        }
        $this->view('customers/form', ['title' => 'Ubah Pelanggan', 'item' => $item]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('customers.manage');
        $this->requireCsrf();
        $this->saveFromRequest((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requirePermission('customers.manage');
        $this->requireCsrf();
        $item = CustomerModel::find((int) $id);
        if ($item === null) {
            $this->withError('Pelanggan tidak ditemukan.', 'customers/index');
        }
        CustomerModel::delete((int) $id);
        AuditLogger::log('customers', 'delete', 'Hapus pelanggan: ' . $item['name'], $item);
        $this->withSuccess('Pelanggan berhasil dihapus.', 'customers/index');
    }

    private function saveFromRequest(?int $id): void
    {
        $data = [
            'branch_id'      => $this->currentBranchId(),
            'name'           => $this->post('name'),
            'phone'          => $this->post('phone'),
            'address'        => $this->post('address'),
            'id_card_number' => $this->post('id_card_number'),
            'note'           => $this->post('note'),
        ];

        $validator = Validator::make($data, ['name' => 'required|max:150']);
        if ($validator->fails()) {
            $_SESSION['_errors'] = $validator->errors();
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', $id === null ? 'customers/create' : 'customers/edit/' . $id);
        }

        if ($id === null) {
            CustomerModel::create($data);
            AuditLogger::log('customers', 'create', 'Tambah pelanggan: ' . $data['name']);
        } else {
            unset($data['branch_id']); // jangan pindahkan kepemilikan cabang saat edit
            CustomerModel::update($id, $data);
            AuditLogger::log('customers', 'update', 'Ubah pelanggan: ' . $data['name']);
        }

        $this->withSuccess('Pelanggan berhasil disimpan.', 'customers/index');
    }
}
