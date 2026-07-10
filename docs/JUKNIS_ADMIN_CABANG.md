# Petunjuk Teknis (Juknis) — ADMIN CABANG

**Aplikasi**: POS Multi Usaha
**Untuk**: Penanggung jawab operasional harian di satu lokasi/cabang usaha
**Ruang lingkup akses**: Hanya data cabang Anda sendiri — penjualan,
pembelian, stok, supplier, pelanggan, gadai, pinjaman, pulsa/data/top up,
transfer/setor bank, servis HP, dan laporan (termasuk laba) khusus cabang Anda.

> Anda **tidak bisa** melihat atau mengubah data cabang lain, dan **tidak
> bisa** mengelola akun pengguna/cabang/pengaturan sistem — itu wewenang
> Super Admin.

---

## 1. Login & Keamanan Akun

1. Buka alamat website aplikasi, masukkan **username** dan **password** yang
   diberikan Super Admin.
2. Klik **Masuk**.
3. **Segera ganti password**: klik nama Anda (pojok kanan atas) → **Profil
   Saya** → isi password lama & baru → **Simpan Perubahan**.
4. Jika salah password 5 kali berturut-turut, akun terkunci otomatis 15 menit.
5. Jika tidak ada aktivitas ±30 menit, sistem otomatis logout — cukup login
   ulang, data yang belum disimpan mungkin perlu diisi ulang.

---

## 2. Memahami Dashboard Cabang

Setelah login, halaman **Dashboard** menampilkan ringkasan cabang Anda hari ini:

- Jumlah & total transaksi **penjualan** dan **pulsa/data/top up**.
- Jumlah & total transaksi **transfer/setor tunai**.
- Jumlah **gadai aktif** & total dana yang belum ditebus (outstanding).
- Jumlah **pinjaman aktif** & total tagihan belum terbayar.
- Jumlah **servis HP** yang sedang berjalan dan yang sudah siap diambil.
- **Laba Hari Ini** (penjualan aksesoris + pulsa/data/top up + transfer/setor bank).
- Daftar **produk stok menipis** — segera lakukan pembelian/restock.

Gunakan halaman ini setiap pagi untuk mengecek kondisi cabang.

---

## 3. Mengelola Produk & Kategori

Menu sidebar: **Produk**, **Kategori**.

### Menambah produk baru
1. Menu **Produk** → **+ Tambah Produk**.
2. Isi **SKU** (kode unik produk), **Barcode** (opsional, untuk scan di kasir),
   **Nama Produk**, **Kategori**, **Satuan** (pcs/box/dll).
3. Isi **Harga Beli** (modal) dan **Harga Jual**.
4. Isi **Stok Awal** dan **Stok Minimum** (batas untuk peringatan stok menipis).
5. Klik **Simpan**.

### Menyesuaikan stok (mis. setelah stock opname / barang rusak)
1. Buka **Ubah** pada produk terkait.
2. Scroll ke bagian **Penyesuaian Stok**.
3. Isi jumlah (positif untuk menambah, mis. `5`; negatif untuk mengurangi,
   mis. `-3`) dan **Catatan alasan**.
4. Klik **Sesuaikan Stok**. Riwayat perubahan tercatat di tabel di bawahnya.

### Kategori
Menu **Kategori** untuk mengelompokkan produk (mis. Casing, Charger & Kabel).
Tambah/ubah/hapus sama seperti data master pada umumnya.

---

## 4. Mengelola Supplier & Pembelian (Stok Masuk)

Menu sidebar: **Supplier**, **Pembelian**.

### Menambah supplier
Menu **Supplier** → **+ Tambah Supplier** → isi nama, kontak, telepon, alamat → **Simpan**.

### Mencatat pembelian barang (menambah stok)
1. Menu **Pembelian** → **+ Tambah Pembelian**.
2. Pilih **Supplier** (opsional), isi **Tanggal Pembelian** dan **Catatan**.
3. Klik **+ Tambah Baris** untuk tiap produk yang dibeli: pilih produk, isi
   **Qty** dan **Harga Beli** (otomatis terisi dari harga produk, bisa diubah
   sesuai nota asli dari supplier).
4. Total otomatis terhitung. Klik **Simpan Pembelian**.
5. Stok produk akan **otomatis bertambah** sesuai qty yang dibeli.

---

## 5. Mengelola Pelanggan

