<?php $typeLabel = ['transfer' => 'Transfer', 'setor_tunai' => 'Setor Tunai', 'tarik_tunai' => 'Tarik Tunai']; ?>
<div class="receipt">
    <h2><?= e(APP_NAME) ?></h2>
    <p class="center">Struk <?= e($typeLabel[$trx['transaction_type']] ?? $trx['transaction_type']) ?></p>
    <hr>
    <table>
        <tr><td>No. Transaksi</td><td>: <?= e($trx['trx_no']) ?></td></tr>
        <tr><td>Tanggal</td><td>: <?= e(date('d-m-Y H:i', strtotime($trx['created_at']))) ?></td></tr>
        <tr><td>Bank</td><td>: <?= e($trx['bank_name']) ?></td></tr>
        <?php if ($trx['account_number']): ?><tr><td>No. Rekening</td><td>: <?= e($trx['account_number']) ?></td></tr><?php endif; ?>
        <?php if ($trx['account_name']): ?><tr><td>Nama Rekening</td><td>: <?= e($trx['account_name']) ?></td></tr><?php endif; ?>
        <tr><td>Petugas</td><td>: <?= e($trx['created_by_name'] ?? '-') ?></td></tr>
    </table>
    <hr>
    <table>
        <tr><td>Nominal</td><td style="text-align:right;">Rp <?= e(number_format((float) $trx['amount'], 0, ',', '.')) ?></td></tr>
        <tr><td>Biaya Jasa</td><td style="text-align:right;">Rp <?= e(number_format((float) $trx['admin_fee'], 0, ',', '.')) ?></td></tr>
        <tr><td><strong>Total</strong></td><td style="text-align:right;"><strong>Rp <?= e(number_format((float) $trx['amount'] + (float) $trx['admin_fee'], 0, ',', '.')) ?></strong></td></tr>
    </table>
    <hr>
    <p class="center">Simpan struk ini sebagai bukti transaksi</p>
</div>
