# Panduan Penggunaan — POS Multi Usaha

## Peran (Role) & Hak Akses

| Role | Ruang Lingkup | Hak Akses Utama |
|---|---|---|
| **Super Admin** | Seluruh cabang | Akses penuh: semua modul, semua cabang, laporan laba, log audit, kelola pengguna/role, backup & restore, pengaturan sistem. |
| **Admin Cabang** | Hanya cabangnya sendiri | Penjualan, pembelian, stok, supplier, pelanggan, gadai, pinjaman, pulsa/data/top up, transfer/setor bank, servis HP, catat saldo akhir kas, laporan cabangnya sendiri (termasuk laba). |
| **Kasir** | Hanya cabangnya sendiri | Membuat transaksi (penjualan, pulsa/data/top up, gadai, pinjaman, transfer/setor bank, servis HP) dan mencatat **saldo akhir kas harian**, serta mencetak struk. **Tidak dapat** mengubah harga jual saat transaksi, membatalkan (void) transaksi, mengubah saldo awal/sumber dana kas, atau melihat laporan laba. |

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

Menampilkan ringkasan transaksi hari ini (penjualan, pulsa/data/top up,
transfer/setor bank), jumlah gadai & pinjaman aktif beserta total outstanding,
jumlah servis HP yang sedang berjalan/siap diambil, serta daftar produk dengan
stok menipis. Kartu **Laba Hari Ini** hanya tampil untuk role yang memiliki
izin `reports.profit` (Super Admin & Admin Cabang).

## 3. Data Master

- **Cabang** (Super Admin): tambah/ubah/nonaktifkan cabang usaha.
- **Pengguna** (Super Admin): kelola akun kasir/admin cabang, tetapkan role & cabang.
- **Role & Hak Akses** (Super Admin): sesuaikan permission Admin Cabang & Kasir.
- **Kategori & Produk**: kelola katalog aksesoris HP beserta harga beli/jual dan stok minimum.
- **Supplier**: data pemasok untuk transaksi pembelian.
- **Pelanggan**: data pelanggan dipakai bersama oleh modul Penjualan, Gadai, Pinjaman, Transfer/Setor Bank, dan Servis HP.
- **Daftar Bank**: master data bank untuk modul Transfer/Setor Tunai (izin `bank.manage`).
- **Sumber Dana Saldo Kas**: keterangan sumber dana (mis. Kas Tunai, Saldo Bank,
  Gopay Merchant) dan **saldo awal** masing-masing — **khusus izin `cash.manage`,
  hanya dimiliki Super Admin**. Admin Cabang dan Kasir hanya bisa mencatat saldo
  akhir harian, tidak bisa menambah sumber dana baru atau mengubah saldo awal.

> **Catatan**: Super Admin tidak terikat ke satu cabang, sehingga saat mengelola
> produk harus memilih cabang terlebih dahulu dari dropdown di menu Produk. Untuk
> membuat transaksi (POS, Pulsa, Gadai, Pinjaman, Transfer/Setor Bank, Servis HP),
> gunakan akun Admin Cabang/Kasir karena transaksi selalu terikat ke satu cabang spesifik.

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

## 6. Pulsa, Paket Data & Top Up Saldo E-Wallet

Menu **Pulsa, Data & Top Up** menampilkan form transaksi cepat (pilih produk,
isi nomor tujuan) di bagian atas, dan riwayat transaksi di bawahnya. Produk
dikelompokkan per kategori (Pulsa, Paket Data, Token PLN, **Top Up E-Wallet**
seperti Gopay/ShopeePay/OVO/DANA, dan Lainnya). Daftar produk (nominal, harga
modal/jual per provider) dikelola terpisah melalui tombol **Kelola Produk**
(izin `pulsa.manage_product`).

## 7. Transfer / Setor Tunai / Tarik Tunai (Agen Semua Bank)

