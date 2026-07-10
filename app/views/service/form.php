<div class="content-header">
    <h1>Terima Servis Baru</h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url('service/store')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-group">
                <label>Pelanggan <span class="req">*</span></label>
                <select name="customer_id" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?> <?= $c['phone'] ? '(' . e($c['phone']) . ')' : '' ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="form-hint">Belum terdaftar? <a href="<?= e(url('customers/create')) ?>" target="_blank">Tambah pelanggan baru</a>.</p>
                <?= form_error('customer_id') ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Merk / Tipe HP <span class="req">*</span></label>
                    <input type="text" name="device_type" required maxlength="100" placeholder="mis. Samsung A10" value="<?= old('device_type') ?>">
                    <?= form_error('device_type') ?>
                </div>
                <div class="form-group">
                    <label>IMEI / Serial (opsional)</label>
                    <input type="text" name="device_imei" value="<?= old('device_imei') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Keluhan Pelanggan <span class="req">*</span></label>
                <textarea name="issue_description" required placeholder="mis. Layar retak, tidak bisa charge, dll"><?= old('issue_description') ?></textarea>
                <?= form_error('issue_description') ?>
            </div>
            <div class="form-group">
                <label>Kelengkapan yang Dititipkan</label>
                <input type="text" name="accessories_note" placeholder="mis. Charger, sim card, dus">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Perkiraan Biaya</label>
                    <input type="number" step="0.01" min="0" name="estimated_cost" value="<?= old('estimated_cost') ?: '0' ?>">
                </div>
                <div class="form-group">
                    <label>Estimasi Selesai</label>
                    <input type="date" name="estimated_finish_date">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Terima</label>
                    <input type="date" name="received_date" value="<?= e(date('Y-m-d')) ?>">
                </div>
                <div class="form-group"></div>
            </div>

            <hr>
            <div class="form-row">
                <div class="form-group">
                    <label>Uang Muka (DP)</label>
                    <input type="number" step="0.01" min="0" name="down_payment" value="0">
                </div>
                <div class="form-group">
                    <label>Metode Bayar DP</label>
                    <select name="payment_method">
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                        <option value="debit">Debit/Kredit</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan Servis</button>
            <a class="btn btn-secondary" href="<?= e(url('service/index')) ?>">Batal</a>
        </div>
    </form>
</div>