Menu sidebar: **Pelanggan**. Data ini dipakai bersama oleh transaksi
Penjualan, Gadai, Pinjaman, Transfer/Setor Bank, dan Servis HP.

1. **+ Tambah Pelanggan** → isi Nama, Telepon, **No. KTP** (wajib untuk
   transaksi Gadai/Pinjaman), Alamat, Catatan.
2. Klik **Simpan**.

Anda juga bisa menambah pelanggan baru langsung dari formulir Gadai/Pinjaman/
Servis HP lewat tautan "Tambah pelanggan baru" tanpa harus pindah menu.

---

## 6. Transaksi Penjualan Aksesoris (Kasir Aksesoris)

Langkah sama seperti Kasir (lihat [`JUKNIS_KASIR.md`](JUKNIS_KASIR.md) §3),
**ditambah** dua kewenangan khusus Admin Cabang:

- **Boleh mengubah harga jual** saat transaksi di layar kasir (kolom harga
  bisa diedit manual sebelum diproses), untuk kasus nego harga.
- **Boleh membatalkan (void) transaksi** yang sudah tersimpan:
  1. Menu **Riwayat Penjualan** → klik **Detail** pada transaksi yang ingin dibatalkan.
  2. Isi **Alasan Pembatalan** (wajib diisi, akan tercatat di Log Audit).
  3. Klik **Batalkan Transaksi** → konfirmasi.
  4. Stok barang otomatis dikembalikan.

Gunakan wewenang void secara bijak — hanya untuk kesalahan input, bukan untuk
menutupi kecurangan kasir. Semua pembatalan tercatat permanen di Log Audit.

---

## 7. Pulsa, Paket Data & Top Up Saldo E-Wallet

Menu sidebar: **Pulsa, Data & Top Up**.

