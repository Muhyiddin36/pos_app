-- =====================================================================
-- POS Multi Usaha - Seed Data
-- Menyediakan: role & permission default, 1 cabang pusat, 1 akun
-- Super Admin, dan pengaturan sistem dasar.
--
-- Login awal:
--   Username : superadmin
--   Password : Admin123!
--   -> WAJIB diganti setelah login pertama kali.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- Cabang default
-- ---------------------------------------------------------------------
INSERT INTO branches (id, code, name, address, phone, is_active) VALUES
(1, 'PST', 'Cabang Pusat', 'Jl. Contoh No. 1', '0800000000', 1);

-- ---------------------------------------------------------------------
-- Roles
-- ---------------------------------------------------------------------
INSERT INTO roles (id, slug, name, description) VALUES
(1, 'super_admin', 'Super Admin', 'Akses penuh ke seluruh sistem dan cabang'),
(2, 'branch_admin', 'Admin Cabang', 'Mengelola operasional pada satu cabang'),
(3, 'cashier', 'Kasir', 'Transaksi penjualan dan cetak struk');

-- ---------------------------------------------------------------------
-- Permissions
-- ---------------------------------------------------------------------
INSERT INTO permissions (code, module, name) VALUES
('dashboard.view',      'dashboard', 'Lihat Dashboard'),
('branches.view',       'branches',  'Lihat Cabang'),
('branches.manage',     'branches',  'Kelola Cabang'),
('users.view',          'users',     'Lihat Pengguna'),
('users.manage',        'users',     'Kelola Pengguna'),
('roles.view',          'roles',     'Lihat Role & Hak Akses'),
('roles.manage',        'roles',     'Kelola Role & Hak Akses'),
('categories.manage',   'categories','Kelola Kategori Produk'),
('products.view',       'products',  'Lihat Produk'),
('products.manage',     'products',  'Kelola Produk'),
('suppliers.manage',    'suppliers', 'Kelola Supplier'),
('customers.manage',    'customers', 'Kelola Pelanggan'),
('purchases.view',      'purchases', 'Lihat Pembelian'),
('purchases.manage',    'purchases', 'Kelola Pembelian'),
('sales.view',          'sales',     'Lihat Penjualan'),
('sales.create',        'sales',     'Buat Transaksi Penjualan'),
('sales.print',         'sales',     'Cetak Struk Penjualan'),
('sales.void',          'sales',     'Batalkan Transaksi Penjualan'),
('sales.edit_price',    'sales',     'Ubah Harga Saat Transaksi'),
('pulsa.view',          'pulsa',     'Lihat Transaksi Pulsa/Data'),
('pulsa.create',        'pulsa',     'Buat Transaksi Pulsa/Data'),
('pulsa.manage_product','pulsa',     'Kelola Produk Pulsa/Data'),
('pawn.view',           'pawn',      'Lihat Gadai'),
('pawn.create',         'pawn',      'Buat Transaksi Gadai'),
('pawn.manage',         'pawn',      'Kelola Pembayaran/Pelunasan Gadai'),
('loan.view',           'loan',      'Lihat Pinjaman'),
('loan.create',         'loan',      'Buat Transaksi Pinjaman'),
('loan.manage',         'loan',      'Kelola Angsuran/Pelunasan Pinjaman'),
('reports.view',        'reports',   'Lihat Laporan'),
('reports.profit',      'reports',   'Lihat Laporan Laba/Rugi'),
('backup.manage',       'backup',    'Backup & Restore Database'),
('audit.view',          'audit',     'Lihat Log Audit'),
('settings.manage',     'settings',  'Kelola Pengaturan Sistem'),
('bank.view',           'bank',      'Lihat Transaksi Transfer/Setor/Tarik Tunai'),
('bank.create',         'bank',      'Buat Transaksi Transfer/Setor/Tarik Tunai'),
('bank.void',           'bank',      'Batalkan Transaksi Bank'),
('bank.manage',         'bank',      'Kelola Daftar Bank'),
('service.view',        'service',   'Lihat Servis HP'),
('service.create',      'service',   'Terima Servis HP Baru'),
('service.manage',      'service',   'Kelola Status, Biaya & Pembayaran Servis HP'),
('cash.view',           'cash',      'Lihat Saldo Kas'),
('cash.record',         'cash',      'Input/Ubah Saldo Akhir Kas Harian'),
('cash.manage',         'cash',      'Kelola Sumber Dana & Saldo Awal (Super Admin)');

-- Super Admin => seluruh permission
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

-- Admin Cabang
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions WHERE code IN (
    'dashboard.view','branches.view',
    'categories.manage','products.view','products.manage',
    'suppliers.manage','customers.manage',
    'purchases.view','purchases.manage',
    'sales.view','sales.create','sales.print','sales.void','sales.edit_price',
    'pulsa.view','pulsa.create','pulsa.manage_product',
    'pawn.view','pawn.create','pawn.manage',
    'loan.view','loan.create','loan.manage',
    'bank.view','bank.create','bank.void','bank.manage',
    'service.view','service.create','service.manage',
    'cash.view','cash.record',
    'reports.view','reports.profit',
    'audit.view'
);

