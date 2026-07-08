<?php
/**
 * Proteksi CSRF berbasis token per-sesi.
 */

declare(strict_types=1);

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    public static function verify(string $token): bool
    {
        if (empty($_SESSION[self::SESSION_KEY]) || $token === '') {
            return false;
        }
        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}
