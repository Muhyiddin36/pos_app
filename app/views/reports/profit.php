<div class="content-header">
    <h1>Laporan Laba Rugi</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('reports/profit')) ?>" class="filter-bar mb-0">
            <div class="form-group"><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($from) ?>"></div>
            <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($to) ?>"></div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Terapkan</button></div>
        </form>
    </div>
</div>

<div class="grid grid-cols-4">
    <div class="card stat-card"><div class="stat-label">Laba Aksesoris</div><div class="stat-value text-success"><?= rupiah($totalSalesProfit) ?></div></div>
    <div class="card stat-card"><div class="stat-label">Laba Pulsa, Data &amp; Top Up</div><div class="stat-value text-success"><?= rupiah($totalPulsaProfit) ?></div></div>
    <div class="card stat-card"><div class="stat-label">Laba Transfer/Setor Bank</div><div class="stat-value text-success"><?= rupiah($totalBankProfit) ?></div></div>
    <div class="card stat-card"><div class="stat-label">Total Laba</div><div class="stat-value text-success"><?= rupiah($totalSalesProfit + $totalPulsaProfit + $totalBankProfit) ?></div></div>
</div>

<div class="grid grid-cols-3">
    <div class="card">
        <div class="card-header"><h3>Laba Penjualan Aksesoris per Hari</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tanggal</th><th class="text-right">Omzet</th><th class="text-right">Laba</th></tr></thead>
                <tbody>
                <?php if (empty($salesProfit)): ?><tr><td colspan="3" class="empty-state">Tidak ada data.</td></tr><?php endif; ?>
                <?php foreach ($salesProfit as $row): ?>
                    <tr><td><?= tgl($row['d']) ?></td><td class="text-right"><?= rupiah($row['omzet']) ?></td><td class="text-right text-success"><?= rupiah($row['laba']) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Laba Pulsa, Data &amp; Top Up per Hari</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tanggal</th><th class="text-right">Omzet</th><th class="text-right">Laba</th></tr></thead>
                <tbody>
                <?php if (empty($pulsaProfit)): ?><tr><td colspan="3" class="empty-state">Tidak ada data.</td></tr><?php endif; ?>
                <?php foreach ($pulsaProfit as $row): ?>
                    <tr><td><?= tgl($row['d']) ?></td><td class="text-right"><?= rupiah($row['omzet']) ?></td><td class="text-right text-success"><?= rupiah($row['laba']) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Laba Transfer/Setor Bank per Hari</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tanggal</th><th class="text-right">Nominal</th><th class="text-right">Laba</th></tr></thead>
                <tbody>
                <?php if (empty($bankProfit)): ?><tr><td colspan="3" class="empty-state">Tidak ada data.</td></tr><?php endif; ?>
                <?php foreach ($bankProfit as $row): ?>
                    <tr><td><?= tgl($row['d']) ?></td><td class="text-right"><?= rupiah($row['omzet']) ?></td><td class="text-right text-success"><?= rupiah($row['laba']) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
