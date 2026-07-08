# POS Multi Usaha

Aplikasi Point of Sale (POS) multi-usaha berbasis web untuk mengelola dalam
satu sistem: **penjualan aksesoris HP**, **pulsa & paket data**, **gadai
barang**, **jasa pinjam uang**, dan **multi cabang** — dirancang khusus agar
dapat berjalan di **shared hosting standar** (tanpa SSH, Node.js, Python,
Composer, Git, Cron Job, maupun PHP Selector khusus).

## Teknologi

- **PHP 8.3 Native** (tanpa framework) — arsitektur MVC sederhana buatan sendiri.
- **HTML5 + CSS3** murni (tanpa Bootstrap/Tailwind).
- **JavaScript Vanilla** (tanpa jQuery/React/Vue).
- **MySQL/MariaDB** dengan storage engine **InnoDB** (mendukung `FOREIGN KEY` & transaksi ACID).

## 1. Analisis Kebutuhan Singkat

**Aktor**: Super Admin, Admin Cabang, Kasir (lihat detail hak akses di
[`docs/USER_GUIDE.md`](docs/USER_GUIDE.md)).

**Modul fungsional**:

1. Penjualan aksesoris HP (POS kasir, cetak struk, void, riwayat)
2. Pulsa & paket data (transaksi + kelola produk provider)
3. Gadai barang (buat gadai, bayar bunga/perpanjang, tebus, cetak surat gadai)
4. Jasa pinjam uang (buat pinjaman, jadwal angsuran otomatis, pembayaran angsuran)
5. Multi cabang (setiap transaksi & stok terikat ke satu cabang; Super Admin lintas cabang)
6. Data master (kategori, produk, supplier, pelanggan, pembelian/stok masuk)
7. Laporan (penjualan, laba rugi, stok, gadai, pinjaman, pulsa/data — dengan ekspor CSV)
8. Backup & Restore database (murni PHP, tanpa `mysqldump`/SSH)
9. Log audit (jejak seluruh aksi penting per pengguna/cabang/waktu/IP)
10. Hak akses (RBAC berbasis permission per modul, dapat disesuaikan per role)

**Non-fungsional**: ringan & cepat (tanpa framework berat), aman (lihat bagian
Keamanan), responsif (mobile-first CSS), multi-user (transaksi dibungkus
database transaction untuk mencegah race condition pada stok), mudah dipelihara
(struktur MVC + Separation of Concerns), mudah dikembangkan (autoload sederhana,
konvensi konsisten antar modul).

## 2. Desain Database (ERD)

Lihat [`docs/ERD.md`](docs/ERD.md) untuk diagram relasi lengkap dan penjelasan
prinsip desain (soft delete, histori harga, jejak mutasi stok, transaksi DB).
Skema SQL siap pakai: [`database/schema.sql`](database/schema.sql) dan data
awal: [`database/seed.sql`](database/seed.sql).

## 3. Struktur Folder

```
/ (docroot)
├── index.php               # Front controller (satu-satunya pintu masuk)
├── .htaccess                # URL rewriting + header keamanan
├── app/
│   ├── config/                # config.php, database.php
│   ├── core/                   # Router, Model/Controller dasar, Auth, Csrf,
│   │                            # Security, Validator, AuditLogger, Flash, BackupService
│   ├── controllers/             # 1 controller per modul (Auth, Sales, Pawn, Loan, dst)
│   ├── models/                   # Akses data via PDO + prepared statement
│   └── views/                     # layouts/ (partial header/sidebar/footer) + 1 folder per modul
├── assets/
│   ├── css/app.css             # Design system vanilla CSS (responsif)
│   └── js/                      # app.js (global UI), pos.js (logika kasir)
├── database/
│   ├── schema.sql               # Struktur seluruh tabel (InnoDB + FK)
│   └── seed.sql                  # Role/permission/akun awal + contoh data
├── storage/
│   ├── backups/                 # Hasil backup database (writable)
│   ├── logs/                     # Log error PHP (writable)
│   └── uploads/                   # (writable, cadangan untuk kebutuhan mendatang)
└── docs/
    ├── INSTALLATION.md           # Panduan instalasi di shared hosting
    ├── ERD.md                     # Desain database
    ├── API.md                      # Dokumentasi endpoint internal (AJAX)
    └── USER_GUIDE.md                # Panduan penggunaan per role
```

