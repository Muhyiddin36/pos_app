<div class="content-header">
    <div>
        <h1>Riwayat Penjualan</h1>
        <p class="text-muted mb-0">Daftar transaksi penjualan aksesoris HP.</p>
    </div>
    <?php if (Auth::can('sales.create')): ?>
    <a class="btn" href="<?= e(url('sales/pos')) ?>">+ Transaksi Baru</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('sales/index')) ?>" class="filter-bar mb-0">
            <div class="form-group">
                <label>Dari Tanggal</label>
                <input type="date" name="from" value="<?= e($from) ?>">
            </div>
            <div class="form-group">
                <label>Sampai Tanggal</label>
                <input type="date" name="to" value="<?= e($to) ?>">
            </div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Filter</button></div>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Invoice</th><th>Tanggal</th><th>Pelanggan</th><th class="text-right">Total</th><th>Status</th><th>Kasir</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($sales)): ?>
                <tr><td colspan="7" class="empty-state">Belum ada transaksi.</td></tr>
            <?php endif; ?>
            <?php foreach ($sales as $s): ?>
                <tr>
                    <td><?= e($s['invoice_no']) ?></td>
                    <td><?= e(date('d-m-Y H:i', strtotime($s['sale_date']))) ?></td>
                    <td><?= e($s['customer_name'] ?? 'Umum') ?></td>
                    <td class="text-right"><?= rupiah($s['total_amount']) ?></td>
                    <td><?= $s['status'] === 'void' ? '<span class="badge badge-danger">Dibatalkan</span>' : '<span class="badge badge-success">Selesai</span>' ?></td>
                    <td><?= e($s['created_by_name'] ?? '-') ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('sales/show/' . $s['id'])) ?>">Detail</a>
                            <?php if (Auth::can('sales.print')): ?>
                                <a class="btn btn-sm btn-outline" target="_blank" href="<?= e(url('sales/print/' . $s['id'])) ?>">Cetak</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
