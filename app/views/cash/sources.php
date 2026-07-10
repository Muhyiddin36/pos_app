<?php $typeLabel = ['cash' => 'Kas Tunai', 'bank' => 'Bank', 'ewallet' => 'E-Wallet', 'other' => 'Lainnya']; ?>
<div class="content-header">
    <div>
        <h1>Sumber Dana Saldo Kas</h1>
        <p class="text-muted mb-0">Keterangan sumber dana dan saldo awal hanya dapat diatur oleh Super Admin.</p>
    </div>
    <a class="btn" href="<?= e(url('cash/sourceCreate')) ?>">+ Tambah Sumber Dana</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Cabang</th><th>Nama / Keterangan</th><th>Tipe</th><th class="text-right">Saldo Awal</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($sources)): ?>
                <tr><td colspan="6" class="empty-state">Belum ada sumber dana.</td></tr>
            <?php endif; ?>
            <?php foreach ($sources as $s): ?>
                <tr>
                    <td><?= e($s['branch_name']) ?></td>
                    <td><?= e($s['name']) ?></td>
                    <td><span class="badge badge-gray"><?= e($typeLabel[$s['type']] ?? $s['type']) ?></span></td>
                    <td class="text-right"><?= rupiah($s['opening_balance']) ?></td>
                    <td><?= $s['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" href="<?= e(url('cash/sourceEdit/' . $s['id'])) ?>">Ubah</a>
                            <form method="post" action="<?= e(url('cash/sourceDestroy/' . $s['id'])) ?>" data-confirm="Hapus sumber dana ini? Riwayat saldo yang sudah tercatat tidak akan terhapus.">
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
