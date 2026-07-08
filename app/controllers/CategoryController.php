<?php

declare(strict_types=1);

final class CategoryController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('categories.manage');
        $this->view('categories/index', [
            'title'      => 'Kategori Produk',
            'categories' => CategoryModel::all('name'),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('categories.manage');
        $this->view('categories/form', ['title' => 'Tambah Kategori', 'item' => null]);
    }

    public function store(): void
    {
        $this->requirePermission('categories.manage');
        $this->requireCsrf();
        $this->saveFromRequest(null);
    }

    public function edit(string $id): void
    {
        $this->requirePermission('categories.manage');
        $item = CategoryModel::find((int) $id);
        if ($item === null) {
            $this->withError('Kategori tidak ditemukan.', 'categories/index');
        }
        $this->view('categories/form', ['title' => 'Ubah Kategori', 'item' => $item]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('categories.manage');
        $this->requireCsrf();
        $this->saveFromRequest((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requirePermission('categories.manage');
        $this->requireCsrf();
        $item = CategoryModel::find((int) $id);
        if ($item === null) {
            $this->withError('Kategori tidak ditemukan.', 'categories/index');
        }
        CategoryModel::delete((int) $id);
        AuditLogger::log('categories', 'delete', 'Hapus kategori: ' . $item['name'], $item);
        $this->withSuccess('Kategori berhasil dihapus.', 'categories/index');
    }

    private function saveFromRequest(?int $id): void
    {
        $data = [
            'name'        => $this->post('name'),
            'description' => $this->post('description'),
        ];

        $validator = Validator::make($data, ['name' => 'required|max:100']);
        if ($validator->fails()) {
            $_SESSION['_errors'] = $validator->errors();
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', $id === null ? 'categories/create' : 'categories/edit/' . $id);
        }

        if ($id === null) {
            CategoryModel::create($data);
            AuditLogger::log('categories', 'create', 'Tambah kategori: ' . $data['name']);
        } else {
            CategoryModel::update($id, $data);
            AuditLogger::log('categories', 'update', 'Ubah kategori: ' . $data['name']);
        }

        $this->withSuccess('Kategori berhasil disimpan.', 'categories/index');
    }
}
