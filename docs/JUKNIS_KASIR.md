# Petunjuk Teknis (Juknis) — KASIR

**Aplikasi**: POS Multi Usaha
**Untuk**: Staf kasir yang melayani transaksi harian di satu cabang
**Ruang lingkup akses**: Hanya **membuat transaksi** dan **mencetak struk**
untuk Penjualan Aksesoris, Pulsa/Data/Top Up Saldo, Transfer/Setor Bank,
Servis HP, Gadai Barang, dan Pinjam Uang — ditambah **mencatat saldo akhir
kas harian**.

> **Penting**: Sebagai Kasir, Anda **tidak bisa**:
> - Mengubah harga jual saat transaksi.
> - Membatalkan (void) transaksi yang sudah tersimpan.
> - Melihat laporan laba/untung usaha.
> - Mengubah data produk, harga, atau menghapus data apa pun.
> - Menambah sumber dana kas baru atau mengubah **saldo awal** kas (hanya
>   bisa mengisi/mengedit angka **saldo akhir**).
>
> Ini bukan kesalahan sistem — memang dirancang demikian untuk melindungi
> Anda dan usaha dari kesalahan/kecurigaan yang tidak perlu. Jika ada
> kesalahan transaksi, segera laporkan ke **Admin Cabang** Anda.

---

## 1. Login

1. Buka alamat website aplikasi (mis. `https://tokoanda.com/`) di browser
   komputer/HP/tablet kasir.
2. Masukkan **username** dan **password** yang diberikan Admin Cabang/Super Admin.
3. Klik **Masuk**. Anda akan masuk ke halaman **Dashboard**.
4. Jika ini kali pertama Anda login, sebaiknya ganti password: klik nama
   Anda (pojok kanan atas) → **Profil Saya** → isi password lama & baru →
   **Simpan Perubahan**.

**Jika salah password 5 kali berturut-turut**, akun akan terkunci otomatis
selama 15 menit — tunggu, jangan panik, lalu coba lagi atau hubungi Admin Cabang.

**Jika layar tiba-tiba kembali ke halaman login saat sedang bekerja**, itu
karena sesi otomatis berakhir setelah ±30 menit tanpa aktivitas. Login ulang
dan lanjutkan transaksi.

---

## 2. Mengenal Tampilan Utama

- **Sidebar kiri**: menu navigasi. Anda hanya akan melihat menu yang memang
  diizinkan untuk Kasir (Dashboard, Kasir Aksesoris, Riwayat Penjualan,
  Pulsa/Data/Top Up, Transfer/Setor Bank, Servis HP, Gadai Barang, Pinjam
  Uang, Saldo Kas Harian, Produk, Pelanggan, Laporan — sesuai pengaturan
  Admin Cabang Anda).
- **Pojok kanan atas**: nama Anda, role (Kasir), dan cabang tempat Anda bertugas.
- Di layar HP/tablet sempit, sidebar tersembunyi — ketuk ikon **☰** di pojok
  kiri atas untuk membukanya.

---

## 3. Transaksi Penjualan Aksesoris (Kasir Aksesoris)

Ini adalah layar yang paling sering Anda pakai sehari-hari.

1. Klik menu **Kasir Aksesoris**.
2. Di kotak pencarian ("Cari nama produk / SKU / scan barcode..."), lakukan salah satu:
   - Ketik nama produk (mis. "casing"), atau
   - Ketik kode SKU produk, atau
   - **Scan barcode** dengan alat scanner (kursor otomatis fokus ke kotak ini) lalu tekan Enter.
3. Klik kartu produk yang muncul untuk menambahkannya ke **Keranjang** di sebelah kanan.
4. Atur jumlah (qty) barang dengan tombol **+** dan **-** di keranjang. Jika
   qty melebihi stok tersedia, sistem akan menolak dan memberi tahu Anda.
5. Ulangi langkah 2–4 untuk semua barang yang dibeli pelanggan.
6. (Opsional) Pilih **Pelanggan** dari daftar jika pelanggan sudah terdaftar
   — atau biarkan "Pelanggan Umum" jika tidak perlu dicatat.
7. Pilih **Metode Bayar**: Tunai, Transfer, QRIS, atau Debit/Kredit.
8. Isi **Jumlah Dibayar** — kolom **Kembalian** otomatis terhitung.
9. Periksa sekali lagi **Total Bayar** di layar sudah sesuai.
10. Klik **Proses & Cetak Struk**. Struk akan otomatis terbuka untuk dicetak —
    klik **Cetak Ulang** bila perlu mencetak lagi, atau **Tutup** untuk kembali.

