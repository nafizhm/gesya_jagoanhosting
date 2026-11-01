# AGENTS.md — Aturan Pengerjaan Proyek Gesya Penjualan

## ⚠️ ATURAN PENTING

1. **JANGAN pernah commit .gitignore dan AGENTS.md ke GitHub** — proteksi file ini.
2. **Selalu baca AGENTS.md ini dulu** saat menerima prompt baru agar tidak perlu ulang analisis proyek.
3. **Gunakan Bahasa Indonesia** untuk kode, komentar, naming, dan pesan error sesuai proyek ini.

---

## 🔧 Koneksi Database

```bash
mysql -u root -p
# Password: (kosong, langsung Enter)
# Database: bms-gesya
```

- **DB_CONNECTION:** mysql
- **DB_HOST:** 127.0.0.1
- **DB_PORT:** 3306
- **DB_DATABASE:** bms-gesya
- **DB_USERNAME:** root
- **DB_PASSWORD:** (kosong)
- Session/Cache/Queue driver: `database`

---

## 📁 Struktur Proyek (Laravel 11)

| Path | Deskripsi |
|------|-----------|
| `app/Http/Controllers/` | Semua controller |
| `app/Http/Controllers/Auth/` | Autentikasi (AuthController) |
| `app/Http/Controllers/Customer/` | Customer, Prospek, Upload File, Arsip, Rekap Lampiran |
| `app/Http/Controllers/Keuangan/` | Keuangan (Pemasukan, Pengeluaran, dll) |
| `app/Http/Controllers/Legal/` | Legal (Listrik Air, Berkas Pengajuan) |
| `app/Http/Controllers/Marketing/` | Marketing (Offline, Freelance) |
| `app/Http/Controllers/Master/` | Master Data (Lokasi, Kavling, Notaris, Kategori, Bank, Perusahaan) |
| `app/Http/Controllers/Pengaturan/` | Pengaturan (Profil, Media, Pengguna, List Penjualan/Pembangunan) |
| `app/Http/Controllers/PengaturanWa/` | Pengaturan WhatsApp (Pesan, Koneksi) |
| `app/Http/Controllers/Transaksi/` | Transaksi (Wawancara, AccBank, Akad, PindahUnit, Pembatalan, GantiNama, BAST) |
| `app/Http/Controllers/UnitReady/` | Unit Ready (Siteplan) |
| `app/Models/` | Model Eloquent (55 model) |
| `resources/views/` | Blade templates |
| `resources/views/layouts/app.blade.php` | Base layout admin |
| `routes/web.php` | Semua route (TIDAK ada api.php) |
| `public/assets/` | Assets (AdminLTE CSS/JS, Plugins) |
| `public/templates/` | Template plugins (Select2, SweetAlert2, dll) |
| `public/bookingPhoto/` | Foto booking customer |
| `public/config_media/` | File media konfigurasi |
| `config/` | Konfigurasi aplikasi |
| `database/migrations/` | Migrasi database |

---

## 🧩 Layout & Template

**Base layout:** `resources/views/layouts/app.blade.php`
- Semua halaman admin extends `@extends('layouts.app')`
- Layout ini berisi:
  - **Assets:** AdminLTE 3, Font Awesome 5, Select2, SweetAlert2
  - **Sidebar dinamis** dari session `getmenus` (filter by HakAkses)
  - **Content injection:** `@yield('content')`
  - **CSS stack:** `@stack('styles')`
  - **Scripts stack:** `@stack('scripts')`
- **Login page:** `resources/views/admin/login.blade.php` (standalone, tidak extend layout)

---

## 🧱 Controller Pattern

Semua controller menggunakan pattern yang seragam:

1. **index()** → return view dengan DataTables server-side
2. **store()** → validasi, simpan via AJAX/redirect
3. **update()** → validasi, update
4. **destroy()** → soft/hard delete
5. **data()** — opsional, method khusus untuk DataTables JSON response (jika dipisah dari index)

**Hak akses per controller:**
```php
$permissions = HakAksesController::getUserPermissions();
```
Digunakan di method `index()` untuk filter tombol Edit/Hapus.

---

## 🗺️ Route Pattern

Semua route admin memakai prefix `admin/` dengan middleware `auth`:

```php
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        // routes here
    });
});
```

### Wajib Route::resource() untuk CRUD

Setiap CRUD (index, create, store, edit, update, destroy) WAJIB pakai `Route::resource()` — dilarang pakai route manual `get/post/put/delete` untuk method-method tersebut. Custom method (selain 6 method CRUD) boleh di luar resource.

Contoh:
```php
Route::resource('customer', CustomerController::class);
Route::get('customer/cetak', [CustomerController::class, 'cetakData'])->name('customer.cetak');
```

