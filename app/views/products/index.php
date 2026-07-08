<div class="content-header">
    <div>
        <h1>Produk</h1>
        <p class="text-muted mb-0">Data produk aksesoris HP beserta stok per cabang.</p>
    </div>
    <?php if (Auth::can('products.manage')): ?>
    <a class="btn" href="<?= e(url('products/create')) ?>">+ Tambah Produk</a>
    <?php endif; ?>
</div>

<?php if (Auth::isSuperAdmin()): ?>
<div class="filter-bar">
    <form method="get" action="<?= e(url('products/index')) ?>" class="flex gap-sm" style="align-items:end;">
        <div class="form-group">
            <label>Cabang</label>
            <select name="branch_id" onchange="this.form.submit()">
                <option value="">-- Pilih Cabang --</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= (int) $b['id'] ?>" <?= $selectedBranchId === (int) $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <input type="search" placeholder="Cari produk..." data-table-search="#tbl-products" style="max-width:260px;">
    </div>
    <div class="table-wrap">
        <table id="tbl-products">
            <thead><tr><th>SKU</th><th>Nama Produk</th><th>Kategori</th><th class="text-right">Harga Beli</th><th class="text-right">Harga Jual</th><th class="text-right">Stok</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if ($selectedBranchId === null): ?>
                <tr><td colspan="8" class="empty-state">Silakan pilih cabang terlebih dahulu.</td></tr>
            <?php elseif (empty($products)): ?>
                <tr><td colspan="8" class="empty-state">Belum ada produk untuk cabang ini.</td></tr>
            <?php endif; ?>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= e($p['sku']) ?></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name'] ?? '-') ?></td>
                    <td class="text-right"><?= rupiah($p['purchase_price']) ?></td>
                    <td class="text-right"><?= rupiah($p['sale_price']) ?></td>
                    <td class="text-right">
                        <?php if ((int) $p['stock_qty'] <= (int) $p['min_stock']): ?>
                            <span class="badge badge-danger"><?= (int) $p['stock_qty'] ?></span>
                        <?php else: ?>
                            <?= (int) $p['stock_qty'] ?>
                        <?php endif; ?>
                        <?= e($p['unit']) ?>
                    </td>
                    <td><?= $p['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
                    <td class="text-right">
                        <?php if (Auth::can('products.manage')): ?>
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('products/edit/' . $p['id'])) ?>">Ubah</a>
                            <form method="post" action="<?= e(url('products/destroy/' . $p['id'])) ?>" data-confirm="Hapus produk ini?">
                                <?= Csrf::field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
