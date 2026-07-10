<?php

declare(strict_types=1);

final class PulsaController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('pulsa.view');
        $from = $this->get('from');
        $to = $this->get('to');

        $this->view('pulsa/index', [
            'title'        => 'Pulsa, Data & Top Up Saldo',
            'transactions' => PulsaTransactionModel::listForBranch($this->currentBranchId(), $from, $to),
            'products'     => PulsaProductModel::activeList(),
            'from'         => $from,
            'to'           => $to,
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('pulsa.create');
        $this->requireCsrf();

        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Super Admin tidak dapat bertransaksi langsung. Gunakan akun cabang.', 'pulsa/index');
        }

        $productId = (int) $this->post('pulsa_product_id');
        $phone = $this->post('customer_phone');
        $product = PulsaProductModel::find($productId);

        if ($product === null || $phone === '') {
            $this->withError('Produk dan nomor tujuan wajib diisi.', 'pulsa/index');
        }

        $data = [
            'branch_id'        => $branchId,
            'pulsa_product_id' => $productId,
            'trx_no'           => PulsaTransactionModel::nextTrxNumber(),
            'customer_phone'   => $phone,
            'cost_price'       => $product['cost_price'],
            'sale_price'       => $product['sale_price'],
            'profit_amount'    => $product['sale_price'] - $product['cost_price'],
            'status'           => 'success',
            'note'             => $this->post('note'),
            'created_by'       => Auth::id(),
        ];

        $id = PulsaTransactionModel::create($data);
        AuditLogger::log('pulsa', 'create', 'Transaksi pulsa/data: ' . $data['trx_no'] . ' - ' . $phone, null, $data);

        redirect('pulsa/print/' . $id);
    }

    public function print(string $id): void
    {
        $this->requirePermission('pulsa.view');
        $sql = 'SELECT t.*, pp.name AS product_name, pp.provider, u.full_name AS created_by_name
                FROM pulsa_transactions t
                INNER JOIN pulsa_products pp ON pp.id = t.pulsa_product_id
                LEFT JOIN users u ON u.id = t.created_by
                WHERE t.id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $trx = $stmt->fetch();
        if ($trx === false) {
            $this->withError('Transaksi tidak ditemukan.', 'pulsa/index');
        }
        $this->view('pulsa/print', ['title' => 'Struk Pulsa', 'trx' => $trx], 'print');
    }

    // -------------------- Kelola Produk Pulsa & Paket Data --------------------

    public function products(): void
    {
        $this->requirePermission('pulsa.manage_product');
        $this->view('pulsa/products', [
            'title'    => 'Produk Pulsa, Data & Top Up Saldo',
            'products' => PulsaProductModel::all('provider, nominal'),
        ]);
    }

    public function productCreate(): void
    {
        $this->requirePermission('pulsa.manage_product');
        $this->view('pulsa/product_form', ['title' => 'Tambah Produk Pulsa', 'item' => null]);
    }

    public function productStore(): void
    {
        $this->requirePermission('pulsa.manage_product');
        $this->requireCsrf();
        $this->saveProduct(null);
    }

    public function productEdit(string $id): void
    {
        $this->requirePermission('pulsa.manage_product');
        $item = PulsaProductModel::find((int) $id);
        if ($item === null) {
            $this->withError('Produk tidak ditemukan.', 'pulsa/products');
        }
        $this->view('pulsa/product_form', ['title' => 'Ubah Produk Pulsa', 'item' => $item]);
    }

    public function productUpdate(string $id): void
    {
        $this->requirePermission('pulsa.manage_product');
        $this->requireCsrf();
        $this->saveProduct((int) $id);
    }

    public function productDestroy(string $id): void
    {
        $this->requirePermission('pulsa.manage_product');
        $this->requireCsrf();
        $item = PulsaProductModel::find((int) $id);
        if ($item === null) {
            $this->withError('Produk tidak ditemukan.', 'pulsa/products');
        }
        PulsaProductModel::delete((int) $id);
        AuditLogger::log('pulsa', 'delete', 'Hapus produk pulsa: ' . $item['name'], $item);
        $this->withSuccess('Produk berhasil dihapus.', 'pulsa/products');
    }

    private function saveProduct(?int $id): void
    {
        $data = [
            'category'   => $this->post('category'),
            'provider'   => $this->post('provider'),
            'name'       => $this->post('name'),
            'nominal'    => (float) $this->post('nominal'),
            'cost_price' => (float) $this->post('cost_price'),
            'sale_price' => (float) $this->post('sale_price'),
            'is_active'  => $this->postRaw('is_active') ? 1 : 0,
        ];

        $errors = Validator::make($data, [
            'category'   => 'required|in:pulsa,paket_data,pln,ewallet,other',
            'provider'   => 'required|max:50',
            'name'       => 'required|max:100',
            'cost_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
        ])->errors();

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old_input'] = $data;
            $this->withError('Periksa kembali data yang diisi.', $id === null ? 'pulsa/productCreate' : 'pulsa/productEdit/' . $id);
        }

        if ($id === null) {
            PulsaProductModel::create($data);
            AuditLogger::log('pulsa', 'create', 'Tambah produk pulsa: ' . $data['name']);
        } else {
            PulsaProductModel::update($id, $data);
            AuditLogger::log('pulsa', 'update', 'Ubah produk pulsa: ' . $data['name']);
        }

        $this->withSuccess('Produk pulsa berhasil disimpan.', 'pulsa/products');
    }
}