## 4. Desain UI/UX

- **Layout aplikasi**: sidebar navigasi (menu menyesuaikan hak akses pengguna
  yang login), topbar dengan info cabang & menu akun, area konten dengan kartu
  (card-based) dan tabel data yang konsisten di semua modul.
- **Responsif**: sidebar otomatis tersembunyi di layar sempit (mobile) dengan
  tombol hamburger; grid statistik & form menyesuaikan jumlah kolom mengikuti
  lebar layar (CSS Grid + breakpoint).
- **Kasir (POS)**: tata letak dua kolom — grid produk yang dapat dicari/scan
  di kiri, keranjang & ringkasan pembayaran di kanan — dioptimalkan untuk alur
  transaksi cepat.
- **Struk & surat cetak**: layout khusus lebar 300px ala thermal printer untuk
  struk penjualan/pulsa, dan layout A5 sederhana untuk surat gadai/pinjaman,
  dengan tombol cetak otomatis (`window.print()`).
- **Konsistensi komponen**: satu file `assets/css/app.css` sebagai design
  system (warna, tombol, badge status, form, alert, modal, pagination) dipakai
  di seluruh modul agar tampilan seragam tanpa duplikasi CSS.

## 5. Keamanan

| Ancaman | Mitigasi |
|---|---|
| SQL Injection | Seluruh query memakai PDO **prepared statement** (lihat `app/core/Model.php`, `Database.php`). Tidak ada string SQL yang dibangun dari input pengguna secara langsung. |
| XSS | Fungsi `e()` (escape output) dipakai konsisten di seluruh view; input dibersihkan lewat `Security::clean()`. |
| CSRF | Token per-sesi (`Csrf` class) wajib disertakan pada seluruh form POST dan divalidasi di controller sebelum memproses data. |
| Password lemah/bocor | `password_hash()`/`password_verify()` (bcrypt), tidak pernah menyimpan/menampilkan password asli. |
| Brute-force login | Penguncian akun otomatis 15 menit setelah 5 kali gagal login berturut-turut. |
| Sesi menggantung | Session timeout otomatis (default 30 menit, dapat dikonfigurasi), cookie sesi `HttpOnly` + `SameSite=Lax` (+ `Secure` otomatis saat HTTPS). |
| Akses tidak sah antar modul/cabang | RBAC berbasis permission (`Auth::can()`) di setiap aksi controller, plus pembatasan query ke `branch_id` milik pengguna. |
| Kehilangan data akibat aksi keliru | Soft delete (`deleted_at`) pada tabel master, jejak audit (`audit_logs`) untuk seluruh aksi penting. |
| Directory traversal / akses file sensitif | `.htaccess` menolak akses langsung ke folder `app/`, `database/`, `storage/`; nama file backup disanitasi (`Security::safeFilename()`). |

## 6. Dokumentasi Lengkap

- [`docs/INSTALLATION.md`](docs/INSTALLATION.md) — panduan instalasi langkah demi langkah di shared hosting (cPanel).
- [`docs/ERD.md`](docs/ERD.md) — desain database & ERD.
- [`docs/API.md`](docs/API.md) — dokumentasi endpoint internal (dipakai oleh JavaScript kasir).
- [`docs/USER_GUIDE.md`](docs/USER_GUIDE.md) — panduan penggunaan per modul & per role.

## 7. Login Default (setelah import `seed.sql`)

```
Username : superadmin
Password : Admin123!
```

**Wajib diganti** setelah login pertama kali melalui menu **Profil Saya**.
