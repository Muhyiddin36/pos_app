<?php

declare(strict_types=1);

final class SettingController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('settings.manage');
        $settings = [];
        foreach (SettingModel::all() as $row) {
            $settings[$row['key']] = $row['value'];
        }
        $this->view('settings/index', ['title' => 'Pengaturan Sistem', 'settings' => $settings]);
    }

    public function update(): void
    {
        $this->requirePermission('settings.manage');
        $this->requireCsrf();

        $keys = [
            'app_name', 'app_currency', 'app_timezone', 'session_timeout_minutes',
            'receipt_footer', 'pawn_default_interest_rate', 'loan_default_interest_rate',
        ];
        foreach ($keys as $key) {
            SettingModel::set($key, $this->post($key));
        }

        AuditLogger::log('settings', 'update', 'Pengaturan sistem diperbarui');
        $this->withSuccess('Pengaturan berhasil disimpan.', 'settings/index');
    }

    /** Halaman profil untuk mengganti nama/password akun sendiri. */
    public function profile(): void
    {
        $this->requireLogin();
        $user = UserModel::find(Auth::id());
        $this->view('settings/profile', ['title' => 'Profil Saya', 'user' => $user]);
    }

    public function updateProfile(): void
    {
        $this->requireLogin();
        $this->requireCsrf();

        $data = [
            'full_name' => $this->post('full_name'),
            'email'     => $this->post('email'),
            'phone'     => $this->post('phone'),
        ];

        $newPassword = (string) $this->postRaw('new_password', '');
        if ($newPassword !== '') {
            $currentPassword = (string) $this->postRaw('current_password', '');
            $user = UserModel::find(Auth::id());
            if (!password_verify($currentPassword, $user['password_hash'])) {
                $this->withError('Password saat ini tidak sesuai.', 'settings/profile');
            }
            if (strlen($newPassword) < 6) {
                $this->withError('Password baru minimal 6 karakter.', 'settings/profile');
            }
            $data['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        UserModel::update(Auth::id(), $data);
        $_SESSION['user']['full_name'] = $data['full_name'];
        AuditLogger::log('users', 'update', 'Pengguna memperbarui profil sendiri');

        $this->withSuccess('Profil berhasil diperbarui.', 'settings/profile');
    }
}
