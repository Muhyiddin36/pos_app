<div class="receipt" style="width:340px;">
    <h2><?= e(APP_NAME) ?></h2>
    <p class="center">Surat Bukti Gadai</p>
    <hr>
    <table>
        <tr><td>No. Gadai</td><td>: <?= e($pawn['pawn_no']) ?></td></tr>
        <tr><td>Tanggal</td><td>: <?= tgl($pawn['pawn_date']) ?></td></tr>
        <tr><td>Pelanggan</td><td>: <?= e($pawn['customer_name']) ?></td></tr>
        <tr><td>No. KTP</td><td>: <?= e($pawn['id_card_number'] ?? '-') ?></td></tr>
        <tr><td>Barang</td><td>: <?= e($pawn['item_name']) ?></td></tr>
        <tr><td>Taksiran</td><td>: Rp <?= e(number_format((float) $pawn['estimated_value'], 0, ',', '.')) ?></td></tr>
        <tr><td>Pinjaman</td><td>: Rp <?= e(number_format((float) $pawn['loan_amount'], 0, ',', '.')) ?></td></tr>
        <tr><td>Bunga</td><td>: <?= e($pawn['interest_rate']) ?>% / bulan</td></tr>
        <tr><td>Jatuh Tempo</td><td>: <?= tgl($pawn['due_date']) ?></td></tr>
    </table>
    <hr>
    <p class="center">Barang akan hangus apabila tidak ditebus setelah jatuh tempo sesuai ketentuan yang berlaku.</p>
</div>
