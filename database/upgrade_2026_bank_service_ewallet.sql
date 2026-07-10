-- =====================================================================
-- POS Multi Usaha - Upgrade Script
-- Menambahkan 3 modul baru ke instalasi yang SUDAH BERJALAN:
--   1) Transfer / Setor Tunai / Tarik Tunai (agen semua bank)
--   2) Servis HP
--   3) Top Up Saldo E-Wallet (Gopay/ShopeePay/OVO/DANA) - perluasan modul Pulsa
--
-- CARA PAKAI: jalankan file ini SEKALI melalui phpMyAdmin (tab SQL) pada
-- database yang sudah berisi data dari instalasi awal (schema.sql + seed.sql).
-- Aman dijalankan lebih dari sekali (idempotent) - tidak akan menduplikasi data.
--
-- Untuk instalasi BARU, tidak perlu file ini - database/schema.sql dan
-- database/seed.sql sudah mencakup seluruh perubahan pada file ini.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. Perluas kategori produk Pulsa & Paket Data agar mendukung E-Wallet
-- ---------------------------------------------------------------------
ALTER TABLE pulsa_products
    MODIFY COLUMN category ENUM('pulsa','paket_data','pln','ewallet','other') NOT NULL DEFAULT 'pulsa';

-- ---------------------------------------------------------------------
-- 2. Tabel baru: Transfer / Setor Tunai / Tarik Tunai
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS banks (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code          VARCHAR(20)  NOT NULL UNIQUE,
    name          VARCHAR(100) NOT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at    DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bank_transactions (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id         INT UNSIGNED NOT NULL,
    bank_id           INT UNSIGNED NOT NULL,
    trx_no            VARCHAR(40) NOT NULL UNIQUE,
    transaction_type  ENUM('transfer','setor_tunai','tarik_tunai') NOT NULL DEFAULT 'transfer',
    account_number    VARCHAR(50)  DEFAULT NULL,
    account_name      VARCHAR(100) DEFAULT NULL,
    customer_id       INT UNSIGNED DEFAULT NULL,
    customer_phone    VARCHAR(30)  DEFAULT NULL,
    amount            DECIMAL(15,2) NOT NULL DEFAULT 0,
    cost_fee          DECIMAL(15,2) NOT NULL DEFAULT 0,
    admin_fee         DECIMAL(15,2) NOT NULL DEFAULT 0,
    profit_amount     DECIMAL(15,2) NOT NULL DEFAULT 0,
    status            ENUM('success','pending','failed','void') NOT NULL DEFAULT 'success',
    void_reason       VARCHAR(255) DEFAULT NULL,
    voided_by         INT UNSIGNED DEFAULT NULL,
    voided_at         DATETIME DEFAULT NULL,
    note              VARCHAR(255) DEFAULT NULL,
    created_by        INT UNSIGNED DEFAULT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_banktrx_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_banktrx_bank FOREIGN KEY (bank_id) REFERENCES banks(id) ON DELETE RESTRICT,
    CONSTRAINT fk_banktrx_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    CONSTRAINT fk_banktrx_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_banktrx_voider FOREIGN KEY (voided_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_banktrx_branch_date (branch_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 3. Tabel baru: Servis HP
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS service_orders (
    id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id               INT UNSIGNED NOT NULL,
    customer_id             INT UNSIGNED NOT NULL,
    service_no              VARCHAR(40)  NOT NULL UNIQUE,
    device_type             VARCHAR(100) NOT NULL,
    device_imei             VARCHAR(50)  DEFAULT NULL,
    issue_description       VARCHAR(500) NOT NULL,
    accessories_note        VARCHAR(255) DEFAULT NULL,
    estimated_cost          DECIMAL(15,2) NOT NULL DEFAULT 0,
    final_cost              DECIMAL(15,2) NOT NULL DEFAULT 0,
    down_payment            DECIMAL(15,2) NOT NULL DEFAULT 0,
    paid_amount             DECIMAL(15,2) NOT NULL DEFAULT 0,
    status                  ENUM('received','in_progress','waiting_parts','completed','picked_up','cancelled') NOT NULL DEFAULT 'received',
    technician_notes        VARCHAR(500) DEFAULT NULL,
    received_date           DATE NOT NULL,
    estimated_finish_date   DATE DEFAULT NULL,
    completed_at            DATETIME DEFAULT NULL,
    picked_up_at            DATETIME DEFAULT NULL,
    created_by              INT UNSIGNED DEFAULT NULL,
    created_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_service_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_service_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    CONSTRAINT fk_service_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_service_branch_status (branch_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS service_status_logs (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_order_id    INT UNSIGNED NOT NULL,
    status              VARCHAR(30) NOT NULL,
    note                VARCHAR(255) DEFAULT NULL,
    created_by          INT UNSIGNED DEFAULT NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_svclog_order FOREIGN KEY (service_order_id) REFERENCES service_orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_svclog_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_svclog_order (service_order_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS service_payments (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_order_id    INT UNSIGNED NOT NULL,
    payment_date        DATE NOT NULL,
    type                ENUM('down_payment','final_payment') NOT NULL,
    amount              DECIMAL(15,2) NOT NULL,
    payment_method      ENUM('cash','transfer','qris','debit') NOT NULL DEFAULT 'cash',
    note                VARCHAR(255) DEFAULT NULL,
    created_by          INT UNSIGNED DEFAULT NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_svcpay_order FOREIGN KEY (service_order_id) REFERENCES service_orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_svcpay_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 4. Hak akses (permissions) baru + pemetaan ke role yang sudah ada
-- ---------------------------------------------------------------------
INSERT IGNORE INTO permissions (code, module, name) VALUES
('bank.view',      'bank',    'Lihat Transaksi Transfer/Setor/Tarik Tunai'),
('bank.create',    'bank',    'Buat Transaksi Transfer/Setor/Tarik Tunai'),
('bank.void',      'bank',    'Batalkan Transaksi Bank'),
('bank.manage',    'bank',    'Kelola Daftar Bank'),
('service.view',   'service', 'Lihat Servis HP'),
('service.create', 'service', 'Terima Servis HP Baru'),
('service.manage', 'service', 'Kelola Status, Biaya & Pembayaran Servis HP');

-- Super Admin: otomatis mendapat semua permission (termasuk yang baru saja ditambahkan)
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'super_admin'
  AND p.code IN ('bank.view','bank.create','bank.void','bank.manage','service.view','service.create','service.manage');

-- Admin Cabang: akses penuh ke modul baru pada cabangnya
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'branch_admin'
  AND p.code IN ('bank.view','bank.create','bank.void','bank.manage','service.view','service.create','service.manage');

-- Kasir: hanya boleh membuat & melihat transaksi (tidak boleh void/kelola)
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'cashier'
  AND p.code IN ('bank.view','bank.create','service.view','service.create');

-- ---------------------------------------------------------------------
-- 5. Pengaturan default biaya admin transfer bank
-- ---------------------------------------------------------------------
INSERT IGNORE INTO settings (`key`, `value`) VALUES ('bank_default_admin_fee', '5000');

-- ---------------------------------------------------------------------
-- 6. Master data: daftar bank
-- ---------------------------------------------------------------------
INSERT IGNORE INTO banks (code, name) VALUES
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
-- 7. Contoh produk top up e-wallet (opsional, boleh diubah/dihapus)
-- ---------------------------------------------------------------------
INSERT INTO pulsa_products (category, provider, name, nominal, cost_price, sale_price)
SELECT 'ewallet', 'Gopay', 'Top Up Gopay 25.000', 25000, 25500, 27000
WHERE NOT EXISTS (SELECT 1 FROM pulsa_products WHERE category = 'ewallet' AND provider = 'Gopay' AND name = 'Top Up Gopay 25.000');

INSERT INTO pulsa_products (category, provider, name, nominal, cost_price, sale_price)
SELECT 'ewallet', 'Gopay', 'Top Up Gopay 50.000', 50000, 50500, 52500
WHERE NOT EXISTS (SELECT 1 FROM pulsa_products WHERE category = 'ewallet' AND provider = 'Gopay' AND name = 'Top Up Gopay 50.000');

INSERT INTO pulsa_products (category, provider, name, nominal, cost_price, sale_price)
SELECT 'ewallet', 'ShopeePay', 'Top Up ShopeePay 25.000', 25000, 25500, 27000
WHERE NOT EXISTS (SELECT 1 FROM pulsa_products WHERE category = 'ewallet' AND provider = 'ShopeePay' AND name = 'Top Up ShopeePay 25.000');

INSERT INTO pulsa_products (category, provider, name, nominal, cost_price, sale_price)
SELECT 'ewallet', 'ShopeePay', 'Top Up ShopeePay 50.000', 50000, 50500, 52500
WHERE NOT EXISTS (SELECT 1 FROM pulsa_products WHERE category = 'ewallet' AND provider = 'ShopeePay' AND name = 'Top Up ShopeePay 50.000');

INSERT INTO pulsa_products (category, provider, name, nominal, cost_price, sale_price)
SELECT 'ewallet', 'OVO', 'Top Up OVO 25.000', 25000, 25500, 27000
WHERE NOT EXISTS (SELECT 1 FROM pulsa_products WHERE category = 'ewallet' AND provider = 'OVO' AND name = 'Top Up OVO 25.000');

INSERT INTO pulsa_products (category, provider, name, nominal, cost_price, sale_price)
SELECT 'ewallet', 'DANA', 'Top Up DANA 25.000', 25000, 25500, 27000
WHERE NOT EXISTS (SELECT 1 FROM pulsa_products WHERE category = 'ewallet' AND provider = 'DANA' AND name = 'Top Up DANA 25.000');

-- Selesai. Muat ulang halaman aplikasi lalu login ulang agar menu baru
-- ("Transfer/Setor Bank" dan "Servis HP") langsung terlihat di sidebar.