- Transaksi harian: sama seperti Kasir (pilih produk, isi nomor tujuan, Proses).
- **Khusus Admin Cabang**: tombol **Kelola Produk** untuk menambah/mengubah
  daftar produk pulsa/paket data/token PLN/**top up e-wallet** (Gopay,
  ShopeePay, OVO, DANA, dll) beserta harga modal & jual per provider. Lakukan
  ini saat provider mengubah harga modal atau ada produk baru yang ingin dijual.
- Saat menambah produk baru, pilih **Kategori** yang sesuai (Pulsa, Paket
  Data, Token PLN, **Top Up Saldo E-Wallet**, atau Lainnya) agar tampil pada
  grup yang tepat di layar transaksi.

---

## 8. Transfer / Setor Tunai / Tarik Tunai (Agen Semua Bank)

Menu sidebar: **Transfer / Setor Bank**.

### Melakukan transaksi
1. Pilih **Jenis Transaksi**: Transfer ke Rekening Lain, Setor Tunai, atau Tarik Tunai.
2. Pilih **Bank** tujuan, isi nomor rekening & nama pemilik rekening (untuk transfer).
3. Isi **Nominal Uang** dan **Biaya Jasa/Admin** (otomatis terisi dari
   Pengaturan Sistem, bisa disesuaikan sesuai kebijakan cabang atau negosiasi
   dengan pelanggan).
4. Klik **Proses & Cetak Struk**.
5. Biaya jasa yang tercatat menjadi **laba** transaksi ini — muncul otomatis
   di Dashboard dan Laporan Laba Rugi.

### Mengelola Daftar Bank
Tombol **Kelola Daftar Bank** untuk menambah bank baru atau menonaktifkan
bank yang sudah tidak dilayani.

### Membatalkan transaksi
Dari daftar transaksi, klik **Batalkan** pada baris terkait, isi alasan
pembatalan (wajib). Gunakan hanya untuk kesalahan input (mis. salah nominal
atau salah pilih bank), bukan untuk menutupi kesalahan operasional lainnya.

---

## 9. Servis HP

Menu sidebar: **Servis HP**.

### Menerima servis baru
1. **+ Terima Servis Baru** → pilih **Pelanggan** (atau tambah baru).
2. Isi **Merk/Tipe HP**, **IMEI/Serial** (opsional), **Keluhan Pelanggan**,
   dan **Kelengkapan yang Dititipkan** (charger, sim card, dus, dll — penting
   dicatat untuk menghindari klaim kehilangan barang titipan).
3. Isi **Perkiraan Biaya** dan (opsional) **Uang Muka (DP)** yang dibayar
   pelanggan saat itu.
4. Klik **Simpan Servis** → cetak **Bukti Servis** untuk diberikan ke pelanggan
   (berisi No. Servis sebagai bukti pengambilan nanti).

### Mengelola servis yang sedang berjalan
Buka **Detail** pada servis terkait:
- **Ubah Status Pengerjaan** — pilih Dikerjakan, Menunggu Sparepart, Selesai
  (Siap Diambil), atau Dibatalkan, sertakan catatan teknisi (mis. "Ganti LCD,
  menunggu sparepart datang"). Setiap perubahan status tercatat dengan waktu
  di riwayat status agar bisa dijawab cepat saat pelanggan menanyakan progres.
- **Serah Terima ke Pelanggan** — saat pelanggan mengambil unit: isi **Biaya
  Final** (bisa berbeda dari perkiraan awal) dan **Jumlah Dibayar Sekarang**
  (pelunasan sisa setelah dikurangi DP). Sistem otomatis menjumlahkan dengan
  DP yang sudah dibayar dan menandai status **Sudah Diambil**.

---

## 10. Gadai Barang

Menu sidebar: **Gadai Barang**.

### Membuat gadai baru
1. **+ Gadai Baru** → pilih **Pelanggan** (atau tambah baru).
2. Isi **Nama Barang**, **Deskripsi**, **Taksiran Nilai Barang**, **Jumlah
   Pinjaman** yang diberikan ke pelanggan, **Bunga per bulan** (otomatis
   terisi sesuai Pengaturan Sistem, bisa diubah), **Tanggal Gadai**, dan
   **Jatuh Tempo**.
3. Klik **Simpan Gadai** → surat bukti gadai bisa langsung dicetak.

### Mengelola gadai berjalan (buka menu **Detail** pada daftar gadai)
- **Bayar Bunga / Perpanjang** — catat pembayaran bunga bulanan, atau pilih
  "Perpanjangan" dan isi tanggal jatuh tempo baru jika pelanggan minta
  tambah waktu.
- **Tebus Barang** — saat pelanggan melunasi, isi jumlah tebusan lalu klik
  **Tandai Sudah Ditebus**. Status berubah menjadi *redeemed* dan barang
  bisa dikembalikan ke pelanggan.

---

## 11. Pinjam Uang

Menu sidebar: **Pinjam Uang**.

### Membuat pinjaman baru
1. **+ Pinjaman Baru** → pilih **Pelanggan**.
2. Isi **Jumlah Pinjaman**, **Bunga per bulan**, **Tenor (jumlah bulan)**, **Tanggal Pinjam**.
3. Klik **Simpan Pinjaman** — sistem **otomatis membuat jadwal angsuran
   bulanan** (pokok dibagi rata + bunga tetap tiap bulan). Anda tidak perlu
   menghitung manual.

### Menerima pembayaran angsuran
1. Buka **Detail** pinjaman terkait.
2. Pada tabel **Jadwal Angsuran**, klik **Bayar** di baris angsuran yang
   dilunasi pelanggan.
3. Isi jumlah yang dibayarkan (bisa sebagian/partial atau penuh) → **Simpan Pembayaran**.
4. Status pinjaman otomatis berubah **Lunas** setelah seluruh angsuran terbayar.

---

## 12. Saldo Kas Harian

Menu sidebar: **Saldo Kas Harian**.

1. Setiap tutup toko (atau kapan pun diperlukan), buka menu ini untuk melihat
   daftar sumber dana cabang Anda (mis. Kas Tunai, Saldo Bank, Gopay Merchant).
2. Hitung fisik/cek saldo aktual masing-masing sumber, lalu isi kolom **Saldo
   Akhir** untuk tiap baris.
3. Tambahkan **Catatan** bila ada selisih atau hal yang perlu dijelaskan.
4. Klik **Simpan Saldo Kas**. Anda bisa mengedit ulang nilai untuk tanggal
   yang sama kapan pun — data terakhir yang menimpa yang lama.

**Saldo Awal berjalan otomatis (rolling)**: saldo akhir yang disimpan Kasir
pada suatu hari otomatis menjadi **Saldo Awal** untuk hari berikutnya. Anda
tidak perlu menghitung/mengisi Saldo Awal secara manual setiap hari.

**Perbaikan tanggal lampau** *(khusus wewenang Anda dan Super Admin, izin
`cash.correct` — Kasir tidak memilikinya)*: jika Kasir salah input pada hari
sebelumnya, gunakan pemilih tanggal di bagian atas form untuk berpindah ke
tanggal yang bermasalah, lalu simpan ulang angka yang benar. Karena Saldo
Awal berjalan otomatis, perbaikan ini akan otomatis memperbaiki Saldo Awal
hari-hari sesudahnya juga — Anda tidak perlu mengoreksi satu per satu.
Gunakan tabel **Riwayat Saldo Kas** untuk meninjau catatan lama per rentang
tanggal.

> **Anda tidak bisa** menambah sumber dana baru atau mengubah **Saldo Awal**
> pertama kali (saat sumber dana dibuat) — itu wewenang eksklusif Super Admin
> (lihat §14).

---

## 13. Melihat Laporan Cabang Anda

Menu sidebar: **Laporan** — semua laporan (Penjualan, **Laba Rugi**, Stok,
Gadai, Pinjaman, Pulsa/Data/Top Up, Transfer/Setor Bank, Servis HP, **Saldo
Kas Harian**) otomatis hanya menampilkan data **cabang Anda**. Gunakan filter
tanggal untuk laporan harian/mingguan/bulanan, dan tombol **Ekspor CSV** bila
perlu diolah di Excel atau dilaporkan ke pemilik usaha.

---

## 14. Checklist Rutin Admin Cabang

| Frekuensi | Tugas |
|---|---|
| Setiap buka toko | Cek Dashboard: stok menipis, gadai/pinjaman jatuh tempo hari ini |
| Setiap ada barang masuk | Catat di menu **Pembelian** agar stok akurat |
| Harian | Pantau **Riwayat Penjualan**, tangani transaksi bermasalah (void bila perlu) |
| Harian | Cek transaksi **Pulsa/Top Up** yang berstatus gagal/pending, tindak lanjuti ke provider |
| Harian | Perbarui **status servis HP** yang sedang dikerjakan agar pelanggan bisa ditanya progresnya |
| Setiap tutup toko | Isi/verifikasi **Saldo Kas Harian** (kas tunai, bank, e-wallet merchant) |
| Mingguan | Review **Laporan Laba Rugi** cabang |
| Saat jatuh tempo gadai/pinjaman mendekat | Hubungi pelanggan untuk pembayaran/perpanjangan |

---

## 15. Yang BUKAN Wewenang Admin Cabang

- Tidak bisa menambah/menghapus **Cabang** lain atau melihat datanya.
- Tidak bisa membuat/menghapus akun **Pengguna** atau mengubah **Role & Hak Akses**.
- Tidak bisa mengakses **Backup & Restore** database atau **Pengaturan Sistem**.
- Tidak bisa menambah **Sumber Dana Saldo Kas** baru atau mengubah **Saldo
  Awal**-nya — hanya bisa mencatat saldo akhir harian (izin `cash.manage`
  khusus Super Admin).

Untuk kebutuhan di atas, hubungi **Super Admin** (lihat [`JUKNIS_SUPER_ADMIN.md`](JUKNIS_SUPER_ADMIN.md)).

---

## 16. Troubleshooting Cepat

| Kendala | Solusi |
|---|---|
| Kasir di cabang saya tidak bisa login | Minta Super Admin memeriksa status akun kasir tsb |
| Stok produk tidak sesuai fisik | Gunakan fitur **Penyesuaian Stok** pada halaman Ubah Produk, isi alasan yang jelas |
| Transaksi salah input (penjualan/bank) | Gunakan fitur **Batalkan Transaksi (void)** dari halaman Detail/daftar, isi alasan |
| Tidak melihat menu Laporan Laba | Hubungi Super Admin untuk memastikan izin `reports.profit` aktif untuk role Admin Cabang |
| Servis HP tidak muncul di daftar | Pastikan filter status di menu Servis HP tidak sedang menyaring status lain |
| Sumber dana baru (mis. rekening bank baru) belum ada di menu Saldo Kas Harian | Minta Super Admin menambahkannya lewat **Kelola Sumber Dana** — Admin Cabang tidak bisa menambah sendiri |
| Lupa password | Minta Super Admin mereset lewat menu Pengguna, atau ganti sendiri jika masih ingat password lama |
