<div class="receipt" style="width:340px;">
    <h2><?= e(APP_NAME) ?></h2>
    <p class="center">Bukti Tanda Terima Servis</p>
    <hr>
    <table>
        <tr><td>No. Servis</td><td>: <?= e($order['service_no']) ?></td></tr>
        <tr><td>Tanggal Terima</td><td>: <?= tgl($order['received_date']) ?></td></tr>
        <tr><td>Pelanggan</td><td>: <?= e($order['customer_name']) ?></td></tr>
        <tr><td>No. HP</td><td>: <?= e($order['customer_phone'] ?? '-') ?></td></tr>
        <tr><td>Perangkat</td><td>: <?= e($order['device_type']) ?></td></tr>
        <?php if ($order['device_imei']): ?><tr><td>IMEI</td><td>: <?= e($order['device_imei']) ?></td></tr><?php endif; ?>
        <tr><td>Keluhan</td><td>: <?= e($order['issue_description']) ?></td></tr>
        <?php if ($order['accessories_note']): ?><tr><td>Kelengkapan</td><td>: <?= e($order['accessories_note']) ?></td></tr><?php endif; ?>
        <tr><td>Perkiraan Biaya</td><td>: Rp <?= e(number_format((float) $order['estimated_cost'], 0, ',', '.')) ?></td></tr>
        <tr><td>Uang Muka</td><td>: Rp <?= e(number_format((float) $order['down_payment'], 0, ',', '.')) ?></td></tr>
    </table>
    <hr>
    <p class="center">Harap simpan bukti ini dan bawa saat pengambilan unit.</p>
</div>