1. Menu **Transfer / Setor Bank**.
2. Pilih **Jenis Transaksi**: Transfer ke Rekening Lain, Setor Tunai, atau Tarik Tunai.
3. Pilih **Bank** tujuan dari daftar (dikelola Super Admin/Admin Cabang lewat **Kelola Daftar Bank**).
4. Isi nomor rekening & nama pemilik rekening (untuk transfer), nominal uang,
   dan **Biaya Jasa/Admin** (otomatis terisi sesuai Pengaturan Sistem, bisa disesuaikan).
5. Klik **Proses & Cetak Struk**.
6. Biaya jasa yang dibebankan ke pelanggan tercatat sebagai laba transaksi ini
   (muncul di Dashboard dan Laporan Laba Rugi).
7. Pembatalan (void) transaksi hanya dapat dilakukan oleh pengguna dengan izin
   `bank.void` (Admin Cabang/Super Admin), dengan alasan wajib diisi.

## 8. Servis HP

1. Menu **Servis HP** → **Terima Servis Baru**.
2. Pilih pelanggan, isi merk/tipe HP, keluhan, kelengkapan yang dititipkan,
   perkiraan biaya, dan (opsional) uang muka (DP).
3. Setelah disimpan, halaman detail menampilkan **riwayat status** dan dua aksi
   (khusus izin `service.manage`, biasanya Admin Cabang):
   - **Ubah Status Pengerjaan** — Dikerjakan, Menunggu Sparepart, Selesai (Siap
     Diambil), atau Dibatalkan, masing-masing tercatat dengan waktu & catatan teknisi.
   - **Serah Terima ke Pelanggan** — isi biaya final dan jumlah dibayar saat
     pengambilan, sistem otomatis menghitung sisa dari uang muka yang sudah dibayar
     dan menandai status **Sudah Diambil**.
4. Bukti tanda terima servis dapat dicetak dari tombol **Cetak Bukti Servis**
   segera setelah unit diterima (untuk diberikan ke pelanggan).

## 9. Gadai Barang

1. Menu **Gadai Barang** → **Gadai Baru**.
2. Pilih pelanggan (atau tambah pelanggan baru dari tautan yang tersedia),
   isi detail barang, taksiran nilai, jumlah pinjaman, bunga per bulan, dan jatuh tempo.
3. Setelah disimpan, halaman detail menyediakan:
   - **Bayar Bunga / Perpanjang** — mencatat pembayaran bunga bulanan atau
     memperpanjang jatuh tempo.
   - **Tebus Barang** — melunasi dan menutup transaksi gadai (status menjadi *redeemed*).
4. Surat bukti gadai dapat dicetak dari tombol **Cetak Surat Gadai**.

## 10. Pinjam Uang

1. Menu **Pinjam Uang** → **Pinjaman Baru**.
2. Isi jumlah pinjaman, bunga per bulan, dan tenor (jumlah bulan).
3. Sistem otomatis membuat **jadwal angsuran bulanan** (pokok rata + bunga
   tetap per bulan) begitu data disimpan.
4. Pada halaman detail, klik **Bayar** di baris angsuran yang ingin dilunasi
   sebagian/penuh. Status pinjaman otomatis berubah menjadi **Lunas** setelah
   seluruh angsuran terbayar.

## 11. Saldo Kas Harian

1. Menu **Saldo Kas Harian** menampilkan daftar sumber dana aktif cabang
   (mis. Kas Tunai, Saldo Bank, Gopay Merchant) untuk tanggal yang dipilih
   (default hari ini).
2. Setiap sumber dana menampilkan **Saldo Awal** dan kolom **Saldo Akhir**
   yang bisa diisi/diedit oleh Kasir atau Admin Cabang.
   - **Saldo Awal berjalan otomatis (rolling)**: begitu Kasir men-submit saldo
     akhir pada suatu hari, angka tersebut otomatis menjadi **saldo awal**
     untuk hari berikutnya. Nilai **saldo awal** yang diatur Super Admin di
     menu **Kelola Sumber Dana** hanya dipakai sebagai titik awal saat sumber
     dana pertama kali dibuat (sebelum ada catatan harian sama sekali).
