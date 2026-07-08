<?php

declare(strict_types=1);

final class ProductController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('products.view');
        $branchId = $this->resolveBranchId();

        $this->view('products/index', [
            'title'       => 'Produk',
            'products'    => $branchId !== null ? ProductModel::listForBranch($branchId) : [],
            'branches'    => Auth::isSuperAdmin() ? BranchModel::activeList() : [],
            'selectedBranchId' => $branchId,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('products.manage');
        $this->view('products/form', [
            'title'      => 'Tambah Produk',
            'item'       => null,
            'categories' => CategoryModel::all('name'),
            'branches'   => Auth::isSuperAdmin() ? BranchModel::activeList() : [],
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('products.manage');
        $this->requireCsrf();
        $this->saveFromRequest(null);
    }

    public function edit(string $id): void
    {
        $this->requirePermission('products.manage');
        $item = ProductModel::find((int) $id);
        if ($item === null || !$this->canAccessBranch((int) $item['branch_id'])) {
            $this->withError('Produk tidak ditemukan.', 'products/index');
        }
        $this->view('products/form', [
            'title'      => 'Ubah Produk',
            'item'       => $item,
            'categories' => CategoryModel::all('name'),
            'branches'   => Auth::isSuperAdmin() ? BranchModel::activeList() : [],
            'stockHistory' => StockMovementModel::historyForProduct((int) $id, 20),
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('products.manage');
        $this->requireCsrf();
        $this->saveFromRequest((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requirePermission('products.manage');
        $this->requireCsrf();
        $item = ProductModel::find((int) $id);
        if ($item === null || !$this->canAccessBranch((int) $item['branch_id'])) {
            $this->withError('Produk tidak ditemukan.', 'products/index');
        }
        ProductModel::delete((int) $id);
        AuditLogger::log('products', 'delete', 'Hapus produk: ' . $item['name'], $item);
        $this->withSuccess('Produk berhasil dihapus.', 'products/index');
    }

    /** Penyesuaian stok manual (opname/koreksi), tercatat di stock_movements + audit log. */
    public function adjustStock(string $id): void
    {
        $this->requirePermission('products.manage');
        $this->requireCsrf();

        $product = ProductModel::find((int) $id);
        if ($product === null || !$this->canAccessBranch((int) $product['branch_id'])) {
            $this->withError('Produk tidak ditemukan.', 'products/index');
        }

        $qty = (int) $this->post('qty');
        $note = $this->post('note') ?: 'Penyesuaian stok manual';
        if ($qty === 0) {
            $this->withError('Jumlah penyesuaian tidak boleh nol.', 'products/edit/' . $id);
        }

        ProductModel::adjustStock((int) $id, $qty);
        StockMovementModel::create([
            'branch_id'      => $product['branch_id'],
            'product_id'     => (int) $id,
            'type'           => 'adjustment',
            'qty'            => $qty,
            'reference_type' => 'adjustment',
            'reference_id'   => null,
            'note'           => $note,
            'created_by'     => Auth::id(),
        ]);
        AuditLogger::log('products', 'update', 'Penyesuaian stok produk: ' . $product['name'] . ' (' . ($qty > 0 ? '+' : '') . $qty . ')');

        $this->withSuccess('Stok berhasil disesuaikan.', 'products/edit/' . $id);
    }

    private function resolveBranchId(): ?int
    {
        if (!Auth::isSuperAdmin()) {
            return $this->currentBranchId();
        }
        $requested = $this->get('branch_id');
        return $requested !== '' ? (int) $requested : null;
    }

    private function canAccessBranch(int $branchId): bool
    {
        return Auth::isSuperAdmin() || $this->currentBranchId() === $branchId;
    }

    private function saveFromRequest(?int $id): void
    {
        $branchId = Auth::isSuperAdmin() ? (int) $this->post('branch_id') : $this->currentBranchId();

        $categoryIdRaw = $this->post('category_id');
        $data = [
            'branch_id'      => $branchId,
            'category_id'    => $categoryIdRaw === '' ? null : (int) $categoryIdRaw,
            'sku'            => $this->post('sku'),
            'barcode'        => $this->post('barcode'),
            'name'           => $this->post('name'),
            'unit'           => $this->post('unit') ?: 'pcs',
            'purchase_price' => (float) $this->post('purchase_price'),
            'sale_price'     => (float) $this->post('sale_price'),
            'min_stock'      => (int) $this->post('min_stock'),
            'is_active'      => $this->postRaw('is_active') ? 1 : 0,
        ];

        $errors = Validator::make($data, [
            'sku'            => 'required|max:50',
            'name'           => 'required|max:150',
            'purchase_price' => 'required|numeric',
            'sale_price'     => 'required|numeric',
        ])->errors();

        if ($branchId === null || $branchId === 0) {
            $errors['branch_id'] = 'Cabang wajib dipilih.';
        } elseif (!isset($errors['sku']) && ProductModel::skuExists($branchId, $data['sku'], $id)) {
            $errors['sku'] = 'SKU sudah digunakan pada cabang ini.';
        }

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', $id === null ? 'products/create' : 'products/edit/' . $id);
        }

        if ($id === null) {
            $data['stock_qty'] = (int) $this->post('stock_qty');
            $newId = ProductModel::create($data);
            AuditLogger::log('products', 'create', 'Tambah produk: ' . $data['name'], null, $data);
            if ($data['stock_qty'] > 0) {
                StockMovementModel::create([
                    'branch_id'  => $branchId,
                    'product_id' => $newId,
                    'type'       => 'in',
                    'qty'        => $data['stock_qty'],
                    'reference_type' => 'initial',
                    'reference_id'   => null,
                    'note'       => 'Stok awal',
                    'created_by' => Auth::id(),
                ]);
            }
        } else {
            $old = ProductModel::find($id);
            if (!$this->canAccessBranch((int) $old['branch_id'])) {
                $this->withError('Produk tidak ditemukan.', 'products/index');
            }
            ProductModel::update($id, $data);
            AuditLogger::log('products', 'update', 'Ubah produk: ' . $data['name'], $old, $data);
        }

        $this->withSuccess('Produk berhasil disimpan.', 'products/index');
    }
}
