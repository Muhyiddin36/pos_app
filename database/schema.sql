-- =====================================================================
-- POS Multi Usaha - Database Schema
-- Engine   : MySQL 5.7+ / MariaDB 10.3+ (InnoDB, utf8mb4)
-- Modules  : Aksesoris HP, Pulsa & Paket Data, Gadai Barang, Pinjam Uang,
--            Multi Cabang, Laporan, Backup, Audit Log, Hak Akses
-- Notes    : Semua tabel transaksi menggunakan FOREIGN KEY + InnoDB agar
--            ACID/transaction terjaga. Soft delete memakai kolom
--            `deleted_at` (NULL = aktif).
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. CABANG (BRANCHES)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS branches (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code          VARCHAR(20)  NOT NULL UNIQUE,
    name          VARCHAR(100) NOT NULL,
    address       VARCHAR(255) DEFAULT NULL,
    phone         VARCHAR(30)  DEFAULT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at    DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 2. HAK AKSES (ROLES & PERMISSIONS)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug          VARCHAR(50)  NOT NULL UNIQUE,
    name          VARCHAR(100) NOT NULL,
    description   VARCHAR(255) DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS permissions (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code          VARCHAR(80)  NOT NULL UNIQUE,
    module        VARCHAR(50)  NOT NULL,
    name          VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id       INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 3. USERS
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id       INT UNSIGNED DEFAULT NULL COMMENT 'NULL = akses semua cabang (super admin)',
    role_id         INT UNSIGNED NOT NULL,
    username        VARCHAR(50)  NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    full_name       VARCHAR(100) NOT NULL,
    email           VARCHAR(100) DEFAULT NULL,
    phone           VARCHAR(30)  DEFAULT NULL,
    is_active       TINYINT(1)   NOT NULL DEFAULT 1,
    last_login_at   DATETIME     DEFAULT NULL,
    failed_login_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until    DATETIME     DEFAULT NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME     DEFAULT NULL,
    CONSTRAINT fk_users_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 4. MASTER: KATEGORI, PRODUK (AKSESORIS HP), SUPPLIER, PELANGGAN
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    description   VARCHAR(255) DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at    DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS suppliers (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    contact_person VARCHAR(100) DEFAULT NULL,
    phone         VARCHAR(30)  DEFAULT NULL,
    address       VARCHAR(255) DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at    DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS customers (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id     INT UNSIGNED DEFAULT NULL,
    name          VARCHAR(150) NOT NULL,
    phone         VARCHAR(30)  DEFAULT NULL,
    address       VARCHAR(255) DEFAULT NULL,
    id_card_number VARCHAR(30) DEFAULT NULL COMMENT 'NIK - untuk gadai/pinjaman',
    note          VARCHAR(255) DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at    DATETIME     DEFAULT NULL,
    CONSTRAINT fk_customers_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id       INT UNSIGNED NOT NULL,
    category_id     INT UNSIGNED DEFAULT NULL,
    sku             VARCHAR(50)  NOT NULL,
    barcode         VARCHAR(50)  DEFAULT NULL,
    name            VARCHAR(150) NOT NULL,
    unit            VARCHAR(20)  NOT NULL DEFAULT 'pcs',
    purchase_price  DECIMAL(15,2) NOT NULL DEFAULT 0,
    sale_price      DECIMAL(15,2) NOT NULL DEFAULT 0,
    stock_qty       INT NOT NULL DEFAULT 0,
    min_stock       INT NOT NULL DEFAULT 0,
    is_active       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME     DEFAULT NULL,
    UNIQUE KEY uq_products_branch_sku (branch_id, sku),
    CONSTRAINT fk_products_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS stock_movements (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id       INT UNSIGNED NOT NULL,
    product_id      INT UNSIGNED NOT NULL,
    type            ENUM('in','out','adjustment') NOT NULL,
    qty             INT NOT NULL COMMENT 'positif=masuk, negatif=keluar (untuk adjustment)',
    reference_type  VARCHAR(30) DEFAULT NULL COMMENT 'purchase|sale|adjustment',
    reference_id    BIGINT UNSIGNED DEFAULT NULL,
    note            VARCHAR(255) DEFAULT NULL,
    created_by      INT UNSIGNED DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_stockmove_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_stockmove_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_stockmove_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 5. PEMBELIAN (PURCHASES) - stok masuk dari supplier
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS purchases (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id       INT UNSIGNED NOT NULL,
    supplier_id     INT UNSIGNED DEFAULT NULL,
    invoice_no      VARCHAR(40)  NOT NULL UNIQUE,
    purchase_date   DATE NOT NULL,
    total_amount    DECIMAL(15,2) NOT NULL DEFAULT 0,
    note            VARCHAR(255) DEFAULT NULL,
    created_by      INT UNSIGNED DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at      DATETIME DEFAULT NULL,
    CONSTRAINT fk_purchase_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_purchase_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    CONSTRAINT fk_purchase_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS purchase_items (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_id     INT UNSIGNED NOT NULL,
    product_id      INT UNSIGNED NOT NULL,
    qty             INT NOT NULL,
    price           DECIMAL(15,2) NOT NULL,
    subtotal        DECIMAL(15,2) NOT NULL,
    CONSTRAINT fk_pitem_purchase FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    CONSTRAINT fk_pitem_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 6. PENJUALAN AKSESORIS HP (SALES / POS)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sales (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id       INT UNSIGNED NOT NULL,
    customer_id     INT UNSIGNED DEFAULT NULL,
    invoice_no      VARCHAR(40)  NOT NULL UNIQUE,
    sale_date       DATETIME NOT NULL,
    subtotal        DECIMAL(15,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_amount    DECIMAL(15,2) NOT NULL DEFAULT 0,
    paid_amount     DECIMAL(15,2) NOT NULL DEFAULT 0,
    change_amount   DECIMAL(15,2) NOT NULL DEFAULT 0,
    payment_method  ENUM('cash','transfer','qris','debit') NOT NULL DEFAULT 'cash',
    status          ENUM('completed','void') NOT NULL DEFAULT 'completed',
    void_reason     VARCHAR(255) DEFAULT NULL,
    voided_by       INT UNSIGNED DEFAULT NULL,
    voided_at       DATETIME DEFAULT NULL,
    created_by      INT UNSIGNED DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sales_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_sales_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    CONSTRAINT fk_sales_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_sales_voider FOREIGN KEY (voided_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sale_items (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id         INT UNSIGNED NOT NULL,
    product_id      INT UNSIGNED NOT NULL,
    qty             INT NOT NULL,
    price           DECIMAL(15,2) NOT NULL COMMENT 'harga jual saat transaksi',
    cost_price      DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'harga modal saat transaksi (utk laba)',
    subtotal        DECIMAL(15,2) NOT NULL,
    CONSTRAINT fk_sitem_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    CONSTRAINT fk_sitem_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 7. PULSA & PAKET DATA
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pulsa_products (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category         ENUM('pulsa','paket_data','pln','other') NOT NULL DEFAULT 'pulsa',
    provider         VARCHAR(50) NOT NULL COMMENT 'Telkomsel, Indosat, XL, dll',
    name             VARCHAR(100) NOT NULL,
    nominal          DECIMAL(15,2) NOT NULL DEFAULT 0,
    cost_price       DECIMAL(15,2) NOT NULL DEFAULT 0,
    sale_price       DECIMAL(15,2) NOT NULL DEFAULT 0,
    is_active        TINYINT(1) NOT NULL DEFAULT 1,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at       DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pulsa_transactions (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id         INT UNSIGNED NOT NULL,
    pulsa_product_id  INT UNSIGNED NOT NULL,
    trx_no            VARCHAR(40) NOT NULL UNIQUE,
    customer_phone    VARCHAR(30) NOT NULL,
    cost_price        DECIMAL(15,2) NOT NULL DEFAULT 0,
    sale_price        DECIMAL(15,2) NOT NULL DEFAULT 0,
    profit_amount     DECIMAL(15,2) NOT NULL DEFAULT 0,
    status            ENUM('success','pending','failed') NOT NULL DEFAULT 'success',
    note              VARCHAR(255) DEFAULT NULL,
    created_by        INT UNSIGNED DEFAULT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pulsatrx_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_pulsatrx_product FOREIGN KEY (pulsa_product_id) REFERENCES pulsa_products(id) ON DELETE RESTRICT,
    CONSTRAINT fk_pulsatrx_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 8. GADAI BARANG (PAWN)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pawns (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id         INT UNSIGNED NOT NULL,
    customer_id       INT UNSIGNED NOT NULL,
    pawn_no           VARCHAR(40) NOT NULL UNIQUE,
    item_name         VARCHAR(150) NOT NULL,
    item_description  VARCHAR(255) DEFAULT NULL,
    estimated_value   DECIMAL(15,2) NOT NULL DEFAULT 0,
    loan_amount       DECIMAL(15,2) NOT NULL DEFAULT 0,
    interest_rate     DECIMAL(5,2) NOT NULL DEFAULT 0 COMMENT 'persen per bulan',
    pawn_date         DATE NOT NULL,
    due_date          DATE NOT NULL,
    status            ENUM('active','redeemed','overdue','auctioned') NOT NULL DEFAULT 'active',
    redeemed_at       DATETIME DEFAULT NULL,
    created_by        INT UNSIGNED DEFAULT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pawn_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_pawn_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    CONSTRAINT fk_pawn_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pawn_payments (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pawn_id         INT UNSIGNED NOT NULL,
    payment_date    DATE NOT NULL,
    type            ENUM('interest','redemption','extension') NOT NULL,
    amount          DECIMAL(15,2) NOT NULL,
    note            VARCHAR(255) DEFAULT NULL,
    created_by      INT UNSIGNED DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pawnpay_pawn FOREIGN KEY (pawn_id) REFERENCES pawns(id) ON DELETE CASCADE,
    CONSTRAINT fk_pawnpay_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 9. PINJAM UANG (LOAN)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS loans (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id         INT UNSIGNED NOT NULL,
    customer_id       INT UNSIGNED NOT NULL,
    loan_no           VARCHAR(40) NOT NULL UNIQUE,
    principal_amount  DECIMAL(15,2) NOT NULL DEFAULT 0,
    interest_rate     DECIMAL(5,2) NOT NULL DEFAULT 0 COMMENT 'persen per bulan',
    term_months       SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    loan_date         DATE NOT NULL,
    due_date          DATE NOT NULL,
    status            ENUM('active','paid_off','overdue','default') NOT NULL DEFAULT 'active',
    created_by        INT UNSIGNED DEFAULT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_loan_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_loan_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    CONSTRAINT fk_loan_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS loan_installments (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    loan_id           INT UNSIGNED NOT NULL,
    installment_no    SMALLINT UNSIGNED NOT NULL,
    due_date          DATE NOT NULL,
    principal_amount  DECIMAL(15,2) NOT NULL DEFAULT 0,
    interest_amount   DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_amount      DECIMAL(15,2) NOT NULL DEFAULT 0,
    paid_amount       DECIMAL(15,2) NOT NULL DEFAULT 0,
    paid_date         DATE DEFAULT NULL,
    status            ENUM('unpaid','partial','paid','overdue') NOT NULL DEFAULT 'unpaid',
    CONSTRAINT fk_installment_loan FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    UNIQUE KEY uq_loan_installment (loan_id, installment_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS loan_payments (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    loan_id           INT UNSIGNED NOT NULL,
    installment_id    BIGINT UNSIGNED DEFAULT NULL,
    payment_date      DATE NOT NULL,
    amount            DECIMAL(15,2) NOT NULL,
    note              VARCHAR(255) DEFAULT NULL,
    created_by        INT UNSIGNED DEFAULT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_loanpay_loan FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    CONSTRAINT fk_loanpay_installment FOREIGN KEY (installment_id) REFERENCES loan_installments(id) ON DELETE SET NULL,
    CONSTRAINT fk_loanpay_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 10. AUDIT LOG
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS audit_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED DEFAULT NULL,
    branch_id       INT UNSIGNED DEFAULT NULL,
    module          VARCHAR(50) NOT NULL,
    action           VARCHAR(50) NOT NULL COMMENT 'create|update|delete|void|login|logout|backup|restore',
    description      VARCHAR(500) DEFAULT NULL,
    ip_address       VARCHAR(45) DEFAULT NULL,
    user_agent       VARCHAR(255) DEFAULT NULL,
    old_data         JSON DEFAULT NULL,
    new_data         JSON DEFAULT NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_audit_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 11. PENGATURAN SISTEM (SETTINGS)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
    `key`         VARCHAR(60) NOT NULL PRIMARY KEY,
    `value`       TEXT DEFAULT NULL,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Indexes tambahan untuk performa laporan & pencarian
-- ---------------------------------------------------------------------
CREATE INDEX idx_products_branch_active ON products(branch_id, is_active, deleted_at);
CREATE INDEX idx_sales_branch_date ON sales(branch_id, sale_date);
CREATE INDEX idx_pulsatrx_branch_date ON pulsa_transactions(branch_id, created_at);
CREATE INDEX idx_pawns_branch_status ON pawns(branch_id, status);
CREATE INDEX idx_loans_branch_status ON loans(branch_id, status);
CREATE INDEX idx_audit_module_date ON audit_logs(module, created_at);
CREATE INDEX idx_customers_branch ON customers(branch_id);
CREATE INDEX idx_stockmove_product ON stock_movements(product_id, created_at);

SET FOREIGN_KEY_CHECKS = 1;
