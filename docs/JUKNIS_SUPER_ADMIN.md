# Petunjuk Teknis (Juknis) — SUPER ADMIN

**Aplikasi**: POS Multi Usaha
**Untuk**: Pemilik usaha / penanggung jawab utama sistem
**Ruang lingkup akses**: Seluruh cabang, seluruh modul, seluruh laporan (termasuk laba), pengaturan sistem, backup/restore, log audit, manajemen pengguna & hak akses.

> Dokumen ini ditulis agar bisa langsung dipakai oleh siapa pun yang
> menggantikan Anda mengelola sistem — tidak perlu latar belakang teknis.
> Ikuti langkah-langkah secara berurutan sesuai kebutuhan.

---

## 1. Tanggung Jawab Super Admin

Super Admin adalah **pemegang kendali tertinggi** sistem. Tugas utamanya:

1. Menyiapkan cabang, akun pengguna (Admin Cabang & Kasir), dan hak akses.
2. Memantau seluruh cabang lewat Dashboard & Laporan (termasuk laba/rugi).
3. Menjaga keamanan data: rutin backup, memeriksa Log Audit.
4. Mengatur parameter sistem (nama aplikasi, bunga default gadai/pinjaman, dll).
5. Menjadi tempat eskalasi jika Admin Cabang/Kasir mengalami kendala teknis.

**Super Admin sebaiknya TIDAK dipakai untuk transaksi harian** (POS, pulsa,
gadai, pinjaman) karena akun ini tidak terikat ke satu cabang — sistem akan
menolak dan meminta Anda memakai akun Admin Cabang/Kasir untuk transaksi.

---

## 2. Login Pertama Kali

1. Buka alamat website aplikasi di browser (mis. `https://tokoanda.com/`).
2. Masukkan:
   - Username: `superadmin`
   - Password: `Admin123!`
3. Klik **Masuk**.
4. **Segera ganti password default** ini (lihat langkah §9 di bawah) —
   jangan biarkan password bawaan dipakai lebih dari hari pertama instalasi.

Jika 5 kali salah memasukkan password berturut-turut, akun akan otomatis
terkunci selama 15 menit sebagai proteksi keamanan. Tunggu, lalu coba lagi.

---

## 3. Menyiapkan Cabang

Menu di sidebar: **Cabang** (grup "Administrasi Sistem").

1. Klik menu **Cabang** → tombol **+ Tambah Cabang**.
2. Isi:
   - **Kode Cabang** — singkatan unik, mis. `PST` untuk Pusat, `CB2` untuk cabang kedua.
   - **Nama Cabang**, **Alamat**, **Telepon**.
   - Centang **Aktif** agar cabang bisa langsung dipakai.
3. Klik **Simpan**.
4. Ulangi untuk setiap lokasi usaha yang Anda miliki.
5. Untuk menonaktifkan cabang yang tutup (bukan menghapus data), buka **Ubah** pada cabang tsb, hilangkan centang **Aktif**, simpan.

---

## 4. Menyiapkan Akun Pengguna (Admin Cabang & Kasir)

Menu sidebar: **Pengguna**.

1. Klik **Pengguna** → **+ Tambah Pengguna**.
2. Isi:
   - **Username** (dipakai untuk login, harus unik) & **Nama Lengkap**.
   - **Password** awal (beri tahu pengguna untuk menggantinya nanti).
   - **Role**:
     - Pilih **Admin Cabang** untuk penanggung jawab operasional satu cabang.
     - Pilih **Kasir** untuk staf yang hanya melayani transaksi & cetak struk.
   - **Cabang** — WAJIB dipilih untuk Admin Cabang & Kasir (menentukan data
     mana yang bisa mereka lihat/kelola). Biarkan kosong ("Semua Cabang")
     hanya untuk akun Super Admin lain (jarang dibutuhkan).
3. Klik **Simpan**.
4. Catat username & password awal, sampaikan ke pengguna terkait secara aman
   (jangan lewat chat/grup terbuka).

**Menonaktifkan/menghapus pengguna** (mis. karyawan resign): buka **Ubah**
pada pengguna tsb → hilangkan centang **Aktif** → **Simpan**. Ini menonaktifkan
login tanpa menghapus jejak transaksi yang pernah dibuat pengguna tsb.

---

## 5. Mengatur Hak Akses (Role & Permission)

Menu sidebar: **Role & Hak Akses**.

