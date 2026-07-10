<?php $isEdit = $item !== null; ?>
<div class="content-header">
    <h1><?= $isEdit ? 'Ubah Sumber Dana' : 'Tambah Sumber Dana' ?></h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url($isEdit ? 'cash/sourceUpdate/' . $item['id'] : 'cash/sourceStore')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-group">
                <label>Cabang <span class="req">*</span></label>
                <select name="branch_id" required <?= $isEdit ? 'disabled' : '' ?>>
                    <option value="">-- Pilih Cabang --</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= (int) $b['id'] ?>" <?= $isEdit && (int) $item['branch_id'] === (int) $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($isEdit): ?><input type="hidden" name="branch_id" value="<?= (int) $item['branch_id'] ?>"><?php endif; ?>
                <p class="form-hint">Cabang tidak dapat diubah setelah sumber dana dibuat.</p>
                <?= form_error('branch_id') ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nama / Keterangan Sumber Dana <span class="req">*</span></label>
                    <input type="text" name="name" value="<?= $isEdit ? e($item['name']) : old('name') ?>" required maxlength="100" placeholder="mis. Kas Tunai, Saldo Bank BCA, Gopay Merchant">
                    <?= form_error('name') ?>
                </div>
                <div class="form-group">
                    <label>Tipe</label>
                    <?php $type = $isEdit ? $item['type'] : old('type', 'cash'); ?>
                    <select name="type">
                        <option value="cash" <?= $type === 'cash' ? 'selected' : '' ?>>Kas Tunai</option>
                        <option value="bank" <?= $type === 'bank' ? 'selected' : '' ?>>Bank</option>
                        <option value="ewallet" <?= $type === 'ewallet' ? 'selected' : '' ?>>E-Wallet</option>
                        <option value="other" <?= $type === 'other' ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Saldo Awal</label>
                <input type="number" step="0.01" min="0" name="opening_balance" value="<?= $isEdit ? e((string) $item['opening_balance']) : (old('opening_balance') ?: '0') ?>">
                <p class="form-hint">Hanya Super Admin yang dapat mengatur saldo awal. Kasir/Admin Cabang hanya mencatat saldo akhir harian.</p>
            </div>

            <div class="form-group">
                <label style="font-weight:400;"><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || $item['is_active']) ? 'checked' : '' ?> style="width:auto;display:inline-block;"> Aktif</label>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn btn-secondary" href="<?= e(url('cash/sources')) ?>">Batal</a>
        </div>
    </form>
</div>
