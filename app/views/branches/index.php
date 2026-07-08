<div class="content-header">
    <div>
        <h1>Cabang</h1>
        <p class="text-muted mb-0">Kelola cabang usaha yang terdaftar dalam sistem.</p>
    </div>
    <a class="btn" href="<?= e(url('branches/create')) ?>">+ Tambah Cabang</a>
</div>

<div class="card">
    <div class="card-header">
        <input type="search" placeholder="Cari cabang..." data-table-search="#tbl-branches" style="max-width:260px;">
    </div>
    <div class="table-wrap">
        <table id="tbl-branches">
            <thead>
                <tr><th>Kode</th><th>Nama Cabang</th><th>Alamat</th><th>Telepon</th><th>Status</th><th class="text-right">Aksi</th></tr>
            </thead>
            <tbody>
            <?php if (empty($branches)): ?>
                <tr><td colspan="6" class="empty-state">Belum ada data cabang.</td></tr>
            <?php endif; ?>
            <?php foreach ($branches as $b): ?>
                <tr>
                    <td><?= e($b['code']) ?></td>
                    <td><?= e($b['name']) ?></td>
                    <td><?= e($b['address'] ?? '-') ?></td>
                    <td><?= e($b['phone'] ?? '-') ?></td>
                    <td><?= $b['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('branches/edit/' . $b['id'])) ?>">Ubah</a>
                            <form method="post" action="<?= e(url('branches/destroy/' . $b['id'])) ?>" data-confirm="Hapus cabang ini?">
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
