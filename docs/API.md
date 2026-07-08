# Dokumentasi API Internal

Aplikasi ini bukan REST API publik — seluruh endpoint di bawah ini adalah
**API internal** yang hanya dipakai oleh JavaScript sisi klien pada halaman
yang sama (mis. pencarian produk di layar kasir), dan **selalu memerlukan
sesi login yang valid** (cookie sesi PHP). Semua respons berformat JSON.

Base URL mengikuti instalasi Anda, mis. `https://tokoanda.com/`.
Semua contoh di bawah relatif terhadap base URL tersebut.

---

## Autentikasi & Otorisasi

- Endpoint API **tidak** memakai token/API key terpisah — otorisasi memakai
  sesi cookie yang sama dengan aplikasi web (`POS_SESSID`).
- Setiap endpoint memeriksa hak akses (permission) sebelum mengembalikan data.
- Permintaan tanpa sesi valid akan diarahkan ke halaman login (untuk request
  biasa) atau mengembalikan data kosong dengan status HTTP 403 (untuk fetch JSON).

---

## `GET /api/product_search`

Mencari produk aktif pada cabang milik pengguna yang sedang login. Dipakai
oleh layar Kasir Aksesoris (`sales/pos`) untuk menampilkan grid produk yang
bisa diklik ke keranjang.

**Permission**: `sales.create` atau `purchases.manage`

**Query Parameter**

| Nama | Wajib | Keterangan |
|---|---|---|
| `q` | Tidak | Kata kunci pencarian (nama produk, SKU, atau barcode persis). Kosongkan untuk menampilkan produk terbaru. |

**Contoh Request**

```
GET /api/product_search?q=casing
```

**Contoh Respons (200)**

```json
{
  "data": [
    {
      "id": 1,
      "branch_id": 1,
      "category_id": 1,
      "sku": "CS-001",
      "barcode": "8990000000001",
      "name": "Casing Silikon Universal",
      "unit": "pcs",
      "purchase_price": "8000.00",
      "sale_price": "15000.00",
      "stock_qty": 50,
      "min_stock": 10,
      "is_active": 1
    }
  ]
}
```

Jika pengguna tidak memiliki cabang aktif (mis. Super Admin) atau tidak
memiliki izin, respons berupa `{"data": []}`.

---

## `GET /api/customer_search`

Mencari pelanggan berdasarkan nama atau nomor telepon, dibatasi ke cabang
pengguna (atau pelanggan lintas cabang yang `branch_id`-nya `NULL`). Dipakai
pada formulir gadai/pinjaman/penjualan untuk pencarian pelanggan cepat.

**Permission**: harus login (tanpa permission spesifik tambahan)

**Query Parameter**

| Nama | Wajib | Keterangan |
|---|---|---|
| `q` | Ya | Minimal 2 karakter. Kurang dari itu akan mengembalikan array kosong. |

**Contoh Respons (200)**

```json
{
  "data": [
    { "id": 3, "name": "Budi Santoso", "phone": "081234567890", "address": "..." }
  ]
}
```

---

## Konvensi Umum

- Semua endpoint hanya mendukung method **GET** untuk pembacaan data (read-only,
  tidak mengubah data), sehingga aman dari risiko CSRF pada endpoint tersebut.
- Semua endpoint melakukan **pembatasan cabang otomatis** (branch scoping) di
  layer model — pengguna non-Super Admin tidak dapat melihat data cabang lain
  meskipun mencoba memanipulasi parameter request.
- Aksi yang mengubah data (membuat transaksi penjualan, pulsa, gadai, pinjaman,
  dsb) **tidak** melalui endpoint `/api/*` melainkan melalui form POST biasa ke
  controller terkait (mis. `POST /sales/store`) yang dilindungi CSRF token —
  ini disengaja agar submit transaksi tetap bekerja dengan aman meski tanpa
  JavaScript, dan konsisten dengan seluruh alur form lain di aplikasi.

## Menambah Endpoint API Baru

Untuk menambah endpoint internal baru:

1. Tambahkan method publik baru pada `app/controllers/ApiController.php`
   (nama method = nama aksi pada URL, hanya huruf/angka/underscore).
2. Panggil `$this->requireLogin()` dan/atau `$this->requirePermission(...)`
   di awal method.
3. Ambil parameter lewat `$this->get('nama')` (otomatis dibersihkan dari tag HTML).
4. Query data melalui Model terkait (jangan menulis SQL mentah langsung di
   controller) dan kembalikan dengan `$this->json(['data' => ...])`.
