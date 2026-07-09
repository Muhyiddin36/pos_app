# Petunjuk Teknis (Juknis) — KASIR

**Aplikasi**: POS Multi Usaha
**Untuk**: Staf kasir yang melayani transaksi harian di satu cabang
**Ruang lingkup akses**: Hanya **membuat transaksi** dan **mencetak struk**
untuk Penjualan Aksesoris, Pulsa & Paket Data, Gadai Barang, dan Pinjam Uang.

> **Penting**: Sebagai Kasir, Anda **tidak bisa**:
> - Mengubah harga jual saat transaksi.
> - Membatalkan (void) transaksi yang sudah tersimpan.
> - Melihat laporan laba/untung usaha.
> - Mengubah data produk, harga, atau menghapus data apa pun.
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
  Pulsa & Paket Data, Gadai Barang, Pinjam Uang, Produk, Pelanggan, Laporan
  — sesuai pengaturan Admin Cabang Anda).
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

## 4. Transaksi Pulsa & Paket Data

1. Klik menu **Pulsa & Paket Data**.
2. Pada form **Transaksi Baru** di bagian atas halaman:
   - Pilih **Produk** (nominal pulsa/paket data yang diinginkan pelanggan).
   - Isi **No. Tujuan** (nomor HP pelanggan) dengan teliti — periksa ulang sebelum submit.
   - (Opsional) isi **Catatan**.
3. Klik **Proses**. Struk transaksi otomatis terbuka untuk dicetak/diberikan ke pelanggan.
4. Riwayat transaksi hari ini tampil di tabel bawah halaman yang sama.

> Pastikan nomor tujuan benar sebelum klik Proses — pulsa yang sudah terkirim
> ke nomor salah tidak dapat ditarik kembali oleh sistem ini.

---

## 5. Membuat Gadai Baru

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
di [`JUKNIS_ADMIN_CABANG.md`](JUKNIS_ADMIN_CABANG.md) §8.

---

## 6. Membuat Pinjaman Baru

1. Klik menu **Pinjam Uang** → **+ Pinjaman Baru**.
2. Pilih **Pelanggan** (atau tambah pelanggan baru seperti langkah gadai di atas).
3. Isi **Jumlah Pinjaman**, **Bunga (% per bulan)**, **Tenor (jumlah bulan)**,
   dan **Tanggal Pinjam**.
4. Klik **Simpan Pinjaman** — sistem otomatis membuat jadwal angsuran bulanan.
5. Klik **Cetak** pada halaman detail untuk mencetak surat perjanjian pinjaman.

**Menerima pembayaran angsuran** dari pinjaman yang sudah ada biasanya
dilakukan oleh Admin Cabang — jika Anda diberi wewenang ini, ikuti panduan
[`JUKNIS_ADMIN_CABANG.md`](JUKNIS_ADMIN_CABANG.md) §9.

---

## 7. Melihat Riwayat Transaksi

- Menu **Riwayat Penjualan** menampilkan seluruh transaksi penjualan aksesoris
  cabang Anda — berguna untuk mencari ulang transaksi lama untuk dicetak lagi
  strukturnya (klik **Cetak** di baris terkait) atau sekadar mengecek status.
- Anda **bisa melihat** detail transaksi, tapi tombol **Batalkan Transaksi**
  hanya akan muncul untuk pengguna dengan wewenang tsb (Admin Cabang/Super Admin).

---

## 8. Hal yang TIDAK Bisa Dilakukan Kasir (dan Kenapa)

| Yang tidak bisa dilakukan | Alasan |
|---|---|
| Mengubah harga jual saat transaksi | Mencegah kasir memberi harga tidak resmi tanpa sepengetahuan pemilik usaha |
| Membatalkan (void) transaksi | Mencegah kasir "menghapus jejak" transaksi bermasalah tanpa persetujuan atasan |
| Melihat Laporan Laba Rugi | Informasi laba/margin usaha bersifat rahasia, hanya untuk pemilik & Admin Cabang |
| Menghapus/mengubah data produk, supplier, kategori | Menjaga integritas data master yang dipakai seluruh cabang |
| Membuat/menghapus akun pengguna lain | Wewenang khusus Super Admin |

Jika Anda merasa perlu melakukan salah satu di atas untuk pekerjaan Anda,
sampaikan ke Admin Cabang — mereka bisa melakukannya atau meminta Super Admin
menyesuaikan hak akses Anda.

---

## 9. Checklist Awal & Akhir Shift

**Awal shift:**
- [ ] Login dan pastikan nama & cabang yang tampil di pojok kanan atas sudah benar.
- [ ] Cek Dashboard sekilas — ada peringatan stok menipis yang perlu diketahui?

**Akhir shift:**
- [ ] Pastikan semua transaksi hari ini sudah diproses (cek **Riwayat Penjualan**).
- [ ] Informasikan ke Admin Cabang bila ada transaksi yang salah input dan perlu dibatalkan.
- [ ] Logout jika perangkat kasir dipakai bergantian dengan kasir shift berikutnya (klik nama Anda → **Keluar**).

---

## 10. Troubleshooting Sederhana

| Kendala | Solusi |
|---|---|
| Tidak bisa login | Periksa username/password, atau tunggu 15 menit jika akun terkunci. Jika masih gagal, hubungi Admin Cabang |
| Produk yang dicari tidak muncul | Pastikan ejaan benar, atau cek ke Admin Cabang apakah produk tsb sudah didaftarkan/masih aktif |
| Stok produk tertulis habis padahal ada fisiknya | Laporkan ke Admin Cabang untuk penyesuaian stok, jangan memaksakan transaksi |
| Salah input jumlah bayar/qty sebelum klik Proses | Perbaiki dulu di layar sebelum klik **Proses & Cetak Struk** — setelah diproses harus dibatalkan oleh Admin Cabang |
| Struk tidak tercetak di printer | Klik **Cetak Ulang** di halaman struk; jika printer bermasalah, cek koneksi printer/kertas |
| Halaman keluar sendiri ke login | Sesi otomatis habis (±30 menit tanpa aktivitas) — login ulang, transaksi yang sudah tersimpan tetap aman |

---

Untuk kewenangan tambahan (ubah harga, void transaksi, laporan laba), lihat
[`JUKNIS_ADMIN_CABANG.md`](JUKNIS_ADMIN_CABANG.md). Untuk pengaturan akun,
cabang, dan sistem, lihat [`JUKNIS_SUPER_ADMIN.md`](JUKNIS_SUPER_ADMIN.md).
