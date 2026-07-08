<?php

declare(strict_types=1);

final class SupplierModel extends Model
{
    protected static string $table = 'suppliers';
    protected static bool $softDelete = true;
    protected static array $fillable = ['name', 'contact_person', 'phone', 'address'];
}
