<div class="content-header">
    <div>
        <h1>Pinjam Uang</h1>
        <p class="text-muted mb-0">Daftar pinjaman uang pelanggan beserta status angsuran.</p>
    </div>
    <?php if (Auth::can('loan.create')): ?>
    <a class="btn" href="<?= e(url('loan/create')) ?>">+ Pinjaman Baru</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('loan/index')) ?>" class="filter-bar mb-0">
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
            <thead><tr><th>No. Pinjaman</th><th>Pelanggan</th><th class="text-right">Pokok</th><th>Tenor</th><th>Jatuh Tempo</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($loans)): ?>
                <tr><td colspan="7" class="empty-state">Belum ada data pinjaman.</td></tr>
            <?php endif; ?>
            <?php foreach ($loans as $l):
                $badge = ['active' => 'badge-info', 'paid_off' => 'badge-success', 'overdue' => 'badge-danger', 'default' => 'badge-danger'][$l['status']] ?? 'badge-gray'; ?>
                <tr>
                    <td><?= e($l['loan_no']) ?></td>
                    <td><?= e($l['customer_name']) ?></td>
                    <td class="text-right"><?= rupiah($l['principal_amount']) ?></td>
                    <td><?= (int) $l['term_months'] ?> bulan</td>
                    <td><?= tgl($l['due_date']) ?></td>
                    <td><span class="badge <?= $badge ?>"><?= e($l['status']) ?></span></td>
                    <td class="text-right"><a class="btn btn-sm btn-outline" href="<?= e(url('loan/show/' . $l['id'])) ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
