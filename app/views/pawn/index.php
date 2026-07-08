<div class="content-header">
    <div>
        <h1>Gadai Barang</h1>
        <p class="text-muted mb-0">Daftar transaksi gadai barang pelanggan.</p>
    </div>
    <?php if (Auth::can('pawn.create')): ?>
    <a class="btn" href="<?= e(url('pawn/create')) ?>">+ Gadai Baru</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('pawn/index')) ?>" class="filter-bar mb-0">
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
            <thead><tr><th>No. Gadai</th><th>Pelanggan</th><th>Barang</th><th class="text-right">Pinjaman</th><th>Jatuh Tempo</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($pawns)): ?>
                <tr><td colspan="7" class="empty-state">Belum ada data gadai.</td></tr>
            <?php endif; ?>
            <?php foreach ($pawns as $p):
                $badge = ['active' => 'badge-info', 'redeemed' => 'badge-success', 'overdue' => 'badge-danger', 'auctioned' => 'badge-gray'][$p['status']] ?? 'badge-gray'; ?>
                <tr>
                    <td><?= e($p['pawn_no']) ?></td>
                    <td><?= e($p['customer_name']) ?></td>
                    <td><?= e($p['item_name']) ?></td>
                    <td class="text-right"><?= rupiah($p['loan_amount']) ?></td>
                    <td><?= tgl($p['due_date']) ?></td>
                    <td><span class="badge <?= $badge ?>"><?= e($p['status']) ?></span></td>
                    <td class="text-right"><a class="btn btn-sm btn-outline" href="<?= e(url('pawn/show/' . $p['id'])) ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
