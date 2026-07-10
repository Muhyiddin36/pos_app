<div class="content-header">
    <h1>Laporan Saldo Kas Harian</h1>
    <a class="btn btn-secondary" href="<?= e(url('reports/cash?from=' . $from . '&to=' . $to . '&export=csv')) ?>">Ekspor CSV</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('reports/cash')) ?>" class="filter-bar mb-0">
            <div class="form-group"><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($from) ?>"></div>
            <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($to) ?>"></div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Terapkan</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><?php if (Auth::isSuperAdmin()): ?><th>Cabang</th><?php endif; ?><th>Sumber Dana</th><th class="text-right">Saldo Akhir</th><th>Catatan</th><th>Dicatat Oleh</th></tr></thead>
            <tbody>
            <?php if (empty($history)): ?>
                <tr><td colspan="6" class="empty-state">Tidak ada data pada rentang tanggal ini.</td></tr>
            <?php endif; ?>
            <?php foreach ($history as $h): ?>
                <tr>
                    <td><?= tgl($h['record_date']) ?></td>
                    <?php if (Auth::isSuperAdmin()): ?><td><?= e($h['branch_name']) ?></td><?php endif; ?>
                    <td><?= e($h['source_name']) ?></td>
                    <td class="text-right"><?= rupiah($h['closing_balance']) ?></td>
                    <td class="text-muted"><?= e($h['note'] ?? '-') ?></td>
                    <td><?= e($h['updated_by_name'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
