<?php $isEdit = $item !== null; ?>
<div class="content-header">
    <h1><?= $isEdit ? 'Ubah Pengguna' : 'Tambah Pengguna' ?></h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url($isEdit ? 'users/update/' . $item['id'] : 'users/store')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Username <span class="req">*</span></label>
                    <input type="text" name="username" value="<?= $isEdit ? e($item['username']) : old('username') ?>" required maxlength="50">
                    <?= form_error('username') ?>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="full_name" value="<?= $isEdit ? e($item['full_name']) : old('full_name') ?>" required maxlength="100">
                    <?= form_error('full_name') ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password <?= $isEdit ? '' : '<span class="req">*</span>' ?></label>
                    <input type="password" name="password" <?= $isEdit ? '' : 'required' ?>>
                    <?php if ($isEdit): ?><p class="form-hint">Kosongkan jika tidak ingin mengubah password.</p><?php endif; ?>
                    <?= form_error('password') ?>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $isEdit ? e($item['email']) : old('email') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Role <span class="req">*</span></label>
                    <select name="role_id" required>
                        <option value="">-- Pilih Role --</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= (int) $r['id'] ?>" <?= $isEdit && (int) $item['role_id'] === (int) $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('role_id') ?>
                </div>
                <div class="form-group">
                    <label>Cabang</label>
                    <select name="branch_id">
                        <option value="">Semua Cabang (Super Admin)</option>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= (int) $b['id'] ?>" <?= $isEdit && (int) ($item['branch_id'] ?? 0) === (int) $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label style="font-weight:400;"><input type="checkbox" name="is_active" value="1" <?= (!$isEdit || $item['is_active']) ? 'checked' : '' ?> style="width:auto;display:inline-block;"> Aktif</label>
            </div>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn btn-secondary" href="<?= e(url('users/index')) ?>">Batal</a>
        </div>
    </form>
</div>
