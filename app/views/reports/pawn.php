<div class="content-header">
    <h1>Laporan Gadai</h1>
</div>

<div class="card stat-card" style="max-width:320px;">
    <div class="stat-label">Total Outstanding Pinjaman Gadai</div>
    <div class="stat-value"><?= rupiah($outstanding) ?></div>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('reports/pawn')) ?>" class="filter-bar mb-0">
            <div class="form-group">
                <label>Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Aktif</option>
                    <option value="redeemed" <?= $status === 'redeemed' ? 'selected' : '' ?>>Ditebus</option>
                    <option value="overdue" <?= $status === 'overdue' ? 'selected' : '' ?>>Jatuh Tempo</option>
                    <option value="auctioned" <?= $status === 'auctioned' ? 'selected' : '' ?>>Dilelang</option>
                </select>
            </div>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Gadai</th><th>Pelanggan</th><th>Barang</th><th class="text-right">Pinjaman</th><th>Jatuh Tempo</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($pawns)): ?>
                <tr><td colspan="6" class="empty-state">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php foreach ($pawns as $p): ?>
                <tr>
                    <td><?= e($p['pawn_no']) ?></td>
                    <td><?= e($p['customer_name']) ?></td>
                    <td><?= e($p['item_name']) ?></td>
                    <td class="text-right"><?= rupiah($p['loan_amount']) ?></td>
                    <td><?= tgl($p['due_date']) ?></td>
                    <td><span class="badge badge-gray"><?= e($p['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
