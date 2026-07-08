<div class="content-header">
    <div>
        <h1>Pembelian</h1>
        <p class="text-muted mb-0">Riwayat pembelian barang dari supplier (menambah stok).</p>
    </div>
    <?php if (Auth::can('purchases.manage')): ?>
    <a class="btn" href="<?= e(url('purchases/create')) ?>">+ Tambah Pembelian</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('purchases/index')) ?>" class="filter-bar mb-0">
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
            <thead><tr><th>No. Faktur</th><th>Tanggal</th><th>Supplier</th><th class="text-right">Total</th><th>Dibuat Oleh</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($purchases)): ?>
                <tr><td colspan="6" class="empty-state">Belum ada data pembelian.</td></tr>
            <?php endif; ?>
            <?php foreach ($purchases as $p): ?>
                <tr>
                    <td><?= e($p['invoice_no']) ?></td>
                    <td><?= tgl($p['purchase_date']) ?></td>
                    <td><?= e($p['supplier_name'] ?? '-') ?></td>
                    <td class="text-right"><?= rupiah($p['total_amount']) ?></td>
                    <td><?= e($p['created_by_name'] ?? '-') ?></td>
                    <td class="text-right"><a class="btn btn-sm btn-outline" href="<?= e(url('purchases/show/' . $p['id'])) ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
