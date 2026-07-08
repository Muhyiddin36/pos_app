<?php
/**
 * Konfigurasi utama aplikasi.
 * File ini di-load sekali oleh index.php sebelum routing dijalankan.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0'); // jangan tampilkan error mentah ke publik
ini_set('log_errors', '1');
ini_set('error_log', ROOT_PATH . '/storage/logs/php_error.log');

date_default_timezone_set('Asia/Jakarta');

// -----------------------------------------------------------------
// Informasi Aplikasi
// -----------------------------------------------------------------
define('APP_NAME', 'POS Multi Usaha');
define('APP_VERSION', '1.0.0');

// Base URL otomatis terdeteksi dari request (mendukung subfolder di shared hosting)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', rtrim($scheme . '://' . $host . $scriptDir, '/'));

// -----------------------------------------------------------------
// Path
// -----------------------------------------------------------------
define('APP_PATH', ROOT_PATH . '/app');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', APP_PATH . '/views');

// -----------------------------------------------------------------
// Sesi (Session) - hardened
// -----------------------------------------------------------------
define('SESSION_TIMEOUT_SECONDS', 30 * 60); // default 30 menit, override via tabel settings

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => $scriptDir === '' ? '/' : $scriptDir,
        'domain'   => '',
        'secure'   => $scheme === 'https',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('POS_SESSID');
    session_start();
}

// -----------------------------------------------------------------
// Autoload sederhana (tanpa Composer)
// -----------------------------------------------------------------
spl_autoload_register(function (string $class): void {
    $dirs = [
        APP_PATH . '/core/',
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

require_once APP_PATH . '/core/helpers.php';
require_once APP_PATH . '/config/database.php';