-- Kasir
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions WHERE code IN (
    'dashboard.view',
    'products.view',
    'customers.manage',
    'sales.view','sales.create','sales.print',
    'pulsa.view','pulsa.create',
    'pawn.view','pawn.create',
    'loan.view','loan.create',
    'bank.view','bank.create',
    'service.view','service.create',
    'cash.view','cash.record'
);

-- ---------------------------------------------------------------------
-- User Super Admin default (password: Admin123!)
-- ---------------------------------------------------------------------
INSERT INTO users (id, branch_id, role_id, username, password_hash, full_name, email, is_active) VALUES
(1, NULL, 1, 'superadmin', '$2y$12$al4bSzeX8qXGqVpM/bZxQuYrUgCfPM6dSKoyh4pO39IT2RHgqgd9m', 'Super Administrator', 'admin@example.com', 1);

-- ---------------------------------------------------------------------
-- Pengaturan sistem default
-- ---------------------------------------------------------------------
INSERT INTO settings (`key`, `value`) VALUES
('app_name', 'POS Multi Usaha'),
('app_currency', 'Rp'),
('app_timezone', 'Asia/Jakarta'),
('session_timeout_minutes', '30'),
('receipt_footer', 'Terima kasih atas kunjungan Anda'),
('pawn_default_interest_rate', '5'),
('loan_default_interest_rate', '5'),
('bank_default_admin_fee', '5000');

-- ---------------------------------------------------------------------
-- Contoh kategori & produk (opsional, boleh dihapus)
-- ---------------------------------------------------------------------
INSERT INTO categories (id, name, description) VALUES
(1, 'Casing', 'Casing dan pelindung HP'),
(2, 'Charger & Kabel', 'Charger, kabel data, power bank'),
(3, 'Aksesoris Lain', 'Earphone, tempered glass, dll');

INSERT INTO products (branch_id, category_id, sku, barcode, name, unit, purchase_price, sale_price, stock_qty, min_stock) VALUES
(1, 1, 'CS-001', '8990000000001', 'Casing Silikon Universal', 'pcs', 8000, 15000, 50, 10),
(1, 2, 'CH-001', '8990000000002', 'Kabel Data Type-C', 'pcs', 12000, 20000, 40, 10),
(1, 3, 'AC-001', '8990000000003', 'Tempered Glass Universal', 'pcs', 5000, 12000, 60, 15);

INSERT INTO pulsa_products (category, provider, name, nominal, cost_price, sale_price) VALUES
('pulsa', 'Telkomsel', 'Telkomsel 10.000', 10000, 10200, 11000),
('pulsa', 'Indosat', 'Indosat 10.000', 10000, 10100, 11000),
('paket_data', 'Telkomsel', 'Telkomsel Data 3GB', 0, 25000, 28000),
('ewallet', 'Gopay', 'Top Up Gopay 25.000', 25000, 25500, 27000),
('ewallet', 'Gopay', 'Top Up Gopay 50.000', 50000, 50500, 52500),
('ewallet', 'ShopeePay', 'Top Up ShopeePay 25.000', 25000, 25500, 27000),
('ewallet', 'ShopeePay', 'Top Up ShopeePay 50.000', 50000, 50500, 52500),
('ewallet', 'OVO', 'Top Up OVO 25.000', 25000, 25500, 27000),
('ewallet', 'DANA', 'Top Up DANA 25.000', 25000, 25500, 27000);

-- ---------------------------------------------------------------------
-- Daftar bank untuk fitur Transfer / Setor Tunai / Tarik Tunai
-- ---------------------------------------------------------------------
INSERT INTO banks (code, name) VALUES
('BCA', 'Bank Central Asia (BCA)'),
('MANDIRI', 'Bank Mandiri'),
('BNI', 'Bank Negara Indonesia (BNI)'),
('BRI', 'Bank Rakyat Indonesia (BRI)'),
('BSI', 'Bank Syariah Indonesia (BSI)'),
('CIMB', 'CIMB Niaga'),
('PERMATA', 'Bank Permata'),
('DANAMON', 'Bank Danamon'),
('BTN', 'Bank Tabungan Negara (BTN)'),
('BTPN', 'BTPN / Jenius'),
('JAGO', 'Bank Jago'),
('SEABANK', 'SeaBank'),
('OTHER', 'Bank Lainnya');

-- ---------------------------------------------------------------------
-- Sumber dana untuk fitur Saldo Kas Harian (dikelola Super Admin)
-- ---------------------------------------------------------------------
INSERT INTO cash_sources (branch_id, name, type, opening_balance, created_by) VALUES
(1, 'Kas Tunai', 'cash', 0, 1),
(1, 'Saldo Bank', 'bank', 0, 1),
(1, 'Gopay Merchant', 'ewallet', 0, 1);
