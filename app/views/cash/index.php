<?php $typeLabel = ['cash' => 'Kas Tunai', 'bank' => 'Bank', 'ewallet' => 'E-Wallet', 'other' => 'Lainnya']; ?>
<div class="content-header">
    <div>
        <h1>Saldo Kas Harian</h1>
        <p class="text-muted mb-0">Catatan saldo akhir kas tunai, bank, dan e-wallet merchant setiap hari untuk memastikan uang sesuai.</p>
    </div>
    <?php if (Auth::can('cash.manage')): ?>
        <a class="btn btn-outline" href="<?= e(url('cash/sources')) ?>">Kelola Sumber Dana</a>
    <?php endif; ?>
</div>

<?php if (Auth::isSuperAdmin()): ?>
<div class="filter-bar">
    <form method="get" action="<?= e(url('cash/index')) ?>" class="flex gap-sm" style="align-items:end;">
        <div class="form-group">
            <label>Cabang</label>
            <select name="branch_id" onchange="this.form.submit()">
                <option value="">-- Pilih Cabang --</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= (int) $b['id'] ?>" <?= $selectedBranchId === (int) $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Input Saldo Akhir</h3>
        <?php if (Auth::can('cash.correct')): ?>
        <form method="get" action="<?= e(url('cash/index')) ?>" class="flex gap-sm" style="align-items:end;">
            <?php if (Auth::isSuperAdmin() && $selectedBranchId !== null): ?><input type="hidden" name="branch_id" value="<?= (int) $selectedBranchId ?>"><?php endif; ?>
            <div class="form-group mb-0">
                <label>Tanggal</label>
                <input type="date" name="record_date" value="<?= e($date) ?>" onchange="this.form.submit()">
            </div>
        </form>
        <?php else: ?>
            <span class="badge badge-gray">Hari ini, <?= e(tgl($date)) ?></span>
        <?php endif; ?>
    </div>
    <div class="card-body" style="padding-bottom:0;">
        <p class="text-muted mb-0">
            Saldo awal setiap sumber dana berjalan otomatis mengikuti saldo akhir yang disimpan pada hari sebelumnya.
            <?php if (Auth::can('cash.correct')): ?>
                Sebagai Admin Cabang/Super Admin, Anda dapat memperbaiki saldo akhir pada tanggal yang sudah lewat bila terjadi kesalahan input — perbaikan ini otomatis memperbarui saldo awal hari setelahnya.
            <?php else: ?>
                Anda hanya dapat mengisi/mengubah saldo akhir untuk hari ini. Jika ada kesalahan pada tanggal sebelumnya, mintalah Admin Cabang atau Super Admin untuk memperbaikinya.
            <?php endif; ?>
        </p>
    </div>

    <?php if ($selectedBranchId === null): ?>
        <div class="card-body"><p class="empty-state">Silakan pilih cabang terlebih dahulu.</p></div>
    <?php elseif (empty($records)): ?>
        <div class="card-body"><p class="empty-state">Belum ada sumber dana aktif untuk cabang ini. Hubungi Super Admin untuk menambahkannya.</p></div>
    <?php else: ?>
        <form method="post" action="<?= e(url('cash/store')) ?>">
            <?= Csrf::field() ?>
            <input type="hidden" name="record_date" value="<?= e($date) ?>">
            <?php if (Auth::isSuperAdmin()): ?><input type="hidden" name="branch_id" value="<?= (int) $selectedBranchId ?>"><?php endif; ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Sumber Dana</th>
                            <th>Tipe</th>
                            <th class="text-right">Saldo Awal</th>
                            <th class="text-right" style="min-width:160px;">Saldo Akhir (<?= e(tgl($date)) ?>)</th>
                            <th>Terakhir Diperbarui</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?= e($r['name']) ?></td>
                            <td><span class="badge badge-gray"><?= e($typeLabel[$r['type']] ?? $r['type']) ?></span></td>
                            <td class="text-right"><?= rupiah($r['opening_balance']) ?></td>
                            <td class="text-right">
                                <?php if (Auth::can('cash.record')): ?>
                                    <input type="number" step="0.01" min="0" name="closing_balance[<?= (int) $r['cash_source_id'] ?>]"
                                           value="<?= $r['closing_balance'] !== null ? e((string) $r['closing_balance']) : '' ?>"
                                           style="text-align:right;">
                                <?php else: ?>
                                    <?= $r['closing_balance'] !== null ? rupiah($r['closing_balance']) : '-' ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted">
                                <?php if ($r['updated_at']): ?>
                                    <?= e(date('d-m-Y H:i', strtotime($r['updated_at']))) ?> oleh <?= e($r['updated_by_name'] ?? '-') ?>
                                <?php else: ?>
                                    Belum dicatat
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (Auth::can('cash.record')): ?>
            <div class="card-body" style="padding-top:0;">
                <div class="form-group">
                    <label>Catatan (opsional)</label>
                    <input type="text" name="note" placeholder="mis. Ada selisih Rp5.000 karena kembalian belum tercatat">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn">Simpan Saldo Kas</button>
            </div>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header"><h3>Riwayat Saldo Kas</h3></div>
    <div class="card-body">
        <form method="get" action="<?= e(url('cash/index')) ?>" class="filter-bar mb-0">
            <?php if (Auth::isSuperAdmin() && $selectedBranchId !== null): ?><input type="hidden" name="branch_id" value="<?= (int) $selectedBranchId ?>"><?php endif; ?>
            <div class="form-group"><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($from) ?>"></div>
            <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($to) ?>"></div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Filter</button></div>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><?php if (Auth::isSuperAdmin()): ?><th>Cabang</th><?php endif; ?><th>Sumber Dana</th><th class="text-right">Saldo Akhir</th><th>Catatan</th><th>Dicatat Oleh</th></tr></thead>
            <tbody>
            <?php if (empty($history)): ?>
                <tr><td colspan="6" class="empty-state">Belum ada riwayat.</td></tr>
            <?php endif; ?>
            <?php foreach ($history as $h): ?>
                <tr>
                    <td><?= tgl($h['record_date']) ?></td>
                    <?php if (Auth::isSuperAdmin()): ?><td><?= e($h['branch_name']) ?></td><?php endif; ?>
                    <td><?= e($h['source_name']) ?></td>
                    <td class="text-right"><?= rupiah($h['closing_balance']) ?></td>
                    <td class="text-muted"><?= e($h['note'] ?? '-') ?></td>
                    <td><?= e($h['updated_by_name'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
