<?php
/**
 * Flash message satu-kali-tampil (disimpan di session, dihapus setelah dibaca).
 */

declare(strict_types=1);

final class Flash
{
    public static function set(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }

    public static function get(string $type): ?string
    {
        if (empty($_SESSION['_flash'][$type])) {
            return null;
        }
        $message = $_SESSION['_flash'][$type];
        unset($_SESSION['_flash'][$type]);
        return $message;
    }

    public static function all(): array
    {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }
}
