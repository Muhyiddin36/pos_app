<?php
/**
 * Otentikasi, sesi, dan pengecekan hak akses (RBAC) berbasis permission code.
 */

declare(strict_types=1);

final class Auth
{
    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCK_MINUTES = 15;

    public static function attempt(string $username, string $password): array
    {
        $user = UserModel::findActiveByUsername($username);
        if ($user === null) {
            return ['ok' => false, 'message' => 'Username atau password salah.'];
        }

        if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $until = date('H:i', strtotime($user['locked_until']));
            return ['ok' => false, 'message' => "Akun terkunci sementara akibat percobaan login gagal. Coba lagi setelah {$until}."];
        }

        if (!password_verify($password, $user['password_hash'])) {
            self::registerFailedAttempt((int) $user['id'], (int) $user['failed_login_count']);
            return ['ok' => false, 'message' => 'Username atau password salah.'];
        }

        UserModel::update((int) $user['id'], [
            'failed_login_count' => 0,
            'locked_until'       => null,
            'last_login_at'      => date('Y-m-d H:i:s'),
        ]);

        self::login($user);
        return ['ok' => true, 'message' => 'Login berhasil.'];
    }

    private static function registerFailedAttempt(int $userId, int $currentCount): void
    {
        $newCount = $currentCount + 1;
        $data = ['failed_login_count' => $newCount];
        if ($newCount >= self::MAX_FAILED_ATTEMPTS) {
            $data['locked_until'] = date('Y-m-d H:i:s', time() + self::LOCK_MINUTES * 60);
            $data['failed_login_count'] = 0;
        }
        UserModel::update($userId, $data);
    }

    private static function login(array $user): void
    {
        session_regenerate_id(true);

        $permissions = PermissionModel::codesForRole((int) $user['role_id']);
        $role = RoleModel::find((int) $user['role_id']);

        $_SESSION['user'] = [
            'id'         => (int) $user['id'],
            'username'   => $user['username'],
            'full_name'  => $user['full_name'],
            'branch_id'  => $user['branch_id'] !== null ? (int) $user['branch_id'] : null,
            'role_id'    => (int) $user['role_id'],
            'role_slug'  => $role['slug'] ?? '',
            'role_name'  => $role['name'] ?? '',
            'permissions'=> $permissions,
        ];
        $_SESSION['_last_activity'] = time();

        AuditLogger::log('auth', 'login', 'User login: ' . $user['username']);
    }

    public static function logout(): void
    {
        if (self::check()) {
            AuditLogger::log('auth', 'logout', 'User logout: ' . self::user()['username']);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function isSuperAdmin(): bool
    {
        return (self::user()['role_slug'] ?? '') === 'super_admin';
    }

    public static function branchId(): ?int
    {
        return self::user()['branch_id'] ?? null;
    }

    public static function can(string $permissionCode): bool
    {
        $user = self::user();
        if ($user === null) {
            return false;
        }
        return in_array($permissionCode, $user['permissions'] ?? [], true);
    }

    /** Cek timeout sesi akibat idle terlalu lama, lalu perbarui waktu aktivitas. */
    public static function checkSessionTimeout(): void
    {
        if (!self::check()) {
            return;
        }
        $timeout = (int) (SettingModel::get('session_timeout_minutes') ?? 30) * 60;
        $last = $_SESSION['_last_activity'] ?? time();
        if (time() - $last > $timeout) {
            self::logout();
            Flash::set('error', 'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
            redirect('auth/login');
        }
        $_SESSION['_last_activity'] = time();
    }
}
