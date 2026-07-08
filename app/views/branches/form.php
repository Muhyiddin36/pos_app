<?php $isEdit = $item !== null; ?>
<div class="content-header">
    <h1><?= $isEdit ? 'Ubah Cabang' : 'Tambah Cabang' ?></h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url($isEdit ? 'branches/update/' . $item['id'] : 'branches/store')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Kode Cabang <span class="req">*</span></label>
                    <input type="text" name="code" value="<?= $isEdit ? e($item['code']) : old('code') ?>" required maxlength="20">
                    <?= form_error('code') ?>
                </div>
                <div class="form-group">
                    <label>Nama Cabang <span class="req">*</span></label>
                    <input type="text" name="name" value="<?= $isEdit ? e($item['name']) : old('name') ?>" required maxlength="100">
                    <?= form_error('name') ?>
                </div>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address"><?= $isEdit ? e($item['address']) : old('address') ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" name="phone" value="<?= $isEdit ? e($item['phone']) : old('phone') ?>">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <label style="font-weight:400;"><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || $item['is_active']) ? 'checked' : '' ?> style="width:auto;display:inline-block;"> Aktif</label>
                </div>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn btn-secondary" href="<?= e(url('branches/index')) ?>">Batal</a>
        </div>
    </form>
</div>
