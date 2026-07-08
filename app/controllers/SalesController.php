<?php

declare(strict_types=1);

final class SalesController extends Controller
{
    public function pos(): void
    {
        $this->requirePermission('sales.create');
        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Super Admin tidak dapat bertransaksi langsung. Gunakan akun cabang.', 'dashboard/index');
        }

        $this->view('sales/pos', [
            'title'        => 'Kasir Aksesoris',
            'customers'    => CustomerModel::listForBranch($branchId),
            'extraScripts' => ['js/pos.js'],
        ], 'app');
    }

    public function store(): void
    {
        $this->requirePermission('sales.create');
        $this->requireCsrf();

        $branchId = $this->currentBranchId();
        if ($branchId === null) {
            $this->withError('Cabang tidak valid.', 'sales/pos');
        }

        $itemsJson = $this->postRaw('items_json', '[]');
        $items = json_decode((string) $itemsJson, true);
        if (!is_array($items) || $items === []) {
            $this->withError('Keranjang belanja kosong.', 'sales/pos');
        }

        $canEditPrice = Auth::can('sales.edit_price');
        $subtotal = 0.0;
        $preparedItems = [];
        foreach ($items as $row) {
            $productId = (int) ($row['product_id'] ?? 0);
            $qty = (int) ($row['qty'] ?? 0);
            $product = ProductModel::find($productId);
            if ($product === null || (int) $product['branch_id'] !== $branchId || $qty <= 0) {
                $this->withError('Salah satu produk pada keranjang tidak valid.', 'sales/pos');
            }

            // Harga jual selalu diambil ulang dari database kecuali user memiliki izin ubah harga.
            $price = $canEditPrice && isset($row['price']) ? (float) $row['price'] : (float) $product['sale_price'];
            $costPrice = (float) $product['purchase_price'];
            $rowSubtotal = $price * $qty;
            $subtotal += $rowSubtotal;

            $preparedItems[] = [
                'product_id' => $productId,
                'qty'        => $qty,
                'price'      => $price,
                'cost_price' => $costPrice,
                'subtotal'   => $rowSubtotal,
            ];
        }

        $discount = min((float) $this->post('discount'), $subtotal);
        $total = max($subtotal - $discount, 0);
        $paid = (float) $this->post('paid');
        if ($paid < $total) {
            $this->withError('Jumlah bayar kurang dari total belanja.', 'sales/pos');
        }

        $customerIdRaw = $this->post('customer_id');
        $header = [
            'branch_id'       => $branchId,
            'customer_id'     => $customerIdRaw === '' ? null : (int) $customerIdRaw,
            'invoice_no'      => SaleModel::nextInvoiceNumber(),
            'sale_date'       => date('Y-m-d H:i:s'),
            'subtotal'        => $subtotal,
            'discount_amount' => $discount,
            'total_amount'    => $total,
            'paid_amount'     => $paid,
            'change_amount'   => $paid - $total,
            'payment_method'  => $this->post('payment_method') ?: 'cash',
            'status'          => 'completed',
            'created_by'      => Auth::id(),
        ];

        try {
            $saleId = SaleModel::createWithItems($header, $preparedItems);
        } catch (Throwable $e) {
            $this->withError('Transaksi gagal: ' . $e->getMessage(), 'sales/pos');
        }

        AuditLogger::log('sales', 'create', 'Penjualan baru: ' . $header['invoice_no'], null, $header);
        redirect('sales/print/' . $saleId);
    }

    public function index(): void
    {
        $this->requirePermission('sales.view');
        $from = $this->get('from');
        $to = $this->get('to');

        $this->view('sales/index', [
            'title' => 'Riwayat Penjualan',
            'sales' => SaleModel::listForBranch($this->currentBranchId(), $from, $to),
            'from'  => $from,
            'to'    => $to,
        ]);
    }

    public function show(string $id): void
    {
        $this->requirePermission('sales.view');
        $sale = SaleModel::withCustomer((int) $id);
        if ($sale === null) {
            $this->withError('Transaksi tidak ditemukan.', 'sales/index');
        }
        $this->view('sales/show', [
            'title' => 'Detail Penjualan',
            'sale'  => $sale,
            'items' => SaleModel::items((int) $id),
        ]);
    }

    public function print(string $id): void
    {
        $this->requirePermission('sales.print');
        $sale = SaleModel::withCustomer((int) $id);
        if ($sale === null) {
            $this->withError('Transaksi tidak ditemukan.', 'sales/index');
        }
        $this->view('sales/print', [
            'title' => 'Struk ' . $sale['invoice_no'],
            'sale'  => $sale,
            'items' => SaleModel::items((int) $id),
        ], 'print');
    }

    public function void(string $id): void
    {
        $this->requirePermission('sales.void');
        $this->requireCsrf();

        $reason = $this->post('reason');
        if ($reason === '') {
            $this->withError('Alasan pembatalan wajib diisi.', 'sales/show/' . $id);
        }

        try {
            SaleModel::void((int) $id, Auth::id(), $reason);
        } catch (Throwable $e) {
            $this->withError('Gagal membatalkan transaksi: ' . $e->getMessage(), 'sales/show/' . $id);
        }

        AuditLogger::log('sales', 'void', 'Pembatalan transaksi #' . $id . ': ' . $reason);
        $this->withSuccess('Transaksi berhasil dibatalkan dan stok dikembalikan.', 'sales/show/' . $id);
    }
}