3. Isi angka hasil hitung fisik (kas tunai di laci, saldo rekening bank, saldo
   Gopay Merchant, dst.) untuk setiap sumber dana, tambahkan catatan bila ada
   selisih, lalu klik **Simpan Saldo Kas**.
4. Data dapat diedit berulang kali pada hari yang sama (mis. jika di-cross
   check ulang) — setiap penyimpanan menimpa nilai sebelumnya untuk tanggal
   dan sumber dana yang sama.
5. **Kasir hanya bisa mengisi/mengubah saldo untuk hari ini.** Tanggal pada
   form input terkunci ke hari berjalan dan tidak bisa diganti oleh Kasir.
6. **Perbaikan tanggal lampau** *(khusus izin `cash.correct` — Admin Cabang
   dan Super Admin)*: jika terjadi kesalahan input pada hari-hari sebelumnya,
   Admin Cabang atau Super Admin dapat memilih tanggal lampau melalui
   pemilih tanggal (yang muncul khusus untuk mereka) lalu menyimpan ulang
   angka yang benar. Karena saldo awal berjalan otomatis, perbaikan ini akan
   otomatis memperbarui saldo awal hari-hari setelahnya tanpa perlu
   mengubah data satu per satu.
7. Tabel **Riwayat Saldo Kas** di bawahnya menampilkan histori tercatat,
   dapat difilter per rentang tanggal.
8. **Kelola Sumber Dana** (tombol di pojok kanan atas, khusus izin
   `cash.manage`/Super Admin) — menambah sumber dana baru, mengubah nama/
   keterangan, dan mengatur **saldo awal** pertama kali. Kasir dan Admin
   Cabang tidak melihat tombol ini.

## 12. Laporan

Menu **Laporan** menyediakan:

- **Laporan Penjualan** — rekap transaksi per rentang tanggal, dapat diekspor ke CSV.
- **Laporan Laba Rugi** *(khusus izin `reports.profit`)* — laba harian dari penjualan aksesoris, pulsa/data/top up, dan transfer/setor bank.
- **Laporan Stok** — posisi stok & nilai stok per produk, dapat diekspor ke CSV.
- **Laporan Gadai** — daftar transaksi gadai & total outstanding pinjaman gadai.
- **Laporan Pinjaman** — daftar pinjaman & total outstanding tagihan.
- **Laporan Pulsa, Data & Top Up** — rekap transaksi pulsa/paket data/e-wallet.
- **Laporan Transfer / Setor Tunai** — rekap transaksi agen bank & laba jasa layanan.
- **Laporan Servis HP** — rekap servis, nilai jasa, dan status pengambilan.
- **Laporan Saldo Kas Harian** — riwayat saldo akhir seluruh sumber dana, dapat diekspor ke CSV.

## 13. Log Audit

Setiap aksi penting (login/logout, tambah/ubah/hapus data, pembatalan transaksi,
backup/restore) otomatis tercatat di menu **Log Audit** beserta waktu, pengguna,
cabang, dan alamat IP — dapat difilter berdasarkan modul, cabang, dan tanggal.

## 14. Backup & Restore

Lihat menu **Backup & Restore** (khusus Super Admin). Backup dibuat murni
dengan PHP (tanpa `mysqldump`) sehingga tetap berjalan di shared hosting tanpa
akses shell. File backup dapat diunduh sebagai cadangan offline, atau
digunakan untuk memulihkan data kapan saja. **Restore akan menimpa seluruh
data yang ada** — selalu buat backup terbaru sebelum melakukan restore.

## 15. Pengaturan Sistem

Menu **Pengaturan** (khusus Super Admin) mengatur nama aplikasi, mata uang,
zona waktu, lama timeout sesi, suku bunga default gadai/pinjaman, biaya admin
default transfer/setor bank, dan catatan kaki struk.
