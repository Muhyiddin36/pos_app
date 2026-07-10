<?php
$statusBadge = [
    'received' => 'badge-info', 'in_progress' => 'badge-warning', 'waiting_parts' => 'badge-warning',
    'completed' => 'badge-success', 'picked_up' => 'badge-gray', 'cancelled' => 'badge-danger',
];
$statusText = [
    'received' => 'Diterima', 'in_progress' => 'Dikerjakan', 'waiting_parts' => 'Menunggu Sparepart',
    'completed' => 'Selesai (Siap Diambil)', 'picked_up' => 'Sudah Diambil', 'cancelled' => 'Dibatalkan',
];
$isOpen = !in_array($order['status'], ['picked_up', 'cancelled'], true);
?>
<div class="content-header">
    <h1>Servis <?= e($order['service_no']) ?> <span class="badge <?= $statusBadge[$order['status']] ?? 'badge-gray' ?>"><?= e($statusText[$order['status']] ?? $order['status']) ?></span></h1>
    <div class="flex gap-sm">
        <a class="btn btn-outline" target="_blank" href="<?= e(url('service/print/' . $order['id'])) ?>">Cetak Bukti Servis</a>
        <a class="btn btn-secondary" href="<?= e(url('service/index')) ?>">Kembali</a>
    </div>
</div>

<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header"><h3>Informasi Servis</h3></div>
        <div class="card-body">
            <p><strong>Pelanggan:</strong> <?= e($order['customer_name']) ?> (<?= e($order['customer_phone'] ?? '-') ?>)</p>
            <p><strong>Perangkat:</strong> <?= e($order['device_type']) ?></p>
            <?php if ($order['device_imei']): ?><p><strong>IMEI/Serial:</strong> <?= e($order['device_imei']) ?></p><?php endif; ?>
            <p><strong>Keluhan:</strong> <?= e($order['issue_description']) ?></p>
            <?php if ($order['accessories_note']): ?><p><strong>Kelengkapan:</strong> <?= e($order['accessories_note']) ?></p><?php endif; ?>
            <p><strong>Diterima:</strong> <?= tgl($order['received_date']) ?> <?php if ($order['estimated_finish_date']): ?>&middot; Estimasi selesai: <?= tgl($order['estimated_finish_date']) ?><?php endif; ?></p>
            <p><strong>Perkiraan Biaya:</strong> <?= rupiah($order['estimated_cost']) ?></p>
            <?php if ((float) $order['final_cost'] > 0): ?><p><strong>Biaya Final:</strong> <?= rupiah($order['final_cost']) ?></p><?php endif; ?>
            <p><strong>Sudah Dibayar:</strong> <?= rupiah($order['paid_amount']) ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Riwayat Status</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Waktu</th><th>Status</th><th>Catatan</th><th>Oleh</th></tr></thead>
                <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><?= e(date('d-m-Y H:i', strtotime($l['created_at']))) ?></td>
                        <td><span class="badge <?= $statusBadge[$l['status']] ?? 'badge-gray' ?>"><?= e($statusText[$l['status']] ?? $l['status']) ?></span></td>
                        <td><?= e($l['note'] ?? '-') ?></td>
                        <td><?= e($l['created_by_name'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($isOpen && Auth::can('service.manage')): ?>
<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header"><h3>Ubah Status Pengerjaan</h3></div>
        <form method="post" action="<?= e(url('service/changeStatus/' . $order['id'])) ?>">
            <?= Csrf::field() ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Status Baru</label>
                    <select name="status" required>
                        <option value="in_progress" <?= $order['status'] === 'in_progress' ? 'selected' : '' ?>>Dikerjakan</option>
                        <option value="waiting_parts">Menunggu Sparepart</option>
                        <option value="completed">Selesai (Siap Diambil)</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Catatan Teknisi</label>
                    <input type="text" name="note" placeholder="mis. Ganti LCD, tunggu part datang">
                </div>
            </div>
            <div class="card-footer"><button type="submit" class="btn">Perbarui Status</button></div>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h3>Serah Terima ke Pelanggan</h3></div>
        <form method="post" action="<?= e(url('service/pickup/' . $order['id'])) ?>" data-confirm="Yakin servis sudah selesai dan diserahkan ke pelanggan?">
            <?= Csrf::field() ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Biaya Final</label>
                    <input type="number" step="0.01" min="0" name="final_cost" value="<?= (float) ($order['final_cost'] ?: $order['estimated_cost']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Jumlah Dibayar Sekarang</label>
                    <input type="number" step="0.01" min="0" name="payment_amount" value="0">
                    <p class="form-hint">Sudah dibayar sebelumnya (DP): <?= rupiah($order['paid_amount']) ?></p>
                </div>
                <div class="form-group">
                    <label>Metode Bayar</label>
                    <select name="payment_method">
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                        <option value="debit">Debit/Kredit</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <input type="text" name="note">
                </div>
            </div>
            <div class="card-footer"><button type="submit" class="btn btn-success">Tandai Diambil &amp; Lunas</button></div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h3>Riwayat Pembayaran</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Jenis</th><th class="text-right">Jumlah</th><th>Metode</th><th>Catatan</th></tr></thead>
            <tbody>
            <?php if (empty($payments)): ?>
                <tr><td colspan="5" class="empty-state">Belum ada pembayaran.</td></tr>
            <?php endif; ?>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= tgl($p['payment_date']) ?></td>
                    <td><span class="badge badge-gray"><?= $p['type'] === 'down_payment' ? 'Uang Muka' : 'Pelunasan' ?></span></td>
                    <td class="text-right"><?= rupiah($p['amount']) ?></td>
                    <td><?= e($p['payment_method']) ?></td>
                    <td><?= e($p['note'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
