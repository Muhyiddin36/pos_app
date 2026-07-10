<div class="content-header">
    <h1>Laporan Transfer / Setor Tunai</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('reports/bank')) ?>" class="filter-bar mb-0">
            <div class="form-group"><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($from) ?>"></div>
            <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($to) ?>"></div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Terapkan</button></div>
        </form>
    </div>
</div>

<div class="grid grid-cols-3">
    <div class="card stat-card"><div class="stat-label">Jumlah Transaksi</div><div class="stat-value"><?= (int) $summary['count'] ?></div></div>
    <div class="card stat-card"><div class="stat-label">Total Nominal</div><div class="stat-value"><?= rupiah($summary['amount']) ?></div></div>
    <?php if (Auth::can('reports.profit')): ?>
    <div class="card stat-card"><div class="stat-label">Total Laba Jasa</div><div class="stat-value text-success"><?= rupiah($summary['profit']) ?></div></div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Transaksi</th><th>Tanggal</th><th>Jenis</th><th>Bank</th><th class="text-right">Nominal</th><th class="text-right">Biaya Jasa</th></tr></thead>
            <tbody>
            <?php $typeLabel = ['transfer' => 'Transfer', 'setor_tunai' => 'Setor Tunai', 'tarik_tunai' => 'Tarik Tunai']; ?>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="6" class="empty-state">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= e($t['trx_no']) ?></td>
                    <td><?= e(date('d-m-Y H:i', strtotime($t['created_at']))) ?></td>
                    <td><?= e($typeLabel[$t['transaction_type']] ?? $t['transaction_type']) ?></td>
                    <td><?= e($t['bank_name']) ?></td>
                    <td class="text-right"><?= rupiah($t['amount']) ?></td>
                    <td class="text-right"><?= rupiah($t['admin_fee']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
