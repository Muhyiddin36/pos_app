<?php $footer = SettingModel::get('receipt_footer', ''); ?>
<div class="receipt">
    <h2><?= e(APP_NAME) ?></h2>
    <p class="center"><?= e(BranchModel::find((int) $sale['branch_id'])['name'] ?? '') ?></p>
    <hr>
    <table>
        <tr><td>No. Invoice</td><td>: <?= e($sale['invoice_no']) ?></td></tr>
        <tr><td>Tanggal</td><td>: <?= e(date('d-m-Y H:i', strtotime($sale['sale_date']))) ?></td></tr>
        <tr><td>Kasir</td><td>: <?= e($sale['created_by_name'] ?? '-') ?></td></tr>
        <tr><td>Pelanggan</td><td>: <?= e($sale['customer_name'] ?? 'Umum') ?></td></tr>
    </table>
    <hr>
    <table>
        <?php foreach ($items as $it): ?>
        <tr><td colspan="3"><?= e($it['product_name']) ?></td></tr>
        <tr>
            <td><?= (int) $it['qty'] ?> x <?= e(number_format((float) $it['price'], 0, ',', '.')) ?></td>
            <td></td>
            <td style="text-align:right;"><?= e(number_format((float) $it['subtotal'], 0, ',', '.')) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <hr>
    <table>
        <tr><td>Subtotal</td><td style="text-align:right;">Rp <?= e(number_format((float) $sale['subtotal'], 0, ',', '.')) ?></td></tr>
        <tr><td>Diskon</td><td style="text-align:right;">Rp <?= e(number_format((float) $sale['discount_amount'], 0, ',', '.')) ?></td></tr>
        <tr><td><strong>Total</strong></td><td style="text-align:right;"><strong>Rp <?= e(number_format((float) $sale['total_amount'], 0, ',', '.')) ?></strong></td></tr>
        <tr><td>Bayar</td><td style="text-align:right;">Rp <?= e(number_format((float) $sale['paid_amount'], 0, ',', '.')) ?></td></tr>
        <tr><td>Kembali</td><td style="text-align:right;">Rp <?= e(number_format((float) $sale['change_amount'], 0, ',', '.')) ?></td></tr>
    </table>
    <hr>
    <p class="center"><?= e($footer) ?></p>
</div>
