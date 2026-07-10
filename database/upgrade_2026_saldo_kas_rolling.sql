-- =====================================================================
-- POS Multi Usaha - Upgrade Script
-- Menyempurnakan modul Saldo Kas Harian:
--  - Saldo awal kini BERJALAN (rolling) otomatis: saldo akhir yang
--    disimpan Kasir pada suatu hari menjadi saldo awal hari berikutnya.
--    Tidak ada perubahan struktur tabel untuk ini (dihitung otomatis
--    oleh aplikasi dari data cash_balance_records yang sudah ada).
--  - Permission baru: cash.correct - memperbaiki saldo akhir pada
--    tanggal yang sudah lewat. Hanya diberikan ke Admin Cabang dan
--    Super Admin, TIDAK ke Kasir (Kasir hanya boleh mengisi/mengubah
--    saldo untuk hari ini).
--
-- CARA PAKAI: jalankan file ini SEKALI melalui phpMyAdmin (tab SQL) pada
-- database yang sudah memiliki modul Saldo Kas Harian (dari
-- upgrade_2026_saldo_kas.sql atau instalasi awal). Aman dijalankan
-- lebih dari sekali (idempotent).
--
-- Untuk instalasi BARU, tidak perlu file ini - database/schema.sql dan
-- database/seed.sql sudah mencakup seluruh perubahan pada file ini.
-- =====================================================================

SET NAMES utf8mb4;

INSERT IGNORE INTO permissions (code, module, name) VALUES
('cash.correct', 'cash', 'Perbaiki Saldo Kas Tanggal Lampau (Admin Cabang/Super Admin)');

-- Super Admin: otomatis mendapat semua permission (termasuk yang baru saja ditambahkan)
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'super_admin'
  AND p.code = 'cash.correct';

-- Admin Cabang: boleh memperbaiki saldo kas tanggal lampau
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'branch_admin'
  AND p.code = 'cash.correct';

-- Kasir SENGAJA tidak diberi cash.correct - hanya boleh mencatat hari ini.

-- Selesai. Muat ulang halaman aplikasi lalu login ulang agar perubahan
-- hak akses langsung berlaku.
