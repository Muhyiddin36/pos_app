<div class="content-header">
    <div>
        <h1>Transfer / Setor Tunai</h1>
        <p class="text-muted mb-0">Layanan agen bank: transfer antar rekening, setor tunai, dan tarik tunai untuk semua bank.</p>
    </div>
    <?php if (Auth::can('bank.manage')): ?>
        <a class="btn btn-outline" href="<?= e(url('bank/banks')) ?>">Kelola Daftar Bank</a>
    <?php endif; ?>
</div>

<?php if (Auth::can('bank.create')): ?>
<div class="card">
    <div class="card-header"><h3>Transaksi Baru</h3></div>
    <form method="post" action="<?= e(url('bank/store')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Jenis Transaksi</label>
                    <select name="transaction_type">
                        <option value="transfer">Transfer ke Rekening Lain</option>
                        <option value="setor_tunai">Setor Tunai</option>
                        <option value="tarik_tunai">Tarik Tunai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Bank</label>
                    <select name="bank_id" required>
                        <option value="">-- Pilih Bank --</option>
                        <?php foreach ($banks as $b): ?>
                            <option value="<?= (int) $b['id'] ?>"><?= e($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>No. Rekening Tujuan</label>
                    <input type="text" name="account_number">
                </div>
                <div class="form-group">
                    <label>Nama Pemilik Rekening</label>
                    <input type="text" name="account_name">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>No. HP Pelanggan</label>
                    <input type="text" name="customer_phone">
                </div>
                <div class="form-group"></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nominal Uang (Rp)</label>
                    <input type="number" step="0.01" min="0" name="amount" required id="bank-amount">
                </div>
                <div class="form-group">
                    <label>Biaya Jasa / Admin (Rp)</label>
                    <input type="number" step="0.01" min="0" name="admin_fee" value="<?= e($defaultFee) ?>" id="bank-fee">
                </div>
            </div>
            <input type="hidden" name="cost_fee" value="0">
            <div class="form-group">
                <label>Catatan</label>
                <input type="text" name="note">
            </div>
            <div class="pos-summary-row total"><span>Total Diterima dari Pelanggan</span><strong id="bank-total">Rp 0</strong></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn">Proses &amp; Cetak Struk</button>
        </div>
    </form>
</div>
<script>
(function () {
    var amount = document.getElementById('bank-amount');
    var fee = document.getElementById('bank-fee');
    var total = document.getElementById('bank-total');
    function recalc() {
        var t = (parseFloat(amount.value) || 0) + (parseFloat(fee.value) || 0);
        total.textContent = window.POS.formatRupiah(t);
    }
    amount.addEventListener('input', recalc);
    fee.addEventListener('input', recalc);
})();
</script>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="get" action="<?= e(url('bank/index')) ?>" class="filter-bar mb-0">
            <div class="form-group"><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($from) ?>"></div>
            <div class="form-group"><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($to) ?>"></div>
            <div class="form-group"><button class="btn btn-outline" type="submit">Filter</button></div>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Transaksi</th><th>Tanggal</th><th>Jenis</th><th>Bank</th><th>Tujuan</th><th class="text-right">Nominal</th><th class="text-right">Biaya</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="9" class="empty-state">Belum ada transaksi.</td></tr>
            <?php endif; ?>
            <?php $typeLabel = ['transfer' => 'Transfer', 'setor_tunai' => 'Setor Tunai', 'tarik_tunai' => 'Tarik Tunai']; ?>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= e($t['trx_no']) ?></td>
                    <td><?= e(date('d-m-Y H:i', strtotime($t['created_at']))) ?></td>
                    <td><span class="badge badge-gray"><?= e($typeLabel[$t['transaction_type']] ?? $t['transaction_type']) ?></span></td>
                    <td><?= e($t['bank_name']) ?></td>
                    <td><?= e($t['account_name'] ?: '-') ?></td>
                    <td class="text-right"><?= rupiah($t['amount']) ?></td>
                    <td class="text-right"><?= rupiah($t['admin_fee']) ?></td>
                    <td>
                        <?php if ($t['status'] === 'void'): ?>
                            <span class="badge badge-danger">Dibatalkan</span>
                        <?php else: ?>
                            <span class="badge badge-success">Berhasil</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content:flex-end;">
                            <a class="btn btn-sm btn-outline" target="_blank" href="<?= e(url('bank/print/' . $t['id'])) ?>">Cetak</a>
                            <?php if ($t['status'] !== 'void' && Auth::can('bank.void')): ?>
                                <button type="button" class="btn btn-sm btn-danger" data-modal-open="void-modal-<?= (int) $t['id'] ?>">Batalkan</button>
                                <div class="modal-backdrop" id="void-modal-<?= (int) $t['id'] ?>">
                                    <div class="modal">
                                        <form method="post" action="<?= e(url('bank/void/' . $t['id'])) ?>">
                                            <?= Csrf::field() ?>
                                            <div class="modal-header"><h3>Batalkan Transaksi</h3><button type="button" class="btn btn-sm btn-icon" data-modal-close>&times;</button></div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Alasan Pembatalan</label>
                                                    <input type="text" name="reason" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer"><button type="submit" class="btn btn-danger">Batalkan</button></div>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
