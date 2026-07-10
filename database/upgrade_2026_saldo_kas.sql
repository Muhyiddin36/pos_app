-- =====================================================================
-- POS Multi Usaha - Upgrade Script
-- Menambahkan modul: Saldo Kas Harian (rekonsiliasi kas tunai, bank,
-- dan e-wallet merchant per cabang).
--
-- CARA PAKAI: jalankan file ini SEKALI melalui phpMyAdmin (tab SQL) pada
-- database yang sudah berisi data dari instalasi awal. Aman dijalankan
-- lebih dari sekali (idempotent) - tidak akan menduplikasi data.
--
-- Untuk instalasi BARU, tidak perlu file ini - database/schema.sql dan
-- database/seed.sql sudah mencakup seluruh perubahan pada file ini.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. Tabel baru: Saldo Kas Harian
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cash_sources (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id         INT UNSIGNED NOT NULL,
    name              VARCHAR(100) NOT NULL COMMENT 'Keterangan sumber dana, mis. Kas Tunai, Saldo Bank BCA, Gopay Merchant',
    type              ENUM('cash','bank','ewallet','other') NOT NULL DEFAULT 'cash',
    opening_balance   DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Saldo awal - hanya diatur Super Admin',
    is_active         TINYINT(1) NOT NULL DEFAULT 1,
    created_by        INT UNSIGNED DEFAULT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at        DATETIME DEFAULT NULL,
    CONSTRAINT fk_cashsource_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_cashsource_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cash_balance_records (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id         INT UNSIGNED NOT NULL,
    cash_source_id    INT UNSIGNED NOT NULL,
    record_date       DATE NOT NULL,
    closing_balance   DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Saldo akhir - diinput/diedit Kasir/Admin Cabang',
    note              VARCHAR(255) DEFAULT NULL,
    recorded_by       INT UNSIGNED DEFAULT NULL,
    updated_by        INT UNSIGNED DEFAULT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cash_record (cash_source_id, record_date),
    CONSTRAINT fk_cashrecord_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_cashrecord_source FOREIGN KEY (cash_source_id) REFERENCES cash_sources(id) ON DELETE CASCADE,
    CONSTRAINT fk_cashrecord_recorder FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_cashrecord_updater FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_cashrecord_branch_date (branch_id, record_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 2. Hak akses (permissions) baru + pemetaan ke role yang sudah ada
--    cash.manage (kelola sumber dana & saldo awal) SENGAJA hanya
--    diberikan ke Super Admin, tidak ke Admin Cabang maupun Kasir.
-- ---------------------------------------------------------------------
INSERT IGNORE INTO permissions (code, module, name) VALUES
('cash.view',   'cash', 'Lihat Saldo Kas'),
('cash.record', 'cash', 'Input/Ubah Saldo Akhir Kas Harian'),
('cash.manage', 'cash', 'Kelola Sumber Dana & Saldo Awal (Super Admin)');

-- Super Admin: otomatis mendapat semua permission (termasuk yang baru saja ditambahkan)
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'super_admin'
  AND p.code IN ('cash.view','cash.record','cash.manage');

-- Admin Cabang: boleh melihat & mencatat saldo akhir, TIDAK boleh kelola sumber dana/saldo awal
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'branch_admin'
  AND p.code IN ('cash.view','cash.record');

-- Kasir: boleh melihat & mencatat saldo akhir, TIDAK boleh kelola sumber dana/saldo awal
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'cashier'
  AND p.code IN ('cash.view','cash.record');

-- ---------------------------------------------------------------------
-- 3. Contoh sumber dana per cabang (opsional, boleh diubah/dihapus oleh Super Admin)
-- ---------------------------------------------------------------------
INSERT INTO cash_sources (branch_id, name, type, opening_balance, created_by)
SELECT b.id, 'Kas Tunai', 'cash', 0, (SELECT id FROM users WHERE username = 'superadmin' LIMIT 1)
FROM branches b
WHERE NOT EXISTS (SELECT 1 FROM cash_sources cs WHERE cs.branch_id = b.id AND cs.name = 'Kas Tunai');

INSERT INTO cash_sources (branch_id, name, type, opening_balance, created_by)
SELECT b.id, 'Saldo Bank', 'bank', 0, (SELECT id FROM users WHERE username = 'superadmin' LIMIT 1)
FROM branches b
WHERE NOT EXISTS (SELECT 1 FROM cash_sources cs WHERE cs.branch_id = b.id AND cs.name = 'Saldo Bank');

INSERT INTO cash_sources (branch_id, name, type, opening_balance, created_by)
SELECT b.id, 'Gopay Merchant', 'ewallet', 0, (SELECT id FROM users WHERE username = 'superadmin' LIMIT 1)
FROM branches b
WHERE NOT EXISTS (SELECT 1 FROM cash_sources cs WHERE cs.branch_id = b.id AND cs.name = 'Gopay Merchant');

-- Selesai. Muat ulang halaman aplikasi lalu login ulang agar menu baru
-- ("Saldo Kas Harian") langsung terlihat di sidebar.
