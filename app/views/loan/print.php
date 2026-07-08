<div class="receipt" style="width:340px;">
    <h2><?= e(APP_NAME) ?></h2>
    <p class="center">Surat Perjanjian Pinjaman</p>
    <hr>
    <table>
        <tr><td>No. Pinjaman</td><td>: <?= e($loan['loan_no']) ?></td></tr>
        <tr><td>Tanggal</td><td>: <?= tgl($loan['loan_date']) ?></td></tr>
        <tr><td>Pelanggan</td><td>: <?= e($loan['customer_name']) ?></td></tr>
        <tr><td>No. KTP</td><td>: <?= e($loan['id_card_number'] ?? '-') ?></td></tr>
        <tr><td>Pokok Pinjaman</td><td>: Rp <?= e(number_format((float) $loan['principal_amount'], 0, ',', '.')) ?></td></tr>
        <tr><td>Bunga</td><td>: <?= e($loan['interest_rate']) ?>% / bulan</td></tr>
        <tr><td>Tenor</td><td>: <?= (int) $loan['term_months'] ?> bulan</td></tr>
    </table>
    <hr>
    <p class="center">Jadwal Angsuran</p>
    <table>
        <?php foreach ($installments as $inst): ?>
        <tr>
            <td>#<?= (int) $inst['installment_no'] ?> - <?= tgl($inst['due_date']) ?></td>
            <td style="text-align:right;">Rp <?= e(number_format((float) $inst['total_amount'], 0, ',', '.')) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <hr>
    <p class="center">Terima kasih atas kepercayaan Anda.</p>
</div>
