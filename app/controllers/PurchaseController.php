<?php

declare(strict_types=1);

final class PurchaseController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('purchases.view');
        $from = $this->get('from');
        $to = $this->get('to');

        $this->view('purchases/index', [
            'title'     => 'Pembelian',
            'purchases' => PurchaseModel::listForBranch($this->currentBranchId(), $from, $to),
            'from'      => $from,
            'to'        => $to,
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('purchases.manage');
        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Super Admin harus memilih cabang melalui menu Produk sebelum membuat pembelian.', 'purchases/index');
        }
        $this->view('purchases/form', [
            'title'     => 'Tambah Pembelian',
            'suppliers' => SupplierModel::all('name'),
            'products'  => ProductModel::listForBranch($branchId),
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('purchases.manage');
        $this->requireCsrf();

        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Cabang tidak valid.', 'purchases/index');
        }

        $productIds = (array) $this->postRaw('product_id', []);
        $qtys = (array) $this->postRaw('qty', []);
        $prices = (array) $this->postRaw('price', []);

        $items = [];
        foreach ($productIds as $i => $productId) {
            $qty = (int) ($qtys[$i] ?? 0);
            $price = (float) ($prices[$i] ?? 0);
            if ((int) $productId > 0 && $qty > 0) {
                $items[] = ['product_id' => (int) $productId, 'qty' => $qty, 'price' => $price];
            }
        }

        if ($items === []) {
            $this->withError('Minimal harus ada satu item pembelian.', 'purchases/create');
        }

        $supplierIdRaw = $this->post('supplier_id');
        $total = array_sum(array_map(fn ($it) => $it['qty'] * $it['price'], $items));

        $header = [
            'branch_id'     => $branchId,
            'supplier_id'   => $supplierIdRaw === '' ? null : (int) $supplierIdRaw,
            'invoice_no'    => $this->post('invoice_no') ?: generate_doc_number('PO'),
            'purchase_date' => $this->post('purchase_date') ?: date('Y-m-d'),
            'total_amount'  => $total,
            'note'          => $this->post('note'),
            'created_by'    => Auth::id(),
        ];

        try {
            $id = PurchaseModel::createWithItems($header, $items);
        } catch (Throwable $e) {
            $this->withError('Gagal menyimpan pembelian: ' . $e->getMessage(), 'purchases/create');
        }

        AuditLogger::log('purchases', 'create', 'Pembelian baru: ' . $header['invoice_no'], null, $header);
        $this->withSuccess('Pembelian berhasil disimpan dan stok telah diperbarui.', 'purchases/show/' . $id);
    }

    public function show(string $id): void
    {
        $this->requirePermission('purchases.view');
        $purchase = PurchaseModel::find((int) $id);
        if ($purchase === null) {
            $this->withError('Pembelian tidak ditemukan.', 'purchases/index');
        }
        $this->view('purchases/show', [
            'title'    => 'Detail Pembelian',
            'purchase' => $purchase,
            'items'    => PurchaseModel::items((int) $id),
        ]);
    }
}
