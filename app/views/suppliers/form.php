<?php $isEdit = $item !== null; ?>
<div class="content-header">
    <h1><?= $isEdit ? 'Ubah Supplier' : 'Tambah Supplier' ?></h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url($isEdit ? 'suppliers/update/' . $item['id'] : 'suppliers/store')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Supplier <span class="req">*</span></label>
                    <input type="text" name="name" value="<?= $isEdit ? e($item['name']) : old('name') ?>" required maxlength="150">
                    <?= form_error('name') ?>
                </div>
                <div class="form-group">
                    <label>Nama Kontak</label>
                    <input type="text" name="contact_person" value="<?= $isEdit ? e($item['contact_person']) : old('contact_person') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" name="phone" value="<?= $isEdit ? e($item['phone']) : old('phone') ?>">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="address" value="<?= $isEdit ? e($item['address']) : old('address') ?>">
                </div>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn btn-secondary" href="<?= e(url('suppliers/index')) ?>">Batal</a>
        </div>
    </form>
</div>