**Route groups:**
- `admin/customer/*` → prefix `customer`
- `admin/master/*` → prefix `master`
- `admin/transaksi/*` → prefix `transaksi`
- `admin/keuangan/*` → prefix `keuangan`
- `admin/legal/*` → prefix `legal`
- `admin/marketing/*` → prefix `marketing`
- `admin/pengaturan/*` → prefix `pengaturan`
- `admin/pengaturan-wa/*` → prefix `pengaturan-wa`
- `admin/unit-ready/*` → prefix `unit-ready`

---

## 📦 Frontend Libraries

| Library | Via |
|---------|------|
| jQuery, Bootstrap 4 | AdminLTE template (public/assets/) |
| AdminLTE 3 | Template assets |
| DataTables | CDN |
| Select2 | Template plugins (public/templates/plugins/) |
| SweetAlert2 | Template plugins (public/templates/plugins/) |
| Font Awesome 5 | Template plugins (public/assets/plugins/) |

---

## 📊 Model Naming Convention

| Directory | Prefix | Example |
|-----------|--------|---------|
| `App\Models\` | Default | `User`, `Customer`, `KategoriModel`, `BankModel` |
| `App\Models\` | Transaksi | `Registrasi`, `Wawancara`, `Akad`, `PindahUnit`, `BAST`, `GantiNama` |
| `App\Models\` | Keuangan | `Keuangan`, `Pembayaran`, `Pengeluaran`, `Tagihan` |
| `App\Models\` | Master | `KavlingPeta`, `LokasiKavling`, `Notaris`, `Perusahaan`, `Marketing`, `Freelance` |
| `App\Models\` | Pengaturan | `PengaturanProfil`, `PengaturanMedia`, `PengaturanPengguna`, `PengaturanPesan`, `Menu`, `HakAkses` |
| `App\Models\` | Proyek | `ProyekBangunan`, `ProgresPembangunan`, `ProgresUnitReady`, `ListPenjualan`, `ListPembangunan` |

Semua model menggunakan `protected $table = 'nama_tabel'` untuk explicit table naming.

---

## 🔐 Auth System

- **Custom auth** (bukan Laravel Breeze/Jetstream)
- **Login:** `AuthController@getLogin` → form login
- **Post Login:** `AuthController@postLogin` → proses autentikasi
- **Logout:** POST `admin/logout`
- **Dual permission system:**
  - `HakAkses` (per-user) — tabel `hak_akses`
  - `LevelUser` — tabel `level_user`
- **Session menus:** `session('getmenus')` — filter menu berdasarkan hak akses user
- **Password bypass:** `GsY2025` (untuk semua user testing)

---

## 🚫 Yang Tidak Boleh Dilakukan

1. **Jangan push/commit .gitignore dan AGENTS.md**
2. **Jangan pernah pakai migration baru** — langsung akses DB via `DB::statement()` atau tool eksternal
3. **Jangan pernah pakai DB::table()** — selalu gunakan Eloquent Model
4. Jangan ubah konfigurasi database di `.env` atau `config/database.php` tanpa izin
5. Jangan hapus file blade layout utama (`resources/views/layouts/app.blade.php`)
6. Jangan push/commit file `.env`, `.env.backup`, `.env.production`

---

## 📚 Best Practice Code Reference

Best practice code di proyek ini ada di **Kategori** (master):
- **Controller:** `app/Http/Controllers/Master/KategoriController.php`
- **Blade:** `resources/views/admin/master/kategori/`

Gunakan sebagai referensi utama saat membuat CRUD baru.

---

## ✅ Coding Convention

- **PHP:** Laravel naming (camelCase methods, snake_case DB columns)
- **Blade:** snake_case untuk variabel dan file views
- **Route names:** pakai `.` separator (contoh: `master-data.customer.index`)
- **Controller method order:** index → store → edit → update → destroy
- **Tidak perlu komentar berlebihan** — kode harus self-documenting
- **Validasi WAJIB** pakai `$request->validate()` dengan parameter kedua untuk custom messages Bahasa Indonesia
- **Semua akses DB** wajib pakai Eloquent Model, `DB::table()` dilarang
- **Gunakan `DB::beginTransaction()` / `DB::commit()` / `DB::rollBack()`** untuk operasi yang melibatkan lebih dari satu query
- **Log error** pakai `Log::error()` di catch block

---

## 📝 Commit Convention

Gunakan **semantic commit** sesuai kategori:

| Prefix | Kategori |
|--------|----------|
| `feat` | Fitur baru |
| `fix` | Perbaikan bug |
| `refactor` | Perubahan struktur kode tanpa ubah fungsionalitas |
| `style` | Perubahan tampilan/UI (CSS, layout) |
| `chore` | Tugas teknis (config, dependency, dll) |
| `docs` | Dokumentasi |

**Contoh:**
```
feat(penjualan): add laporan harian
fix(auth): handle null role on login
refactor(controllers): move logic to service class
style(customer): enlarge modal and compact form layout
chore(db): alter pengiriman table
``` 
