<div class="content-header">
    <h1>Laporan Pulsa & Paket Data</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('reports/pulsa')) ?>" class="filter-bar mb-0">
            <div class="form-group"><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($from) ?>"></div>
            <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($to) ?>"></div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Terapkan</button></div>
        </form>
    </div>
</div>

<div class="grid grid-cols-3">
    <div class="card stat-card"><div class="stat-label">Jumlah Transaksi</div><div class="stat-value"><?= (int) $summary['count'] ?></div></div>
    <div class="card stat-card"><div class="stat-label">Total Omzet</div><div class="stat-value"><?= rupiah($summary['omzet']) ?></div></div>
    <?php if (Auth::can('reports.profit')): ?>
    <div class="card stat-card"><div class="stat-label">Total Laba</div><div class="stat-value text-success"><?= rupiah($summary['profit']) ?></div></div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Transaksi</th><th>Tanggal</th><th>Produk</th><th>No. Tujuan</th><th class="text-right">Harga Jual</th></tr></thead>
            <tbody>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="5" class="empty-state">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= e($t['trx_no']) ?></td>
                    <td><?= e(date('d-m-Y H:i', strtotime($t['created_at']))) ?></td>
                    <td><?= e($t['provider'] . ' - ' . $t['product_name']) ?></td>
                    <td><?= e($t['customer_phone']) ?></td>
                    <td class="text-right"><?= rupiah($t['sale_price']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
