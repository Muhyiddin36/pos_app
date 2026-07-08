<div class="content-header">
    <h1>Gadai Baru</h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url('pawn/store')) ?>">
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

            <div class="form-group">
                <label>Nama Barang <span class="req">*</span></label>
                <input type="text" name="item_name" required maxlength="150" value="<?= old('item_name') ?>">
                <?= form_error('item_name') ?>
            </div>
            <div class="form-group">
                <label>Deskripsi Barang</label>
                <textarea name="item_description"><?= old('item_description') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Taksiran Nilai Barang</label>
                    <input type="number" step="0.01" name="estimated_value" value="<?= old('estimated_value') ?>">
                </div>
                <div class="form-group">
                    <label>Jumlah Pinjaman <span class="req">*</span></label>
                    <input type="number" step="0.01" name="loan_amount" required value="<?= old('loan_amount') ?>">
                    <?= form_error('loan_amount') ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Bunga (% per bulan)</label>
                    <input type="number" step="0.01" name="interest_rate" value="<?= old('interest_rate') ?: e($defaultRate) ?>">
                </div>
                <div class="form-group">
                    <label>Tanggal Gadai</label>
                    <input type="date" name="pawn_date" value="<?= e(date('Y-m-d')) ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Jatuh Tempo <span class="req">*</span></label>
                <input type="date" name="due_date" required value="<?= old('due_date') ?>">
                <?= form_error('due_date') ?>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan Gadai</button>
            <a class="btn btn-secondary" href="<?= e(url('pawn/index')) ?>">Batal</a>
        </div>
    </form>
</div>
