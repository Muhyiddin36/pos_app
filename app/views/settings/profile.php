<div class="content-header">
    <h1>Profil Saya</h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url('settings/updateProfile')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?= e($user['username']) ?>" disabled>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="full_name" value="<?= e($user['full_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= e($user['email']) ?>">
                </div>
            </div>
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="phone" value="<?= e($user['phone']) ?>">
            </div>
            <hr>
            <p class="text-muted">Kosongkan jika tidak ingin mengganti password.</p>
            <div class="form-row">
                <div class="form-group">
                    <label>Password Saat Ini</label>
                    <input type="password" name="current_password">
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password">
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn">Simpan Perubahan</button>
        </div>
    </form>
</div>
