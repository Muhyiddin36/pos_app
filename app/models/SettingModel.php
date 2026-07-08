<?php

declare(strict_types=1);

final class SettingModel
{
    private static array $cache = [];

    public static function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }
        $stmt = Database::connection()->prepare('SELECT `value` FROM settings WHERE `key` = :k');
        $stmt->execute(['k' => $key]);
        $row = $stmt->fetch();
        $value = $row === false ? $default : $row['value'];
        self::$cache[$key] = $value;
        return $value;
    }

    public static function set(string $key, string $value): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO settings (`key`, `value`) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE `value` = :v2'
        );
        $stmt->execute(['k' => $key, 'v' => $value, 'v2' => $value]);
        self::$cache[$key] = $value;
    }

    public static function all(): array
    {
        return Database::connection()->query('SELECT * FROM settings ORDER BY `key`')->fetchAll();
    }
}
