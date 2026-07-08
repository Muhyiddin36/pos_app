<div class="content-header">
    <div>
        <h1>Pelanggan</h1>
        <p class="text-muted mb-0">Data pelanggan untuk transaksi penjualan, gadai, dan pinjaman.</p>
    </div>
    <a class="btn" href="<?= e(url('customers/create')) ?>">+ Tambah Pelanggan</a>
</div>

<div class="card">
    <div class="card-header">
        <input type="search" placeholder="Cari pelanggan..." data-table-search="#tbl-customers" style="max-width:260px;">
    </div>
    <div class="table-wrap">
        <table id="tbl-customers">
            <thead><tr><th>Nama</th><th>Telepon</th><th>No. KTP</th><th>Alamat</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($customers)): ?>
                <tr><td colspan="5" class="empty-state">Belum ada pelanggan.</td></tr>
            <?php endif; ?>
            <?php foreach ($customers as $c): ?>
                <tr>
                    <td><?= e($c['name']) ?></td>
                    <td><?= e($c['phone'] ?? '-') ?></td>
                    <td><?= e($c['id_card_number'] ?? '-') ?></td>
                    <td class="text-muted"><?= e($c['address'] ?? '-') ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('customers/edit/' . $c['id'])) ?>">Ubah</a>
                            <form method="post" action="<?= e(url('customers/destroy/' . $c['id'])) ?>" data-confirm="Hapus pelanggan ini?">
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
