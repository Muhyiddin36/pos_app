<?php
/** @var array $currentUser diisi oleh layout pemanggil */
$currentUser = Auth::user();
$route = $_GET['route'] ?? 'dashboard/index';
$activeModule = explode('/', $route)[0] ?: 'dashboard';

/**
 * Struktur menu: label, icon (emoji ringan agar tanpa dependency font-icon),
 * route dasar, dan permission yang dibutuhkan (null = selalu tampil bila login).
 */
$menu = [
    ['label' => 'Dashboard', 'icon' => '&#9679;', 'route' => 'dashboard/index', 'key' => 'dashboard', 'perm' => 'dashboard.view'],
];

$menuTransaksi = [
    ['label' => 'Kasir Aksesoris', 'icon' => '&#128722;', 'route' => 'sales/pos', 'key' => 'sales', 'perm' => 'sales.create'],
    ['label' => 'Riwayat Penjualan', 'icon' => '&#128220;', 'route' => 'sales/index', 'key' => 'sales', 'perm' => 'sales.view'],
    ['label' => 'Pulsa, Data & Top Up', 'icon' => '&#128241;', 'route' => 'pulsa/index', 'key' => 'pulsa', 'perm' => 'pulsa.view'],
    ['label' => 'Transfer / Setor Bank', 'icon' => '&#127974;', 'route' => 'bank/index', 'key' => 'bank', 'perm' => 'bank.view'],
    ['label' => 'Servis HP', 'icon' => '&#128295;', 'route' => 'service/index', 'key' => 'service', 'perm' => 'service.view'],
    ['label' => 'Gadai Barang', 'icon' => '&#128274;', 'route' => 'pawn/index', 'key' => 'pawn', 'perm' => 'pawn.view'],
    ['label' => 'Pinjam Uang', 'icon' => '&#128176;', 'route' => 'loan/index', 'key' => 'loan', 'perm' => 'loan.view'],
    ['label' => 'Saldo Kas Harian', 'icon' => '&#128179;', 'route' => 'cash/index', 'key' => 'cash', 'perm' => 'cash.view'],
];

$menuMaster = [
    ['label' => 'Produk', 'icon' => '&#128230;', 'route' => 'products/index', 'key' => 'products', 'perm' => 'products.view'],
    ['label' => 'Kategori', 'icon' => '&#128193;', 'route' => 'categories/index', 'key' => 'categories', 'perm' => 'categories.manage'],
    ['label' => 'Pembelian', 'icon' => '&#128717;', 'route' => 'purchases/index', 'key' => 'purchases', 'perm' => 'purchases.view'],
    ['label' => 'Supplier', 'icon' => '&#127970;', 'route' => 'suppliers/index', 'key' => 'suppliers', 'perm' => 'suppliers.manage'],
    ['label' => 'Pelanggan', 'icon' => '&#128100;', 'route' => 'customers/index', 'key' => 'customers', 'perm' => 'customers.manage'],
];

$menuLaporan = [
    ['label' => 'Laporan', 'icon' => '&#128202;', 'route' => 'reports/index', 'key' => 'reports', 'perm' => 'reports.view'],
    ['label' => 'Log Audit', 'icon' => '&#128269;', 'route' => 'audit-log/index', 'key' => 'audit-log', 'perm' => 'audit.view'],
];

$menuSistem = [
    ['label' => 'Cabang', 'icon' => '&#127981;', 'route' => 'branches/index', 'key' => 'branches', 'perm' => 'branches.manage'],
    ['label' => 'Pengguna', 'icon' => '&#128101;', 'route' => 'users/index', 'key' => 'users', 'perm' => 'users.manage'],
    ['label' => 'Role & Hak Akses', 'icon' => '&#128737;', 'route' => 'roles/index', 'key' => 'roles', 'perm' => 'roles.manage'],
    ['label' => 'Backup & Restore', 'icon' => '&#128190;', 'route' => 'backup/index', 'key' => 'backup', 'perm' => 'backup.manage'],
    ['label' => 'Pengaturan', 'icon' => '&#9881;', 'route' => 'settings/index', 'key' => 'settings', 'perm' => 'settings.manage'],
];

?>
<div class="sidebar-backdrop" data-sidebar-backdrop></div>
<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="logo-dot"></span>
        <span><?= e(APP_NAME) ?></span>
    </div>

    <ul class="sidebar-nav"><?php render_menu_group($menu, $activeModule); ?></ul>

    <div class="sidebar-section">Transaksi</div>
    <ul class="sidebar-nav"><?php render_menu_group($menuTransaksi, $activeModule); ?></ul>

    <div class="sidebar-section">Data Master</div>
    <ul class="sidebar-nav"><?php render_menu_group($menuMaster, $activeModule); ?></ul>

    <div class="sidebar-section">Laporan</div>
    <ul class="sidebar-nav"><?php render_menu_group($menuLaporan, $activeModule); ?></ul>

    <?php if (Auth::can('branches.manage') || Auth::can('users.manage') || Auth::can('roles.manage') || Auth::can('backup.manage') || Auth::can('settings.manage')): ?>
    <div class="sidebar-section">Administrasi Sistem</div>
    <ul class="sidebar-nav"><?php render_menu_group($menuSistem, $activeModule); ?></ul>
    <?php endif; ?>
</aside>