> **Catatan**: Anda tidak bisa mengubah kolom harga di keranjang — harga
> selalu mengikuti harga jual resmi produk yang sudah ditetapkan Admin
> Cabang/Super Admin. Bila pelanggan minta diskon, gunakan kolom **Diskon
> (Rp)** yang tersedia, bukan mengubah harga satuan.

### Jika salah input transaksi

Kasir **tidak bisa membatalkan sendiri** transaksi yang sudah diproses.
Segera hubungi **Admin Cabang** untuk membatalkan (void) transaksi tsb lewat
menu Riwayat Penjualan mereka, sertakan No. Invoice dan alasan kesalahannya.

---

## 4. Transaksi Pulsa, Paket Data & Top Up Saldo E-Wallet

1. Klik menu **Pulsa, Data & Top Up**.
2. Pada form **Transaksi Baru** di bagian atas halaman:
   - Pilih **Produk** — daftar dikelompokkan per jenis: Pulsa, Paket Data,
     Token PLN, dan **Top Up E-Wallet** (Gopay, ShopeePay, OVO, DANA, dll).
   - Isi **No. Tujuan** (nomor HP atau nomor yang terhubung ke e-wallet
     pelanggan) dengan teliti — periksa ulang sebelum submit.
   - (Opsional) isi **Catatan**.
3. Klik **Proses**. Struk transaksi otomatis terbuka untuk dicetak/diberikan ke pelanggan.
4. Riwayat transaksi hari ini tampil di tabel bawah halaman yang sama.

> Pastikan nomor tujuan benar sebelum klik Proses — pulsa/top up yang sudah
> terkirim ke nomor salah tidak dapat ditarik kembali oleh sistem ini.

---

## 5. Transfer / Setor Tunai / Tarik Tunai (Agen Semua Bank)

1. Klik menu **Transfer / Setor Bank**.
2. Pilih **Jenis Transaksi**: Transfer ke Rekening Lain, Setor Tunai, atau Tarik Tunai.
3. Pilih **Bank** dari daftar. Untuk transfer, isi **No. Rekening Tujuan**
   dan **Nama Pemilik Rekening** — periksa ulang dengan teliti sebelum submit.
4. Isi **Nominal Uang** yang diserahkan/ditransfer pelanggan.
5. Kolom **Biaya Jasa/Admin** biasanya sudah terisi otomatis — biarkan sesuai
   ketentuan cabang kecuali diarahkan lain oleh Admin Cabang.
6. Periksa **Total Diterima dari Pelanggan** (nominal + biaya jasa) sebelum
   klik **Proses & Cetak Struk**.

> Nomor rekening dan nama pemilik yang salah dapat menyebabkan uang terkirim
> ke orang yang salah dan **tidak dapat ditarik kembali**. Selalu konfirmasi
> ulang ke pelanggan sebelum memproses.

### Jika salah input transaksi

Sama seperti modul lain, Kasir tidak bisa membatalkan sendiri. Segera
laporkan ke Admin Cabang dengan menyebutkan No. Transaksi.

---

## 6. Menerima Servis HP

1. Klik menu **Servis HP** → **+ Terima Servis Baru**.
2. Pilih **Pelanggan** dari daftar, atau tambah pelanggan baru dari tautan
   yang tersedia (isi Nama dan Telepon minimal).
