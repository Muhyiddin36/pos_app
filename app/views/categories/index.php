<div class="content-header">
    <div>
        <h1>Kategori Produk</h1>
        <p class="text-muted mb-0">Pengelompokan produk aksesoris HP.</p>
    </div>
    <a class="btn" href="<?= e(url('categories/create')) ?>">+ Tambah Kategori</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama Kategori</th><th>Deskripsi</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($categories)): ?>
                <tr><td colspan="3" class="empty-state">Belum ada kategori.</td></tr>
            <?php endif; ?>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= e($c['name']) ?></td>
                    <td class="text-muted"><?= e($c['description'] ?? '-') ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('categories/edit/' . $c['id'])) ?>">Ubah</a>
                            <form method="post" action="<?= e(url('categories/destroy/' . $c['id'])) ?>" data-confirm="Hapus kategori ini?">
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
