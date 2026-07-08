<div class="content-header">
    <div>
        <h1>Supplier</h1>
        <p class="text-muted mb-0">Data pemasok barang untuk kebutuhan pembelian stok.</p>
    </div>
    <a class="btn" href="<?= e(url('suppliers/create')) ?>">+ Tambah Supplier</a>
</div>

<div class="card">
    <div class="card-header">
        <input type="search" placeholder="Cari supplier..." data-table-search="#tbl-suppliers" style="max-width:260px;">
    </div>
    <div class="table-wrap">
        <table id="tbl-suppliers">
            <thead><tr><th>Nama Supplier</th><th>Kontak</th><th>Telepon</th><th>Alamat</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($suppliers)): ?>
                <tr><td colspan="5" class="empty-state">Belum ada supplier.</td></tr>
            <?php endif; ?>
            <?php foreach ($suppliers as $s): ?>
                <tr>
                    <td><?= e($s['name']) ?></td>
                    <td><?= e($s['contact_person'] ?? '-') ?></td>
                    <td><?= e($s['phone'] ?? '-') ?></td>
                    <td class="text-muted"><?= e($s['address'] ?? '-') ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('suppliers/edit/' . $s['id'])) ?>">Ubah</a>
                            <form method="post" action="<?= e(url('suppliers/destroy/' . $s['id'])) ?>" data-confirm="Hapus supplier ini?">
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
