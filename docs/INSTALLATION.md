# Panduan Instalasi — POS Multi Usaha

Aplikasi ini dibangun khusus agar dapat berjalan di **shared hosting standar**
tanpa SSH, tanpa Node.js/Python, tanpa Composer, tanpa Git, tanpa Cron Job,
dan tanpa PHP Selector khusus — hanya membutuhkan **PHP 8.1+ (disarankan 8.3)**
dan **MySQL/MariaDB**, yang tersedia di hampir semua paket hosting cPanel.

## 1. Persyaratan Server

- PHP 8.1 atau lebih baru (native, tanpa ekstensi tambahan selain default: `pdo_mysql`, `mbstring`, `json`).
- MySQL 5.7+ / MariaDB 10.3+ (mendukung InnoDB & JSON).
- Apache dengan `mod_rewrite` aktif (untuk file `.htaccess`) — hampir semua shared hosting sudah mendukung ini secara default.

## 2. Upload File

1. Unduh/susun seluruh folder proyek ini menjadi satu paket (mis. `pos_app.zip`).
2. Login ke **cPanel** → **File Manager** (atau gunakan FTP/SFTP client seperti FileZilla).
3. Upload seluruh isi folder proyek ke direktori `public_html/` (atau ke subfolder/addon domain sesuai kebutuhan, mis. `public_html/pos/`).
4. Ekstrak file zip jika diupload dalam bentuk terkompresi.
5. Pastikan struktur berikut ada di root domain: `index.php`, `.htaccess`, folder `app/`, `assets/`, `database/`, `storage/`, `docs/`.

## 3. Buat Database MySQL

1. Di cPanel, buka **MySQL Databases**.
2. Buat database baru, misalnya `namauser_pos`.
3. Buat user MySQL baru beserta password yang kuat.
4. Tambahkan user tersebut ke database dengan **All Privileges**.
5. Catat: nama database, username, dan password — akan dipakai di langkah 5.

## 4. Import Struktur & Data Awal

1. Buka **phpMyAdmin** dari cPanel, pilih database yang baru dibuat.
2. Buka tab **Import**.
3. Import file `database/schema.sql` terlebih dahulu (struktur tabel).
4. Kemudian import file `database/seed.sql` (data awal: role, permission, akun Super Admin, pengaturan default, contoh data).

> Alternatif: gunakan fitur **SQL** di phpMyAdmin dan tempel isi kedua file secara berurutan.

> **Sudah pernah instal sebelumnya?** Tidak perlu instal ulang dari awal —
> cukup jalankan skrip migrasi yang sesuai sekali lewat tab **SQL** di
> phpMyAdmin pada database yang sudah berjalan:
> - `database/upgrade_2026_bank_service_ewallet.sql` (sebelum modul Transfer/Setor Bank, Servis HP, Top Up E-Wallet ditambahkan)
> - `database/upgrade_2026_saldo_kas.sql` (sebelum modul Saldo Kas Harian ditambahkan)

## 5. Konfigurasi Koneksi Database

Edit file `app/config/database.php` melalui File Manager (atau editor teks apa pun),
sesuaikan dengan kredensial pada langkah 3:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'namauser_pos');
define('DB_USER', 'namauser_posuser');
define('DB_PASS', 'password_anda');
```

## 6. Atur Izin Folder (Permission)

Pastikan folder berikut dapat ditulis oleh aplikasi (biasanya permission `755`
atau `775` sudah cukup di kebanyakan shared hosting):

- `storage/backups/`
- `storage/logs/`
- `storage/uploads/`

Ini dapat diatur lewat File Manager cPanel (klik kanan folder → Permissions).

## 7. Akses Aplikasi & Login Pertama

1. Buka domain/subfolder tempat aplikasi diupload melalui browser, mis. `https://tokoanda.com/`.
2. Login dengan akun default:
   - **Username**: `superadmin`
   - **Password**: `Admin123!`
3. **Segera ganti password** melalui menu **Profil Saya** (klik nama Anda di pojok kanan atas).
4. Buat data cabang tambahan melalui menu **Cabang** (jika memiliki lebih dari satu lokasi usaha).
5. Buat akun **Admin Cabang** dan **Kasir** melalui menu **Pengguna**, lalu tetapkan cabangnya masing-masing.

## 8. Tidak Ada Cron Job? Tidak Masalah

Aplikasi ini **tidak bergantung pada cron job**. Fitur backup database dijalankan
manual kapan saja melalui menu **Backup & Restore** (murni PHP, tanpa `mysqldump`/SSH).
Disarankan admin melakukan backup rutin secara manual (mis. setiap malam) dan
mengunduh salinannya ke komputer lokal/Google Drive.

## 9. Troubleshooting Umum

| Masalah | Solusi |
|---|---|
| Halaman blank / 500 Error | Cek `storage/logs/php_error.log`, biasanya kredensial database salah atau `mod_rewrite` tidak aktif. |
| URL selain halaman utama menampilkan 404 | Pastikan `mod_rewrite` aktif dan file `.htaccess` di root ter-upload (kadang FTP client menyembunyikan file berawalan titik — aktifkan "Show Hidden Files"). |
| Tidak bisa login | Pastikan `database/seed.sql` sudah diimpor sehingga akun `superadmin` tersedia. |
| Backup/upload gagal | Periksa permission folder `storage/backups` dan `storage/uploads`, serta batas `upload_max_filesize`/`post_max_size` di PHP hosting Anda. |
| Sesi selalu logout cepat | Sesuaikan **Timeout Sesi** di menu Pengaturan Sistem (default 30 menit). |

## 10. Struktur Folder Ringkas

```
/ (docroot)
├── index.php              # Front controller (satu-satunya pintu masuk)
├── .htaccess               # URL rewriting + header keamanan
├── app/
│   ├── config/              # Konfigurasi aplikasi & database
│   ├── core/                 # Router, Model/Controller dasar, Auth, keamanan
│   ├── controllers/           # Logika tiap modul
│   ├── models/                 # Akses data (PDO + prepared statement)
│   └── views/                   # Tampilan (layout, partial, halaman per modul)
├── assets/                # CSS & JavaScript vanilla
├── database/              # schema.sql & seed.sql
├── storage/               # backups/, logs/, uploads/ (harus writable)
└── docs/                  # Dokumentasi (folder ini)
```

Lihat juga: [`docs/USER_GUIDE.md`](USER_GUIDE.md) untuk panduan penggunaan
sehari-hari, [`docs/API.md`](API.md) untuk dokumentasi endpoint internal, dan
[`docs/ERD.md`](ERD.md) untuk desain database.
