<div class="content-header">
    <h1>Detail Pembelian <?= e($purchase['invoice_no']) ?></h1>
    <a class="btn btn-secondary" href="<?= e(url('purchases/index')) ?>">Kembali</a>
</div>

<div class="card">
    <div class="card-body grid grid-cols-3">
        <p><strong>Tanggal:</strong> <?= tgl($purchase['purchase_date']) ?></p>
        <p><strong>Catatan:</strong> <?= e($purchase['note'] ?? '-') ?></p>
        <p><strong>Total:</strong> <?= rupiah($purchase['total_amount']) ?></p>
    </div>
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
</div>
