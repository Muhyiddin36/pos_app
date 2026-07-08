<div class="content-header">
    <div>
        <h1>Pengguna</h1>
        <p class="text-muted mb-0">Kelola akun pengguna sistem beserta role dan cabangnya.</p>
    </div>
    <a class="btn" href="<?= e(url('users/create')) ?>">+ Tambah Pengguna</a>
</div>

<div class="card">
    <div class="card-header">
        <input type="search" placeholder="Cari pengguna..." data-table-search="#tbl-users" style="max-width:260px;">
    </div>
    <div class="table-wrap">
        <table id="tbl-users">
            <thead>
                <tr><th>Username</th><th>Nama Lengkap</th><th>Role</th><th>Cabang</th><th>Status</th><th class="text-right">Aksi</th></tr>
            </thead>
            <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="6" class="empty-state">Belum ada pengguna.</td></tr>
            <?php endif; ?>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= e($u['username']) ?></td>
                    <td><?= e($u['full_name']) ?></td>
                    <td><span class="badge badge-info"><?= e($u['role_name'] ?? '-') ?></span></td>
                    <td><?= e($u['branch_name'] ?? 'Semua Cabang') ?></td>
                    <td><?= $u['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('users/edit/' . $u['id'])) ?>">Ubah</a>
                            <?php if ((int) $u['id'] !== Auth::id()): ?>
                            <form method="post" action="<?= e(url('users/destroy/' . $u['id'])) ?>" data-confirm="Hapus pengguna ini?">
                                <?= Csrf::field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
