<?php
/**
 * Kumpulan fungsi bantu global.
 * Semua fungsi bersifat stateless kecuali disebutkan lain.
 */

declare(strict_types=1);

/** Escape output untuk mencegah XSS. Gunakan di SEMUA view saat mencetak data user. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Bangun URL relatif terhadap BASE_URL. */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** Bangun URL asset (css/js/img). */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** Redirect lalu hentikan eksekusi. */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/** Format angka ke Rupiah, mis. 1500000 -> "Rp 1.500.000". */
function rupiah(float|int|string|null $amount): string
{
    $amount = (float) ($amount ?? 0);
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/** Format tanggal Indonesia. */
function tgl(?string $date, string $format = 'd-m-Y'): string
{
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : '-';
}

/** Ambil nilai lama input (repopulate form setelah validasi gagal). */
function old(string $key, string $default = ''): string
{
    $old = $_SESSION['_old_input'] ?? [];
    return e($old[$key] ?? $default);
}

/** Ambil pesan error validasi untuk field tertentu. */
function form_error(string $key): string
{
    $errors = $_SESSION['_errors'] ?? [];
    return isset($errors[$key]) ? '<span class="field-error">' . e($errors[$key]) . '</span>' : '';
}

/** Cek apakah field memiliki error (untuk class CSS). */
function has_error(string $key): bool
{
    return isset($_SESSION['_errors'][$key]);
}

/** Generate nomor dokumen unik: PREFIX-YYYYMMDD-XXXX */
function generate_doc_number(string $prefix): string
{
    return $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

/** Render satu grup item menu sidebar sesuai hak akses user yang login. */
function render_menu_group(array $items, string $activeModule): void
{
    foreach ($items as $item) {
        if ($item['perm'] !== null && !Auth::can($item['perm'])) {
            continue;
        }
        $active = $item['key'] === $activeModule ? ' active' : '';
        echo '<li><a class="' . trim($active) . '" href="' . e(url($item['route'])) . '"><span class="icon">' . $item['icon'] . '</span><span>' . e($item['label']) . '</span></a></li>';
    }
}

/** Debug helper (hanya untuk pengembangan). */
function dd(mixed ...$vars): never
{
    echo '<pre style="background:#111;color:#0f0;padding:1rem;">';
    foreach ($vars as $v) {
        print_r($v);
    }
    echo '</pre>';
    exit;
}
