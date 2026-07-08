<div class="content-header">
    <h1>Detail Penjualan <?= e($sale['invoice_no']) ?></h1>
    <div class="flex gap-sm">
        <?php if (Auth::can('sales.print')): ?><a class="btn btn-outline" target="_blank" href="<?= e(url('sales/print/' . $sale['id'])) ?>">Cetak Struk</a><?php endif; ?>
        <a class="btn btn-secondary" href="<?= e(url('sales/index')) ?>">Kembali</a>
    </div>
</div>

<div class="card">
    <div class="card-body grid grid-cols-3">
        <p><strong>Tanggal:</strong> <?= e(date('d-m-Y H:i', strtotime($sale['sale_date']))) ?></p>
        <p><strong>Pelanggan:</strong> <?= e($sale['customer_name'] ?? 'Umum') ?></p>
        <p><strong>Status:</strong> <?= $sale['status'] === 'void' ? '<span class="badge badge-danger">Dibatalkan</span>' : '<span class="badge badge-success">Selesai</span>' ?></p>
    </div>
    <?php if ($sale['status'] === 'void'): ?>
    <div class="card-body" style="padding-top:0;">
        <div class="alert alert-warning mb-0">Dibatalkan: <?= e($sale['void_reason'] ?? '-') ?></div>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Produk</th><th class="text-right">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td><?= e($it['product_name']) ?></td>
                    <td class="text-right"><?= (int) $it['qty'] ?> <?= e($it['unit']) ?></td>
                    <td class="text-right"><?= rupiah($it['price']) ?></td>
                    <td class="text-right"><?= rupiah($it['subtotal']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="pos-summary-row"><span>Subtotal</span><strong><?= rupiah($sale['subtotal']) ?></strong></div>
        <div class="pos-summary-row"><span>Diskon</span><strong><?= rupiah($sale['discount_amount']) ?></strong></div>
        <div class="pos-summary-row total"><span>Total</span><strong><?= rupiah($sale['total_amount']) ?></strong></div>
    </div>
</div>

<?php if ($sale['status'] === 'completed' && Auth::can('sales.void')): ?>
<div class="card">
    <div class="card-header"><h3>Batalkan Transaksi</h3></div>
    <div class="card-body">
        <form method="post" action="<?= e(url('sales/void/' . $sale['id'])) ?>" data-confirm="Yakin membatalkan transaksi ini? Stok akan dikembalikan.">
            <?= Csrf::field() ?>
            <div class="form-group">
                <label>Alasan Pembatalan <span class="req">*</span></label>
                <input type="text" name="reason" required>
            </div>
            <button type="submit" class="btn btn-danger">Batalkan Transaksi</button>
        </form>
    </div>
</div>
<?php endif; ?>
