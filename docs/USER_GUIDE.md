# Panduan Penggunaan — POS Multi Usaha

## Peran (Role) & Hak Akses

| Role | Ruang Lingkup | Hak Akses Utama |
|---|---|---|
| **Super Admin** | Seluruh cabang | Akses penuh: semua modul, semua cabang, laporan laba, log audit, kelola pengguna/role, backup & restore, pengaturan sistem. |
| **Admin Cabang** | Hanya cabangnya sendiri | Penjualan, pembelian, stok, supplier, pelanggan, gadai, pinjaman, pulsa/data, laporan cabangnya sendiri (termasuk laba). |
| **Kasir** | Hanya cabangnya sendiri | Membuat transaksi (penjualan, pulsa/data, gadai, pinjaman) dan mencetak struk saja. **Tidak dapat** mengubah harga jual saat transaksi, membatalkan (void) transaksi, atau melihat laporan laba. |

Hak akses setiap role (kecuali Super Admin, yang selalu penuh) dapat disesuaikan
lebih detail oleh Super Admin melalui menu **Role & Hak Akses**.

---

## 1. Login & Keamanan Akun

1. Buka halaman utama aplikasi, masukkan username & password.
2. Setelah 5 kali percobaan login gagal berturut-turut, akun akan **terkunci
   otomatis selama 15 menit** sebagai proteksi brute-force.
3. Sesi otomatis berakhir jika tidak ada aktivitas selama waktu tertentu
   (default 30 menit, dapat diubah Super Admin di menu Pengaturan).
4. Ganti password kapan saja melalui menu **Profil Saya** (klik nama Anda di
   pojok kanan atas).

## 2. Dashboard

Menampilkan ringkasan transaksi hari ini (penjualan, pulsa/data), jumlah
gadai & pinjaman aktif beserta total outstanding, serta daftar produk dengan
stok menipis. Kartu **Laba Hari Ini** hanya tampil untuk role yang memiliki
izin `reports.profit` (Super Admin & Admin Cabang).

## 3. Data Master

- **Cabang** (Super Admin): tambah/ubah/nonaktifkan cabang usaha.
- **Pengguna** (Super Admin): kelola akun kasir/admin cabang, tetapkan role & cabang.
- **Role & Hak Akses** (Super Admin): sesuaikan permission Admin Cabang & Kasir.
- **Kategori & Produk**: kelola katalog aksesoris HP beserta harga beli/jual dan stok minimum.
- **Supplier**: data pemasok untuk transaksi pembelian.
- **Pelanggan**: data pelanggan dipakai bersama oleh modul Penjualan, Gadai, dan Pinjaman.

> **Catatan**: Super Admin tidak terikat ke satu cabang, sehingga saat mengelola
> produk harus memilih cabang terlebih dahulu dari dropdown di menu Produk. Untuk
> membuat transaksi (POS, Pulsa, Gadai, Pinjaman), gunakan akun Admin Cabang/Kasir
> karena transaksi selalu terikat ke satu cabang spesifik.

## 4. Pembelian (Stok Masuk)

Menu **Pembelian** → **Tambah Pembelian** → pilih supplier (opsional), tambahkan
baris produk beserta qty & harga beli, simpan. Stok produk bertambah otomatis
dan tercatat di riwayat pergerakan stok pada halaman edit produk.

## 5. Kasir Aksesoris (POS)

1. Buka menu **Kasir Aksesoris**.
2. Cari produk lewat kotak pencarian (nama/SKU) atau scan barcode langsung ke kotak pencarian lalu Enter.
3. Klik kartu produk untuk menambah ke keranjang; atur qty dengan tombol +/-.
4. Pilih pelanggan (opsional), isi diskon bila ada, pilih metode bayar.
5. Isi jumlah dibayar — kembalian dihitung otomatis.
6. Klik **Proses & Cetak Struk** — struk akan otomatis terbuka untuk dicetak.
7. Kasir **tidak dapat mengubah harga jual** pada langkah ini; hanya Admin
   Cabang/Super Admin (izin `sales.edit_price`) yang bisa.
8. Pembatalan transaksi (void) hanya dapat dilakukan dari halaman **Riwayat
   Penjualan → Detail** oleh pengguna dengan izin `sales.void`, dan otomatis
   mengembalikan stok.

## 6. Pulsa & Paket Data

Menu **Pulsa & Paket Data** menampilkan form transaksi cepat (pilih produk,
isi nomor tujuan) di bagian atas, dan riwayat transaksi di bawahnya. Daftar
produk (nominal, harga modal/jual per provider) dikelola terpisah melalui
tombol **Kelola Produk** (izin `pulsa.manage_product`).

## 7. Gadai Barang

1. Menu **Gadai Barang** → **Gadai Baru**.
2. Pilih pelanggan (atau tambah pelanggan baru dari tautan yang tersedia),
   isi detail barang, taksiran nilai, jumlah pinjaman, bunga per bulan, dan jatuh tempo.
3. Setelah disimpan, halaman detail menyediakan:
   - **Bayar Bunga / Perpanjang** — mencatat pembayaran bunga bulanan atau
     memperpanjang jatuh tempo.
   - **Tebus Barang** — melunasi dan menutup transaksi gadai (status menjadi *redeemed*).
4. Surat bukti gadai dapat dicetak dari tombol **Cetak Surat Gadai**.

## 8. Pinjam Uang

1. Menu **Pinjam Uang** → **Pinjaman Baru**.
2. Isi jumlah pinjaman, bunga per bulan, dan tenor (jumlah bulan).
3. Sistem otomatis membuat **jadwal angsuran bulanan** (pokok rata + bunga
   tetap per bulan) begitu data disimpan.
4. Pada halaman detail, klik **Bayar** di baris angsuran yang ingin dilunasi
   sebagian/penuh. Status pinjaman otomatis berubah menjadi **Lunas** setelah
   seluruh angsuran terbayar.

## 9. Laporan

Menu **Laporan** menyediakan:

- **Laporan Penjualan** — rekap transaksi per rentang tanggal, dapat diekspor ke CSV.
- **Laporan Laba Rugi** *(khusus izin `reports.profit`)* — laba harian dari penjualan aksesoris dan pulsa/data.
- **Laporan Stok** — posisi stok & nilai stok per produk, dapat diekspor ke CSV.
- **Laporan Gadai** — daftar transaksi gadai & total outstanding pinjaman gadai.
- **Laporan Pinjaman** — daftar pinjaman & total outstanding tagihan.
- **Laporan Pulsa & Data** — rekap transaksi pulsa/paket data.

## 10. Log Audit

Setiap aksi penting (login/logout, tambah/ubah/hapus data, pembatalan transaksi,
backup/restore) otomatis tercatat di menu **Log Audit** beserta waktu, pengguna,
cabang, dan alamat IP — dapat difilter berdasarkan modul, cabang, dan tanggal.

## 11. Backup & Restore

Lihat menu **Backup & Restore** (khusus Super Admin). Backup dibuat murni
dengan PHP (tanpa `mysqldump`) sehingga tetap berjalan di shared hosting tanpa
akses shell. File backup dapat diunduh sebagai cadangan offline, atau
digunakan untuk memulihkan data kapan saja. **Restore akan menimpa seluruh
data yang ada** — selalu buat backup terbaru sebelum melakukan restore.

## 12. Pengaturan Sistem

Menu **Pengaturan** (khusus Super Admin) mengatur nama aplikasi, mata uang,
zona waktu, lama timeout sesi, suku bunga default gadai/pinjaman, dan catatan
kaki struk.
