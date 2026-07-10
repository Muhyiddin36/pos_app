<?php
$statusText = [
    'received' => 'Diterima', 'in_progress' => 'Dikerjakan', 'waiting_parts' => 'Menunggu Sparepart',
    'completed' => 'Selesai', 'picked_up' => 'Sudah Diambil', 'cancelled' => 'Dibatalkan',
];
?>
<div class="content-header">
    <h1>Laporan Servis HP</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('reports/service')) ?>" class="filter-bar mb-0">
            <div class="form-group">
                <label>Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusText as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $status === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-3">
    <div class="card stat-card"><div class="stat-label">Jumlah Servis</div><div class="stat-value"><?= (int) $summary['count'] ?></div></div>
    <div class="card stat-card"><div class="stat-label">Total Nilai Servis</div><div class="stat-value"><?= rupiah($summary['final_cost']) ?></div></div>
    <div class="card stat-card"><div class="stat-label">Total Terbayar</div><div class="stat-value"><?= rupiah($summary['paid']) ?></div></div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Servis</th><th>Pelanggan</th><th>Perangkat</th><th>Diterima</th><th class="text-right">Biaya</th><th class="text-right">Terbayar</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="7" class="empty-state">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= e($o['service_no']) ?></td>
                    <td><?= e($o['customer_name']) ?></td>
                    <td><?= e($o['device_type']) ?></td>
                    <td><?= tgl($o['received_date']) ?></td>
                    <td class="text-right"><?= rupiah($o['final_cost'] ?: $o['estimated_cost']) ?></td>
                    <td class="text-right"><?= rupiah($o['paid_amount']) ?></td>
                    <td><span class="badge badge-gray"><?= e($statusText[$o['status']] ?? $o['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
