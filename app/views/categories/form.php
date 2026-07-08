<?php $isEdit = $item !== null; ?>
<div class="content-header">
    <h1><?= $isEdit ? 'Ubah Kategori' : 'Tambah Kategori' ?></h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url($isEdit ? 'categories/update/' . $item['id'] : 'categories/store')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-group">
                <label>Nama Kategori <span class="req">*</span></label>
                <input type="text" name="name" value="<?= $isEdit ? e($item['name']) : old('name') ?>" required maxlength="100">
                <?= form_error('name') ?>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description"><?= $isEdit ? e($item['description']) : old('description') ?></textarea>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn btn-secondary" href="<?= e(url('categories/index')) ?>">Batal</a>
        </div>
    </form>
</div>
