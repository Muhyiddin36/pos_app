<?php $badge = ['active' => 'badge-info', 'paid_off' => 'badge-success', 'overdue' => 'badge-danger', 'default' => 'badge-danger'][$loan['status']] ?? 'badge-gray'; ?>
<div class="content-header">
    <h1>Pinjaman <?= e($loan['loan_no']) ?> <span class="badge <?= $badge ?>"><?= e($loan['status']) ?></span></h1>
    <div class="flex gap-sm">
        <a class="btn btn-outline" target="_blank" href="<?= e(url('loan/print/' . $loan['id'])) ?>">Cetak</a>
        <a class="btn btn-secondary" href="<?= e(url('loan/index')) ?>">Kembali</a>
    </div>
</div>

<div class="card">
    <div class="card-body grid grid-cols-3">
        <p><strong>Pelanggan:</strong> <?= e($loan['customer_name']) ?> (<?= e($loan['customer_phone'] ?? '-') ?>)</p>
        <p><strong>Pokok Pinjaman:</strong> <?= rupiah($loan['principal_amount']) ?></p>
        <p><strong>Bunga:</strong> <?= e($loan['interest_rate']) ?>% / bulan</p>
        <p><strong>Tenor:</strong> <?= (int) $loan['term_months'] ?> bulan</p>
        <p><strong>Tanggal Pinjam:</strong> <?= tgl($loan['loan_date']) ?></p>
        <p><strong>Jatuh Tempo:</strong> <?= tgl($loan['due_date']) ?></p>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Jadwal Angsuran</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Jatuh Tempo</th><th class="text-right">Pokok</th><th class="text-right">Bunga</th><th class="text-right">Total</th><th class="text-right">Terbayar</th><th>Status</th><?php if (Auth::can('loan.manage')): ?><th class="text-right">Aksi</th><?php endif; ?></tr></thead>
            <tbody>
            <?php foreach ($installments as $inst):
                $ibadge = ['unpaid' => 'badge-gray', 'partial' => 'badge-warning', 'paid' => 'badge-success', 'overdue' => 'badge-danger'][$inst['status']] ?? 'badge-gray'; ?>
                <tr>
                    <td><?= (int) $inst['installment_no'] ?></td>
                    <td><?= tgl($inst['due_date']) ?></td>
                    <td class="text-right"><?= rupiah($inst['principal_amount']) ?></td>
                    <td class="text-right"><?= rupiah($inst['interest_amount']) ?></td>
                    <td class="text-right"><?= rupiah($inst['total_amount']) ?></td>
                    <td class="text-right"><?= rupiah($inst['paid_amount']) ?></td>
                    <td><span class="badge <?= $ibadge ?>"><?= e($inst['status']) ?></span></td>
                    <?php if (Auth::can('loan.manage')): ?>
                    <td class="text-right">
                        <?php if ($inst['status'] !== 'paid'): ?>
                        <button type="button" class="btn btn-sm btn-outline" data-modal-open="pay-modal-<?= (int) $inst['id'] ?>">Bayar</button>
                        <div class="modal-backdrop" id="pay-modal-<?= (int) $inst['id'] ?>">
                            <div class="modal">
                                <form method="post" action="<?= e(url('loan/pay/' . $loan['id'])) ?>">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="installment_id" value="<?= (int) $inst['id'] ?>">
                                    <div class="modal-header"><h3>Bayar Angsuran #<?= (int) $inst['installment_no'] ?></h3><button type="button" class="btn btn-sm btn-icon" data-modal-close>&times;</button></div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Jumlah Bayar</label>
                                            <input type="number" step="0.01" name="amount" required value="<?= (float) $inst['total_amount'] - (float) $inst['paid_amount'] ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Catatan</label>
                                            <input type="text" name="note">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn">Simpan Pembayaran</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Riwayat Pembayaran</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th class="text-right">Jumlah</th><th>Catatan</th><th>Oleh</th></tr></thead>
            <tbody>
            <?php if (empty($payments)): ?>
                <tr><td colspan="4" class="empty-state">Belum ada pembayaran.</td></tr>
            <?php endif; ?>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= tgl($p['payment_date']) ?></td>
                    <td class="text-right"><?= rupiah($p['amount']) ?></td>
                    <td><?= e($p['note'] ?? '-') ?></td>
                    <td><?= e($p['created_by_name'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
