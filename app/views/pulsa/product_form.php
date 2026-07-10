<?php $isEdit = $item !== null; ?>
<div class="content-header">
    <h1><?= $isEdit ? 'Ubah Produk Pulsa' : 'Tambah Produk Pulsa' ?></h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url($isEdit ? 'pulsa/productUpdate/' . $item['id'] : 'pulsa/productStore')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Kategori <span class="req">*</span></label>
                    <select name="category" required>
                        <?php $cat = $isEdit ? $item['category'] : old('category', 'pulsa'); ?>
                        <option value="pulsa" <?= $cat === 'pulsa' ? 'selected' : '' ?>>Pulsa</option>
                        <option value="paket_data" <?= $cat === 'paket_data' ? 'selected' : '' ?>>Paket Data</option>
                        <option value="pln" <?= $cat === 'pln' ? 'selected' : '' ?>>Token PLN</option>
                        <option value="ewallet" <?= $cat === 'ewallet' ? 'selected' : '' ?>>Top Up Saldo E-Wallet</option>
                        <option value="other" <?= $cat === 'other' ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Provider <span class="req">*</span></label>
                    <input type="text" name="provider" value="<?= $isEdit ? e($item['provider']) : old('provider') ?>" required maxlength="50">
                    <?= form_error('provider') ?>
                </div>
            </div>
            <div class="form-group">
                <label>Nama Produk <span class="req">*</span></label>
                <input type="text" name="name" value="<?= $isEdit ? e($item['name']) : old('name') ?>" required maxlength="100">
                <?= form_error('name') ?>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nominal</label>
                    <input type="number" step="0.01" name="nominal" value="<?= $isEdit ? e((string) $item['nominal']) : old('nominal') ?>">
                </div>
                <div class="form-group"></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Harga Modal <span class="req">*</span></label>
                    <input type="number" step="0.01" name="cost_price" value="<?= $isEdit ? e((string) $item['cost_price']) : old('cost_price') ?>" required>
                    <?= form_error('cost_price') ?>
                </div>
                <div class="form-group">
                    <label>Harga Jual <span class="req">*</span></label>
                    <input type="number" step="0.01" name="sale_price" value="<?= $isEdit ? e((string) $item['sale_price']) : old('sale_price') ?>" required>
                    <?= form_error('sale_price') ?>
                </div>
            </div>
            <div class="form-group">
                <label style="font-weight:400;"><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || $item['is_active']) ? 'checked' : '' ?> style="width:auto;display:inline-block;"> Aktif</label>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn btn-secondary" href="<?= e(url('pulsa/products')) ?>">Batal</a>
        </div>
    </form>
</div>
