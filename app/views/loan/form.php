<div class="content-header">
    <h1>Pinjaman Baru</h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url('loan/store')) ?>">
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
                    <label>Jumlah Pinjaman <span class="req">*</span></label>
                    <input type="number" step="0.01" name="principal_amount" required value="<?= old('principal_amount') ?>">
                    <?= form_error('principal_amount') ?>
                </div>
                <div class="form-group">
                    <label>Bunga (% per bulan)</label>
                    <input type="number" step="0.01" name="interest_rate" value="<?= old('interest_rate') ?: e($defaultRate) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tenor (bulan) <span class="req">*</span></label>
                    <input type="number" name="term_months" min="1" required value="<?= old('term_months') ?: '1' ?>">
                    <?= form_error('term_months') ?>
                </div>
                <div class="form-group">
                    <label>Tanggal Pinjam</label>
                    <input type="date" name="loan_date" value="<?= e(date('Y-m-d')) ?>">
                </div>
            </div>
            <p class="form-hint">Jadwal angsuran bulanan (pokok rata + bunga tetap) akan dibuat otomatis setelah disimpan.</p>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan Pinjaman</button>
            <a class="btn btn-secondary" href="<?= e(url('loan/index')) ?>">Batal</a>
        </div>
    </form>
</div>
