<?php $isEdit = $item !== null; ?>
<div class="content-header">
    <h1><?= $isEdit ? 'Ubah Bank' : 'Tambah Bank' ?></h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url($isEdit ? 'bank/bankUpdate/' . $item['id'] : 'bank/bankStore')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Kode Bank <span class="req">*</span></label>
                    <input type="text" name="code" value="<?= $isEdit ? e($item['code']) : old('code') ?>" required maxlength="20">
                    <?= form_error('code') ?>
                </div>
                <div class="form-group">
                    <label>Nama Bank <span class="req">*</span></label>
                    <input type="text" name="name" value="<?= $isEdit ? e($item['name']) : old('name') ?>" required maxlength="100">
                    <?= form_error('name') ?>
                </div>
            </div>
            <div class="form-group">
                <label style="font-weight:400;"><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || $item['is_active']) ? 'checked' : '' ?> style="width:auto;display:inline-block;"> Aktif</label>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn btn-secondary" href="<?= e(url('bank/banks')) ?>">Batal</a>
        </div>
    </form>
</div>