- Ada 3 role tetap: **Super Admin** (selalu penuh, tidak bisa diubah),
  **Admin Cabang**, dan **Kasir**.
- Untuk menyesuaikan hak akses Admin Cabang atau Kasir (mis. Anda ingin Kasir
  juga boleh melihat laporan penjualan tanpa laba):
  1. Klik **Kelola Hak Akses** pada baris role yang ingin diubah.
  2. Centang/hilangkan centang izin per modul (Penjualan, Pulsa, Gadai,
     Pinjaman, Laporan, dst).
  3. Klik **Simpan Hak Akses**.
- **Peringatan**: jangan memberi izin `sales.void` (Batalkan Transaksi),
  `sales.edit_price` (Ubah Harga), atau `reports.profit` (Lihat Laba) kepada
  role Kasir — ini melanggar prinsip pemisahan tugas (segregation of duty)
  yang sudah dirancang dalam sistem untuk mencegah kecurangan kasir.

---

## 6. Data Master Lintas Cabang

Beberapa data dikelola terpusat oleh Super Admin dan dipakai bersama semua cabang:

- **Kategori** produk aksesoris (mis. Casing, Charger, Aksesoris Lain).
- **Produk Pulsa, Data & Top Up** — menu **Pulsa, Data & Top Up → Kelola Produk**
  (daftar nominal & harga modal/jual per provider: pulsa/paket data seperti
  Telkomsel/Indosat, token PLN, dan top up e-wallet seperti Gopay/ShopeePay/OVO/DANA).
- **Daftar Bank** — menu **Transfer / Setor Bank → Kelola Daftar Bank**, berisi
  daftar bank yang bisa dipilih saat transaksi transfer/setor/tarik tunai
  (BCA, Mandiri, BNI, BRI, dll — sudah terisi otomatis dari `seed.sql`, tinggal
  tambah/nonaktifkan sesuai kebutuhan).
- **Sumber Dana Saldo Kas** — menu **Saldo Kas Harian → Kelola Sumber Dana**.
  **Ini wewenang eksklusif Super Admin** — Admin Cabang dan Kasir tidak bisa
  menambah sumber dana baru maupun mengubah saldo awalnya, hanya mencatat
  saldo akhir harian. Untuk setiap cabang, pastikan minimal ada sumber dana
  **Kas Tunai** (fisik di laci), **Saldo Bank** (rekening operasional cabang),
  dan sumber e-wallet merchant yang dipakai (mis. **Gopay Merchant**) — sudah
  disiapkan otomatis untuk cabang pertama dari `seed.sql`, tambahkan manual
  untuk cabang baru atau sumber dana lain yang dipakai cabang tsb.

Untuk **Produk aksesoris per cabang** (stok fisik), Super Admin perlu memilih
cabang dulu di menu **Produk** (dropdown "Pilih Cabang" muncul karena Super
Admin tidak terikat satu cabang) baru bisa menambah/mengubah produk cabang tsb.

---

## 7. Memantau Dashboard & Laporan Seluruh Cabang

- **Dashboard** menampilkan ringkasan hari ini: transaksi penjualan, pulsa,
  gadai aktif, pinjaman aktif, **Laba Hari Ini**, dan produk stok menipis
  (khusus Super Admin: lintas semua cabang).
- Menu **Laporan** menyediakan 6 laporan: Penjualan, **Laba Rugi** (khusus
  Super Admin & Admin Cabang), Stok, Gadai, Pinjaman, Pulsa & Data — semua
  bisa difilter per tanggal, dan sebagian bisa diekspor ke **CSV** (untuk
  dibuka di Excel).
- Rekomendasi: cek Dashboard tiap pagi, dan Laporan Laba Rugi tiap akhir pekan/bulan.

---

## 8. Log Audit — Mengawasi Aktivitas

Menu sidebar: **Log Audit**.

Setiap login/logout, tambah/ubah/hapus data, pembatalan transaksi, dan
backup/restore tercatat otomatis di sini beserta **siapa**, **kapan**,
**dari cabang mana**, dan **alamat IP**-nya.

Gunakan filter (modul, cabang, tanggal) untuk:
- Menyelidiki transaksi yang dibatalkan (void) — cek modul `sales`, aksi `void`.
- Memastikan tidak ada perubahan data mencurigakan di luar jam kerja.
- Audit rutin bulanan sebagai bagian dari kontrol internal usaha.

---

## 9. Mengganti Password / Profil Sendiri

