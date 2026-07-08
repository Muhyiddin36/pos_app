<div class="content-header">
    <h1>Pengaturan Sistem</h1>
</div>

<div class="card">
    <form method="post" action="<?= e(url('settings/update')) ?>">
        <?= Csrf::field() ?>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Aplikasi</label>
                    <input type="text" name="app_name" value="<?= e($settings['app_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Simbol Mata Uang</label>
                    <input type="text" name="app_currency" value="<?= e($settings['app_currency'] ?? 'Rp') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Zona Waktu</label>
                    <input type="text" name="app_timezone" value="<?= e($settings['app_timezone'] ?? 'Asia/Jakarta') ?>">
                </div>
                <div class="form-group">
                    <label>Timeout Sesi (menit)</label>
                    <input type="number" name="session_timeout_minutes" min="5" value="<?= e($settings['session_timeout_minutes'] ?? '30') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Bunga Default Gadai (%/bulan)</label>
                    <input type="number" step="0.01" name="pawn_default_interest_rate" value="<?= e($settings['pawn_default_interest_rate'] ?? '5') ?>">
                </div>
                <div class="form-group">
                    <label>Bunga Default Pinjaman (%/bulan)</label>
                    <input type="number" step="0.01" name="loan_default_interest_rate" value="<?= e($settings['loan_default_interest_rate'] ?? '5') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Catatan Kaki Struk</label>
                <textarea name="receipt_footer"><?= e($settings['receipt_footer'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn">Simpan Pengaturan</button>
        </div>
    </form>
</div>
