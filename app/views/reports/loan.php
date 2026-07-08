<div class="content-header">
    <h1>Laporan Pinjaman</h1>
</div>

<div class="card stat-card" style="max-width:320px;">
    <div class="stat-label">Total Outstanding Pinjaman</div>
    <div class="stat-value"><?= rupiah($outstanding) ?></div>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('reports/loan')) ?>" class="filter-bar mb-0">
            <div class="form-group">
                <label>Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Aktif</option>
                    <option value="paid_off" <?= $status === 'paid_off' ? 'selected' : '' ?>>Lunas</option>
                    <option value="overdue" <?= $status === 'overdue' ? 'selected' : '' ?>>Menunggak</option>
                    <option value="default" <?= $status === 'default' ? 'selected' : '' ?>>Macet</option>
                </select>
            </div>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Pinjaman</th><th>Pelanggan</th><th class="text-right">Pokok</th><th>Tenor</th><th>Jatuh Tempo</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($loans)): ?>
                <tr><td colspan="6" class="empty-state">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php foreach ($loans as $l): ?>
                <tr>
                    <td><?= e($l['loan_no']) ?></td>
                    <td><?= e($l['customer_name']) ?></td>
                    <td class="text-right"><?= rupiah($l['principal_amount']) ?></td>
                    <td><?= (int) $l['term_months'] ?> bulan</td>
                    <td><?= tgl($l['due_date']) ?></td>
                    <td><span class="badge badge-gray"><?= e($l['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