1. Klik nama Anda di pojok kanan atas → **Profil Saya**.
2. Isi **Password Saat Ini** dan **Password Baru** (minimal 6 karakter, disarankan lebih panjang & kombinasi huruf-angka).
3. Klik **Simpan Perubahan**.

Lakukan ini **segera setelah instalasi pertama** dan setiap kali Anda curiga
password bocor.

---

## 10. Backup & Restore Database

Menu sidebar: **Backup & Restore**.

### Membuat backup (lakukan rutin, disarankan setiap hari kerja)

1. Klik **Buat Backup Sekarang**.
2. File `.sql` akan muncul di daftar **Backup Tersimpan**.
3. Klik **Unduh** untuk menyimpan salinannya ke komputer/Google Drive/email
   pribadi Anda sebagai cadangan di luar server (praktik terbaik: jangan
   hanya mengandalkan file di server).

### Memulihkan (restore) — HANYA saat darurat (data rusak/salah input massal)

1. Pastikan Anda benar-benar butuh mengembalikan ke kondisi sebelumnya.
2. **Buat backup baru dari kondisi saat ini terlebih dahulu** (jaga-jaga).
3. Pilih file backup yang sesuai tanggal kejadian → klik **Restore**, atau
   unggah file `.sql` dari komputer lewat **Restore dari File Unggahan**.
4. Konfirmasi peringatan yang muncul (proses ini **menimpa seluruh data saat ini**).

> Tidak perlu SSH/`mysqldump`/cron — seluruhnya berjalan otomatis lewat PHP,
> cocok untuk shared hosting apa pun.

---

## 11. Pengaturan Sistem

Menu sidebar: **Pengaturan**.

Bisa diatur di sini: nama aplikasi, simbol mata uang, zona waktu, **lama
timeout sesi login** (default 30 menit — perpanjang jika staf sering dianggap
"logout sendiri"), **suku bunga default** gadai & pinjaman, **biaya admin
default** untuk transaksi transfer/setor/tarik tunai (memudahkan pengisian
form transaksi baru — kasir tetap bisa menyesuaikan per transaksi), dan
catatan kaki struk cetak.

---

## 12. Checklist Rutin Super Admin

| Frekuensi | Tugas |
|---|---|
| Harian | Cek Dashboard (omzet, laba, stok menipis, gadai/pinjaman jatuh tempo) |
| Harian | Buat & unduh Backup database |
| Mingguan | Review Log Audit (transaksi void, login mencurigakan) |
| Mingguan | Review Laporan Laba Rugi per cabang |
| Mingguan | Cek Laporan Saldo Kas Harian — pastikan setiap cabang rutin mencatat |
| Bulanan | Review daftar Pengguna aktif (nonaktifkan yang sudah resign) |
| Saat ada cabang/karyawan baru | Tambah data Cabang, Pengguna, dan Sumber Dana Saldo Kas terkait |
| Saat curiga password bocor | Ganti password sendiri & minta staf ganti juga |

---

## 13. Troubleshooting Cepat

| Kendala | Solusi |
|---|---|
| Admin Cabang/Kasir tidak bisa login | Cek menu Pengguna: apakah akun **Aktif**? Apakah akun sedang **terkunci** (5x salah password, tunggu 15 menit)? |
| Kasir tidak bisa membuka menu tertentu | Itu memang dibatasi sesuai role — cek menu **Role & Hak Akses** jika perlu penyesuaian |
| Laporan laba tidak muncul untuk Admin Cabang | Pastikan izin `reports.profit` masih dicentang di **Role & Hak Akses → Admin Cabang** |
| Halaman error / tidak bisa diakses | Hubungi penyedia hosting untuk cek status server, atau lihat `storage/logs/php_error.log` |
| Perlu memulihkan data yang terhapus tidak sengaja | Gunakan fitur **Restore** dari backup terakhir sebelum kejadian |
| Cabang baru belum punya menu Saldo Kas Harian yang bisa diisi | Tambahkan sumber dananya dulu lewat **Saldo Kas Harian → Kelola Sumber Dana** |

---

Lihat juga: [`JUKNIS_ADMIN_CABANG.md`](JUKNIS_ADMIN_CABANG.md) dan
[`JUKNIS_KASIR.md`](JUKNIS_KASIR.md) untuk diberikan ke tim operasional Anda,
serta [`USER_GUIDE.md`](USER_GUIDE.md) untuk ringkasan seluruh modul.
