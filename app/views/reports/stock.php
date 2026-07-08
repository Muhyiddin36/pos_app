<div class="content-header">
    <h1>Laporan Stok</h1>
    <a class="btn btn-secondary" href="<?= e(url('reports/stock?export=csv')) ?>">Ekspor CSV</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>SKU</th><th>Nama Produk</th><th>Kategori</th><th class="text-right">Stok</th><th class="text-right">Min</th><th class="text-right">Harga Jual</th><th class="text-right">Nilai Stok</th></tr></thead>
            <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" class="empty-state">Tidak ada data produk.</td></tr>
            <?php endif; ?>
            <?php $totalValue = 0; foreach ($products as $p):
                $value = (float) $p['stock_qty'] * (float) $p['purchase_price'];
                $totalValue += $value; ?>
                <tr>
                    <td><?= e($p['sku']) ?></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name'] ?? '-') ?></td>
                    <td class="text-right"><?= (int) $p['stock_qty'] <= (int) $p['min_stock'] ? '<span class="badge badge-danger">' . (int) $p['stock_qty'] . '</span>' : (int) $p['stock_qty'] ?></td>
                    <td class="text-right"><?= (int) $p['min_stock'] ?></td>
                    <td class="text-right"><?= rupiah($p['sale_price']) ?></td>
                    <td class="text-right"><?= rupiah($value) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <?php if (!empty($products)): ?>
            <tfoot><tr><td colspan="6"><strong>Total Nilai Stok (harga beli)</strong></td><td class="text-right"><strong><?= rupiah($totalValue) ?></strong></td></tr></tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
