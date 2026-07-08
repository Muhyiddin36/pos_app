<div class="content-header">
    <h1>Backup & Restore Database</h1>
</div>

<div class="alert alert-warning">
    Fitur ini bekerja murni dengan PHP native (tanpa <code>mysqldump</code>/SSH) agar kompatibel dengan shared hosting.
    Proses restore akan <strong>menimpa seluruh data yang ada saat ini</strong>. Pastikan Anda membuat backup terbaru sebelum melakukan restore.
</div>

<div class="card">
    <div class="card-header"><h3>Buat Backup Baru</h3></div>
    <div class="card-body">
        <form method="post" action="<?= e(url('backup/create')) ?>" data-confirm="Buat backup database sekarang?">
            <?= Csrf::field() ?>
            <button type="submit" class="btn">Buat Backup Sekarang</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Restore dari File Unggahan</h3></div>
    <div class="card-body">
        <form method="post" action="<?= e(url('backup/restoreUpload')) ?>" enctype="multipart/form-data" data-confirm="Restore akan menimpa seluruh data saat ini. Lanjutkan?">
            <?= Csrf::field() ?>
            <div class="form-group">
                <label>File Backup (.sql)</label>
                <input type="file" name="backup_file" accept=".sql" required>
            </div>
            <button type="submit" class="btn btn-danger">Restore dari File</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Daftar Backup Tersimpan</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama File</th><th>Ukuran</th><th>Dibuat</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($backups)): ?>
                <tr><td colspan="4" class="empty-state">Belum ada backup tersimpan.</td></tr>
            <?php endif; ?>
            <?php foreach ($backups as $b): ?>
                <tr>
                    <td><?= e($b['name']) ?></td>
                    <td><?= e(round($b['size'] / 1024, 1)) ?> KB</td>
                    <td><?= e(date('d-m-Y H:i', $b['created_at'])) ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('backup/download/' . $b['name'])) ?>">Unduh</a>
                            <form method="post" action="<?= e(url('backup/restore/' . $b['name'])) ?>" data-confirm="Restore dari file ini akan menimpa seluruh data saat ini. Lanjutkan?">
                                <?= Csrf::field() ?>
                                <button type="submit" class="btn btn-sm btn-warning">Restore</button>
                            </form>
                            <form method="post" action="<?= e(url('backup/destroy/' . $b['name'])) ?>" data-confirm="Hapus file backup ini?">
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
