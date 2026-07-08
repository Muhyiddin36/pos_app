<?php
/**
 * Helper keamanan input: pembersihan dasar terhadap XSS pada string input.
 * Perlindungan SQL Injection dijamin oleh prepared statement (lihat Model/Database),
 * BUKAN oleh fungsi ini.
 */

declare(strict_types=1);

final class Security
{
    /** Bersihkan string input: trim + strip tag berbahaya. Nilai asli tetap disimpan sebagai teks. */
    public static function clean(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);
        return $value;
    }

    /** Validasi path redirect internal agar tidak dipakai untuk open-redirect. */
    public static function isSafePath(string $path): bool
    {
        return !preg_match('#^https?://#i', $path) && !str_starts_with($path, '//');
    }

    /** Bangun nama file aman untuk upload/backup (hanya alfanumerik, dash, titik). */
    public static function safeFilename(string $name): string
    {
        $name = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $name) ?? 'file';
        return substr($name, 0, 150);
    }
}
