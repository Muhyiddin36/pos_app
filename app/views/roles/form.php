<div class="content-header">
    <h1>Hak Akses: <?= e($role['name']) ?></h1>
</div>

<form method="post" action="<?= e(url('roles/update/' . $role['id'])) ?>">
    <?= Csrf::field() ?>
    <div class="card">
        <div class="card-body">
            <?php foreach ($permissionGroups as $module => $permissions): ?>
                <h3 style="text-transform:capitalize;"><?= e($module) ?></h3>
                <div class="grid grid-cols-3" style="margin-bottom:1.25rem;">
                    <?php foreach ($permissions as $p): ?>
                        <label style="font-weight:400;display:flex;gap:.4rem;align-items:center;">
                            <input type="checkbox" name="permissions[]" value="<?= (int) $p['id'] ?>" style="width:auto;"
                                <?= in_array($p['code'], $assignedCodes, true) ? 'checked' : '' ?>>
                            <?= e($p['name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="card-footer flex gap-sm">
            <button type="submit" class="btn">Simpan Hak Akses</button>
            <a class="btn btn-secondary" href="<?= e(url('roles/index')) ?>">Batal</a>
        </div>
    </div>
</form>
