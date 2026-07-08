<?php $isEdit = $item !== null; ?>
<div class="content-header">
    <h1><?= $isEdit ? 'Ubah Produk' : 'Tambah Produk' ?></h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url($isEdit ? 'products/update/' . $item['id'] : 'products/store')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <?php if (Auth::isSuperAdmin()): ?>
            <div class="form-group">
                <label>Cabang <span class="req">*</span></label>
                <select name="branch_id" required <?= $isEdit ? 'disabled' : '' ?>>
                    <option value="">-- Pilih Cabang --</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= (int) $b['id'] ?>" <?= $isEdit && (int) $item['branch_id'] === (int) $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($isEdit): ?><input type="hidden" name="branch_id" value="<?= (int) $item['branch_id'] ?>"><?php endif; ?>
                <?= form_error('branch_id') ?>
            </div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>SKU <span class="req">*</span></label>
                    <input type="text" name="sku" value="<?= $isEdit ? e($item['sku']) : old('sku') ?>" required maxlength="50">
                    <?= form_error('sku') ?>
                </div>
                <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" name="barcode" value="<?= $isEdit ? e($item['barcode']) : old('barcode') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Nama Produk <span class="req">*</span></label>
                <input type="text" name="name" value="<?= $isEdit ? e($item['name']) : old('name') ?>" required maxlength="150">
                <?= form_error('name') ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category_id">
                        <option value="">-- Tanpa Kategori --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= $isEdit && (int) ($item['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text" name="unit" value="<?= $isEdit ? e($item['unit']) : (old('unit') ?: 'pcs') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Harga Beli <span class="req">*</span></label>
                    <input type="number" step="0.01" min="0" name="purchase_price" value="<?= $isEdit ? e((string) $item['purchase_price']) : old('purchase_price') ?>" required>
                    <?= form_error('purchase_price') ?>
                </div>
                <div class="form-group">
                    <label>Harga Jual <span class="req">*</span></label>
                    <input type="number" step="0.01" min="0" name="sale_price" value="<?= $isEdit ? e((string) $item['sale_price']) : old('sale_price') ?>" required>
                    <?= form_error('sale_price') ?>
                </div>
            </div>

            <div class="form-row">
                <?php if (!$isEdit): ?>
                <div class="form-group">
                    <label>Stok Awal</label>
                    <input type="number" min="0" name="stock_qty" value="<?= old('stock_qty', '0') ?>">
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Stok Minimum</label>
                    <input type="number" min="0" name="min_stock" value="<?= $isEdit ? e((string) $item['min_stock']) : (old('min_stock') ?: '0') ?>">
                </div>
            </div>

            <div class="form-group">
                <label style="font-weight:400;"><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || $item['is_active']) ? 'checked' : '' ?> style="width:auto;display:inline-block;"> Aktif dijual</label>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn btn-secondary" href="<?= e(url('products/index')) ?>">Batal</a>
        </div>
    </form>
</div>

<?php if ($isEdit): ?>
<div class="card">
    <div class="card-header"><h3>Penyesuaian Stok</h3></div>
    <div class="card-body">
        <form method="post" action="<?= e(url('products/adjustStock/' . $item['id'])) ?>" class="form-row" style="align-items:end;">
            <?= Csrf::field() ?>
            <div class="form-group">
                <label>Jumlah (+/-)</label>
                <input type="number" name="qty" required placeholder="mis. 5 atau -3">
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <input type="text" name="note" placeholder="Alasan penyesuaian">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-warning">Sesuaikan Stok</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Riwayat Pergerakan Stok</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Tipe</th><th class="text-right">Qty</th><th>Catatan</th><th>Oleh</th></tr></thead>
            <tbody>
            <?php if (empty($stockHistory)): ?>
                <tr><td colspan="5" class="empty-state">Belum ada riwayat.</td></tr>
            <?php endif; ?>
            <?php foreach ($stockHistory as $h): ?>
                <tr>
                    <td><?= e(date('d-m-Y H:i', strtotime($h['created_at']))) ?></td>
                    <td><span class="badge badge-gray"><?= e($h['type']) ?></span></td>
                    <td class="text-right"><?= (int) $h['qty'] > 0 ? '+' : '' ?><?= (int) $h['qty'] ?></td>
                    <td><?= e($h['note'] ?? '-') ?></td>
                    <td><?= e($h['created_by_name'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
