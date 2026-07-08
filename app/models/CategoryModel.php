<?php

declare(strict_types=1);

final class CategoryModel extends Model
{
    protected static string $table = 'categories';
    protected static bool $softDelete = true;
    protected static array $fillable = ['name', 'description'];
}
