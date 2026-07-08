<div class="login-card">
    <div class="login-logo">P</div>
    <h1><?= e(APP_NAME) ?></h1>
    <p class="sub">Silakan masuk untuk melanjutkan</p>

    <form method="post" action="<?= e(url('auth/doLogin')) ?>">
        <?= Csrf::field() ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= old('username') ?>" autofocus required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-block btn-lg">Masuk</button>
    </form>
</div>
