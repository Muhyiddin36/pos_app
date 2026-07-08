<div class="content-header">
    <div>
        <h1>Role & Hak Akses</h1>
        <p class="text-muted mb-0">Role sistem bersifat tetap (Super Admin, Admin Cabang, Kasir); hak akses tiap role dapat disesuaikan.</p>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Role</th><th>Deskripsi</th><th class="text-right">Jumlah Hak Akses</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($roles as $r): ?>
                <tr>
                    <td><strong><?= e($r['name']) ?></strong></td>
                    <td class="text-muted"><?= e($r['description'] ?? '-') ?></td>
                    <td class="text-right"><?= (int) $r['permission_count'] ?></td>
                    <td class="text-right">
                        <?php if ($r['slug'] === 'super_admin'): ?>
                            <span class="badge badge-gray">Selalu Penuh</span>
                        <?php else: ?>
                            <a class="btn btn-sm btn-outline" href="<?= e(url('roles/edit/' . $r['id'])) ?>">Kelola Hak Akses</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
