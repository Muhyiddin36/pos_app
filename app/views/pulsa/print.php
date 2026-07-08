<div class="receipt">
    <h2><?= e(APP_NAME) ?></h2>
    <p class="center">Struk Transaksi Pulsa/Data</p>
    <hr>
    <table>
        <tr><td>No. Transaksi</td><td>: <?= e($trx['trx_no']) ?></td></tr>
        <tr><td>Tanggal</td><td>: <?= e(date('d-m-Y H:i', strtotime($trx['created_at']))) ?></td></tr>
        <tr><td>Produk</td><td>: <?= e($trx['provider'] . ' - ' . $trx['product_name']) ?></td></tr>
        <tr><td>No. Tujuan</td><td>: <?= e($trx['customer_phone']) ?></td></tr>
        <tr><td>Kasir</td><td>: <?= e($trx['created_by_name'] ?? '-') ?></td></tr>
    </table>
    <hr>
    <table>
        <tr><td><strong>Total Bayar</strong></td><td style="text-align:right;"><strong>Rp <?= e(number_format((float) $trx['sale_price'], 0, ',', '.')) ?></strong></td></tr>
    </table>
    <hr>
    <p class="center">Terima kasih</p>
</div>
