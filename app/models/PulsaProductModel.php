<?php

declare(strict_types=1);

final class PulsaProductModel extends Model
{
    protected static string $table = 'pulsa_products';
    protected static bool $softDelete = true;
    protected static array $fillable = ['category', 'provider', 'name', 'nominal', 'cost_price', 'sale_price', 'is_active'];

    public static function activeList(): array
    {
        return self::whereAll(['is_active' => 1], 'provider, nominal');
    }
}
