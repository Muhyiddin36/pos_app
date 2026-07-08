<?php $isEdit = $item !== null; ?>
<div class="content-header">
    <h1><?= $isEdit ? 'Ubah Pelanggan' : 'Tambah Pelanggan' ?></h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url($isEdit ? 'customers/update/' . $item['id'] : 'customers/store')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Pelanggan <span class="req">*</span></label>
                    <input type="text" name="name" value="<?= $isEdit ? e($item['name']) : old('name') ?>" required maxlength="150">
                    <?= form_error('name') ?>
                </div>
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="phone" value="<?= $isEdit ? e($item['phone']) : old('phone') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>No. KTP</label>
                    <input type="text" name="id_card_number" value="<?= $isEdit ? e($item['id_card_number']) : old('id_card_number') ?>">
                    <p class="form-hint">Wajib diisi untuk transaksi gadai / pinjaman.</p>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="address" value="<?= $isEdit ? e($item['address']) : old('address') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea name="note"><?= $isEdit ? e($item['note']) : old('note') ?></textarea>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn btn-secondary" href="<?= e(url('customers/index')) ?>">Batal</a>
        </div>
    </form>
</div>
