<div class="content-header">
    <div>
        <h1>Daftar Bank</h1>
        <p class="text-muted mb-0">Master data bank untuk transaksi transfer, setor, dan tarik tunai.</p>
    </div>
    <a class="btn" href="<?= e(url('bank/bankCreate')) ?>">+ Tambah Bank</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama Bank</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($banks)): ?>
                <tr><td colspan="4" class="empty-state">Belum ada data bank.</td></tr>
            <?php endif; ?>
            <?php foreach ($banks as $b): ?>
                <tr>
                    <td><?= e($b['code']) ?></td>
                    <td><?= e($b['name']) ?></td>
                    <td><?= $b['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('bank/bankEdit/' . $b['id'])) ?>">Ubah</a>
                            <form method="post" action="<?= e(url('bank/bankDestroy/' . $b['id'])) ?>" data-confirm="Hapus bank ini?">
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
