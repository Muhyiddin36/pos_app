<?php $badge = ['active' => 'badge-info', 'redeemed' => 'badge-success', 'overdue' => 'badge-danger', 'auctioned' => 'badge-gray'][$pawn['status']] ?? 'badge-gray'; ?>
<div class="content-header">
    <h1>Gadai <?= e($pawn['pawn_no']) ?> <span class="badge <?= $badge ?>"><?= e($pawn['status']) ?></span></h1>
    <div class="flex gap-sm">
        <a class="btn btn-outline" target="_blank" href="<?= e(url('pawn/print/' . $pawn['id'])) ?>">Cetak Surat Gadai</a>
        <a class="btn btn-secondary" href="<?= e(url('pawn/index')) ?>">Kembali</a>
    </div>
</div>

<div class="grid grid-cols-2">
    <div class="card">
        <div class="card-header"><h3>Informasi Gadai</h3></div>
        <div class="card-body">
            <p><strong>Pelanggan:</strong> <?= e($pawn['customer_name']) ?> (<?= e($pawn['customer_phone'] ?? '-') ?>)</p>
            <p><strong>No. KTP:</strong> <?= e($pawn['id_card_number'] ?? '-') ?></p>
            <p><strong>Barang:</strong> <?= e($pawn['item_name']) ?></p>
            <p><strong>Deskripsi:</strong> <?= e($pawn['item_description'] ?? '-') ?></p>
            <p><strong>Taksiran Nilai:</strong> <?= rupiah($pawn['estimated_value']) ?></p>
            <p><strong>Pinjaman:</strong> <?= rupiah($pawn['loan_amount']) ?></p>
            <p><strong>Bunga:</strong> <?= e($pawn['interest_rate']) ?>% / bulan</p>
            <p><strong>Tanggal Gadai:</strong> <?= tgl($pawn['pawn_date']) ?></p>
            <p><strong>Jatuh Tempo:</strong> <?= tgl($pawn['due_date']) ?></p>
        </div>
    </div>

    <?php if ($pawn['status'] === 'active' && Auth::can('pawn.manage')): ?>
    <div class="card">
        <div class="card-header"><h3>Bayar Bunga / Perpanjang</h3></div>
        <form method="post" action="<?= e(url('pawn/pay/' . $pawn['id'])) ?>">
            <?= Csrf::field() ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Jenis Pembayaran</label>
                    <select name="type">
                        <option value="interest">Bayar Bunga</option>
                        <option value="extension">Perpanjangan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah Bayar</label>
                    <input type="number" step="0.01" name="amount" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Jatuh Tempo Baru (khusus perpanjangan)</label>
                    <input type="date" name="new_due_date">
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <input type="text" name="note">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn">Simpan Pembayaran</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h3>Tebus Barang</h3></div>
        <form method="post" action="<?= e(url('pawn/redeem/' . $pawn['id'])) ?>" data-confirm="Yakin barang sudah ditebus penuh?">
            <?= Csrf::field() ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Jumlah Tebusan</label>
                    <input type="number" step="0.01" name="amount" required value="<?= (float) $pawn['loan_amount'] ?>">
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <input type="text" name="note">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">Tandai Sudah Ditebus</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header"><h3>Riwayat Pembayaran</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Jenis</th><th class="text-right">Jumlah</th><th>Catatan</th><th>Oleh</th></tr></thead>
            <tbody>
            <?php if (empty($payments)): ?>
                <tr><td colspan="5" class="empty-state">Belum ada pembayaran.</td></tr>
            <?php endif; ?>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= tgl($p['payment_date']) ?></td>
                    <td><span class="badge badge-gray"><?= e($p['type']) ?></span></td>
                    <td class="text-right"><?= rupiah($p['amount']) ?></td>
                    <td><?= e($p['note'] ?? '-') ?></td>
                    <td><?= e($p['created_by_name'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