3. Isi **Merk/Tipe HP**, **Keluhan Pelanggan** (jelas dan detail, mis. "Layar
   retak pojok kanan bawah, masih menyala"), dan **Kelengkapan yang
   Dititipkan** (charger, sim card, dus, dll) — ini penting untuk menghindari
   klaim kehilangan barang titipan nantinya.
4. Isi **Perkiraan Biaya** (tanyakan ke teknisi bila belum yakin) dan
   **Uang Muka (DP)** jika pelanggan membayar sebagian di awal.
5. Klik **Simpan Servis**, lalu buka halaman detail dan klik **Cetak Bukti
   Servis** — berikan struk ini ke pelanggan, karena berisi **No. Servis**
   yang menjadi bukti wajib saat pengambilan unit nanti.

**Mengubah status pengerjaan atau menyerahkan unit ke pelanggan** biasanya
dilakukan oleh **Admin Cabang** — jika Anda diberi wewenang ini, ikuti
panduan yang sama seperti di [`JUKNIS_ADMIN_CABANG.md`](JUKNIS_ADMIN_CABANG.md) §9.

---

## 7. Membuat Gadai Baru

1. Klik menu **Gadai Barang** → **+ Gadai Baru**.
2. Pilih **Pelanggan** dari daftar. Jika pelanggan belum terdaftar, klik
   tautan **"Tambah pelanggan baru"** (akan membuka tab baru), isi Nama,
   Telepon, dan **No. KTP** (wajib untuk gadai), simpan, lalu kembali ke tab
   form gadai dan pilih pelanggan tsb.
3. Isi **Nama Barang**, **Deskripsi Barang** (kondisi, kelengkapan, dll),
   **Taksiran Nilai Barang**, dan **Jumlah Pinjaman** yang disepakati.
4. **Bunga (% per bulan)** biasanya sudah terisi otomatis sesuai standar
   usaha — konfirmasi ke Admin Cabang jika perlu diubah.
5. Isi **Tanggal Gadai** (biasanya hari ini) dan **Jatuh Tempo**.
6. Klik **Simpan Gadai** — Anda akan diarahkan ke halaman detail gadai.
7. Klik **Cetak Surat Gadai** untuk mencetak bukti gadai untuk pelanggan.

**Menerima pembayaran bunga, perpanjangan, atau pelunasan (tebus)**
dari gadai yang sudah ada biasanya dilakukan oleh **Admin Cabang** — jika
Anda diberi wewenang ini oleh Admin Cabang, ikuti panduan yang sama seperti
di [`JUKNIS_ADMIN_CABANG.md`](JUKNIS_ADMIN_CABANG.md) §10.

---

## 8. Membuat Pinjaman Baru

1. Klik menu **Pinjam Uang** → **+ Pinjaman Baru**.
2. Pilih **Pelanggan** (atau tambah pelanggan baru seperti langkah gadai di atas).
3. Isi **Jumlah Pinjaman**, **Bunga (% per bulan)**, **Tenor (jumlah bulan)**,
   dan **Tanggal Pinjam**.
4. Klik **Simpan Pinjaman** — sistem otomatis membuat jadwal angsuran bulanan.
5. Klik **Cetak** pada halaman detail untuk mencetak surat perjanjian pinjaman.

**Menerima pembayaran angsuran** dari pinjaman yang sudah ada biasanya
dilakukan oleh Admin Cabang — jika Anda diberi wewenang ini, ikuti panduan
[`JUKNIS_ADMIN_CABANG.md`](JUKNIS_ADMIN_CABANG.md) §11.

---

## 9. Mencatat Saldo Kas Harian (Tutup Kas)

Ini adalah tugas rutin di **akhir shift/hari** untuk memastikan uang yang ada
sesuai dengan catatan sistem.

1. Klik menu **Saldo Kas Harian**.
2. Anda akan melihat daftar sumber dana cabang Anda, biasanya: **Kas Tunai**,
   **Saldo Bank**, dan **Gopay Merchant** (atau sumber lain yang sudah
   ditambahkan Super Admin).
3. Untuk masing-masing sumber dana, hitung/cek jumlah aktualnya:
   - **Kas Tunai** — hitung fisik uang di laci kasir.
   - **Saldo Bank** — cek mutasi/saldo terakhir di rekening (lewat m-banking/internet banking).
   - **Gopay Merchant** (atau e-wallet lain) — cek saldo di aplikasi merchant.
4. Isi angka tersebut pada kolom **Saldo Akhir** di baris masing-masing sumber dana.
5. Bila ada selisih dengan yang seharusnya, tulis penjelasan singkat di kolom **Catatan**.
6. Klik **Simpan Saldo Kas**.

> **Catatan penting**: Anda hanya bisa mengisi/mengedit kolom **Saldo Akhir**.
> Kolom **Saldo Awal** hanya ditampilkan sebagai referensi (readonly) dan
> hanya bisa diubah oleh Super Admin. Anda juga tidak bisa menambah sumber
> dana baru — bila ada rekening/e-wallet baru yang perlu dicatat, minta
> Admin Cabang/Super Admin menambahkannya lewat **Kelola Sumber Dana**.

Anda bisa membuka kembali menu ini kapan saja pada hari yang sama untuk
memperbaiki angka bila ternyata ada koreksi — data yang tersimpan terakhir
yang akan dipakai.

---

## 10. Melihat Riwayat Transaksi

- Menu **Riwayat Penjualan** menampilkan seluruh transaksi penjualan aksesoris
  cabang Anda — berguna untuk mencari ulang transaksi lama untuk dicetak lagi
  strukturnya (klik **Cetak** di baris terkait) atau sekadar mengecek status.
- Menu **Transfer / Setor Bank** dan **Pulsa, Data & Top Up** juga memiliki
  daftar riwayat transaksi hari ini/terkini di bagian bawah halaman masing-masing.
- Anda **bisa melihat** detail transaksi, tapi tombol **Batalkan Transaksi**
  hanya akan muncul untuk pengguna dengan wewenang tsb (Admin Cabang/Super Admin).

---

## 11. Hal yang TIDAK Bisa Dilakukan Kasir (dan Kenapa)

| Yang tidak bisa dilakukan | Alasan |
|---|---|
| Mengubah harga jual saat transaksi | Mencegah kasir memberi harga tidak resmi tanpa sepengetahuan pemilik usaha |
| Membatalkan (void) transaksi (penjualan/bank) | Mencegah kasir "menghapus jejak" transaksi bermasalah tanpa persetujuan atasan |
| Melihat Laporan Laba Rugi | Informasi laba/margin usaha bersifat rahasia, hanya untuk pemilik & Admin Cabang |
| Menghapus/mengubah data produk, supplier, kategori, daftar bank | Menjaga integritas data master yang dipakai seluruh cabang |
| Menambah sumber dana kas baru atau mengubah **saldo awal** | Menjaga akurasi baseline saldo kas — hanya Super Admin (izin `cash.manage`) |
| Mengubah status servis atau menyerahkan unit tanpa izin `service.manage` | Memastikan hanya staf berwenang yang menutup transaksi servis |
| Membuat/menghapus akun pengguna lain | Wewenang khusus Super Admin |

Jika Anda merasa perlu melakukan salah satu di atas untuk pekerjaan Anda,
sampaikan ke Admin Cabang — mereka bisa melakukannya atau meminta Super Admin
menyesuaikan hak akses Anda.

---

## 12. Checklist Awal & Akhir Shift

**Awal shift:**
- [ ] Login dan pastikan nama & cabang yang tampil di pojok kanan atas sudah benar.
- [ ] Cek Dashboard sekilas — ada peringatan stok menipis atau servis yang siap diambil?

**Akhir shift:**
- [ ] Pastikan semua transaksi hari ini sudah diproses (cek **Riwayat Penjualan**,
      **Transfer/Setor Bank**, dan **Pulsa/Top Up**).
- [ ] Hitung & catat **Saldo Kas Harian** — kas tunai, saldo bank, dan saldo e-wallet merchant.
- [ ] Informasikan ke Admin Cabang bila ada transaksi yang salah input dan perlu dibatalkan.
- [ ] Logout jika perangkat kasir dipakai bergantian dengan kasir shift berikutnya (klik nama Anda → **Keluar**).

---

## 13. Troubleshooting Sederhana

| Kendala | Solusi |
|---|---|
| Tidak bisa login | Periksa username/password, atau tunggu 15 menit jika akun terkunci. Jika masih gagal, hubungi Admin Cabang |
| Produk yang dicari tidak muncul | Pastikan ejaan benar, atau cek ke Admin Cabang apakah produk tsb sudah didaftarkan/masih aktif |
| Stok produk tertulis habis padahal ada fisiknya | Laporkan ke Admin Cabang untuk penyesuaian stok, jangan memaksakan transaksi |
| Bank tujuan tidak ada di daftar | Laporkan ke Admin Cabang/Super Admin untuk menambahkan lewat Kelola Daftar Bank |
| Sumber dana kas yang dicari tidak ada di menu Saldo Kas Harian | Laporkan ke Admin Cabang/Super Admin untuk ditambahkan lewat Kelola Sumber Dana |
| Salah input jumlah bayar/qty/nominal sebelum klik Proses | Perbaiki dulu di layar sebelum diproses — setelah diproses harus dibatalkan oleh Admin Cabang |
| Struk tidak tercetak di printer | Klik **Cetak Ulang** di halaman struk; jika printer bermasalah, cek koneksi printer/kertas |
| Halaman keluar sendiri ke login | Sesi otomatis habis (±30 menit tanpa aktivitas) — login ulang, transaksi yang sudah tersimpan tetap aman |

---

Untuk kewenangan tambahan (ubah harga, void transaksi, laporan laba, kelola
status servis), lihat [`JUKNIS_ADMIN_CABANG.md`](JUKNIS_ADMIN_CABANG.md).
Untuk pengaturan akun, cabang, dan sistem, lihat [`JUKNIS_SUPER_ADMIN.md`](JUKNIS_SUPER_ADMIN.md).
