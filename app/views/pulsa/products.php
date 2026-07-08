<div class="content-header">
    <div>
        <h1>Produk Pulsa & Paket Data</h1>
        <p class="text-muted mb-0">Kelola daftar produk pulsa, paket data, dan tagihan lain beserta harga modal/jual.</p>
    </div>
    <a class="btn" href="<?= e(url('pulsa/productCreate')) ?>">+ Tambah Produk</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Provider</th><th>Nama</th><th>Kategori</th><th class="text-right">Harga Modal</th><th class="text-right">Harga Jual</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" class="empty-state">Belum ada produk.</td></tr>
            <?php endif; ?>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= e($p['provider']) ?></td>
                    <td><?= e($p['name']) ?></td>
                    <td><span class="badge badge-gray"><?= e($p['category']) ?></span></td>
                    <td class="text-right"><?= rupiah($p['cost_price']) ?></td>
                    <td class="text-right"><?= rupiah($p['sale_price']) ?></td>
                    <td><?= $p['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('pulsa/productEdit/' . $p['id'])) ?>">Ubah</a>
                            <form method="post" action="<?= e(url('pulsa/productDestroy/' . $p['id'])) ?>" data-confirm="Hapus produk ini?">
                                <?= Csrf::field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
