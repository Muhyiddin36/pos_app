<?php
$statusBadge = [
    'received' => 'badge-info', 'in_progress' => 'badge-warning', 'waiting_parts' => 'badge-warning',
    'completed' => 'badge-success', 'picked_up' => 'badge-gray', 'cancelled' => 'badge-danger',
];
$statusText = [
    'received' => 'Diterima', 'in_progress' => 'Dikerjakan', 'waiting_parts' => 'Menunggu Sparepart',
    'completed' => 'Selesai', 'picked_up' => 'Sudah Diambil', 'cancelled' => 'Dibatalkan',
];
?>
<div class="content-header">
    <div>
        <h1>Servis HP</h1>
        <p class="text-muted mb-0">Pencatatan servis HP pelanggan dari penerimaan sampai pengambilan.</p>
    </div>
    <?php if (Auth::can('service.create')): ?>
    <a class="btn" href="<?= e(url('service/create')) ?>">+ Terima Servis Baru</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('service/index')) ?>" class="filter-bar mb-0">
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
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Servis</th><th>Pelanggan</th><th>Perangkat</th><th>Keluhan</th><th>Diterima</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="7" class="empty-state">Belum ada data servis.</td></tr>
            <?php endif; ?>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= e($o['service_no']) ?></td>
                    <td><?= e($o['customer_name']) ?></td>
                    <td><?= e($o['device_type']) ?></td>
                    <td><?= e(mb_strimwidth($o['issue_description'], 0, 40, '...')) ?></td>
                    <td><?= tgl($o['received_date']) ?></td>
                    <td><span class="badge <?= $statusBadge[$o['status']] ?? 'badge-gray' ?>"><?= e($statusText[$o['status']] ?? $o['status']) ?></span></td>
                    <td class="text-right"><a class="btn btn-sm btn-outline" href="<?= e(url('service/show/' . $o['id'])) ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
