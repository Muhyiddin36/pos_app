<div class="content-header">
    <h1>Laporan Penjualan</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('reports/sales')) ?>" class="filter-bar mb-0">
            <div class="form-group"><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($from) ?>"></div>
            <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($to) ?>"></div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Terapkan</button></div>
            <div class="form-group"><a class="btn btn-secondary" href="<?= e(url('reports/sales?from=' . $from . '&to=' . $to . '&export=csv')) ?>">Ekspor CSV</a></div>
        </form>
    </div>
</div>

<div class="grid grid-cols-2">
    <div class="card stat-card"><div class="stat-label">Jumlah Transaksi</div><div class="stat-value"><?= (int) $summary['trx_count'] ?></div></div>
    <div class="card stat-card"><div class="stat-label">Total Omzet</div><div class="stat-value"><?= rupiah($summary['omzet']) ?></div></div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Invoice</th><th>Tanggal</th><th>Pelanggan</th><th>Metode Bayar</th><th class="text-right">Total</th></tr></thead>
            <tbody>
            <?php if (empty($sales)): ?>
                <tr><td colspan="5" class="empty-state">Tidak ada data pada rentang tanggal ini.</td></tr>
            <?php endif; ?>
            <?php foreach ($sales as $s): ?>
                <tr>
                    <td><?= e($s['invoice_no']) ?></td>
                    <td><?= e(date('d-m-Y H:i', strtotime($s['sale_date']))) ?></td>
                    <td><?= e($s['customer_name'] ?? 'Umum') ?></td>
                    <td><?= e($s['payment_method']) ?></td>
                    <td class="text-right"><?= rupiah($s['total_amount']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
