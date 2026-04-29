# Dokumentasi Backend SIFORA (Surat BBPBL)

Dokumen ini menjelaskan implementasi backend proyek secara detail berdasarkan kode yang ada di repository saat ini.

## 1. Gambaran Umum Backend

- Framework utama: `Laravel 12` (`laravel/framework:^12.0`).
- Runtime PHP: `^8.2` (termasuk konfigurasi deployment yang menyiapkan `php84`).
- Arsitektur: monolith web (Blade + controller), bukan backend API murni.
- Pola data domain surat: data surat masuk dan data balasan surat keluar disimpan dalam tabel utama `surat_masuk` (denormalized), bukan di tabel terpisah aktif.
- Database default aplikasi: dari konfigurasi bawaan `sqlite`, tetapi environment produksi diarahkan ke `mysql`.

Dependensi backend kunci (`composer.json`):
- `barryvdh/laravel-dompdf`: export laporan PDF.
- `maatwebsite/excel`: export laporan Excel.
- `league/flysystem-aws-s3-v3`: dukungan storage berbasis S3/R2.
- `laravel/tinker`: inspeksi runtime.

## 2. Struktur Folder Backend

Komponen backend inti berada pada:
- `routes/web.php`: routing utama web.
- `routes/auth.php`: routing autentikasi (Breeze + kustom admin login).
- `routes/console.php`: artisan command, termasuk migrasi file ke R2.
- `app/Http/Controllers`: logika endpoint.
- `app/Http/Middleware`: middleware role/otorisasi modifikasi.
- `app/Models`: model Eloquent.
- `app/Exports`: class export laporan Excel.
- `database/migrations`: skema database.
- `database/seeders`: data awal user.
- `config/filesystems.php`: konfigurasi disk upload surat.
- `bootstrap/app.php`: registrasi middleware alias dan global exception reporter.

## 3. Lifecycle Request Backend

Alur request backend berbasis web:
1. Client mengakses endpoint `web` route.
2. Middleware standar web berjalan (`session`, `csrf`, `bindings`, dst).
3. Untuk route privat, middleware `auth` + `verified` wajib lulus.
4. Untuk operasi ubah data surat, middleware `can.modify` mengecek hak modifikasi user.
5. Controller memvalidasi request.
6. Data disimpan via Eloquent/Query Builder, termasuk upload file via `Storage`.
7. Response mayoritas berupa redirect + flash message ke view Blade.

Catatan:
- Tidak ada `routes/api.php` aktif.
- Respon backend dominan HTML redirect/view, bukan JSON API.

## 4. Autentikasi, Verifikasi, dan Otorisasi

### 4.1 Autentikasi

Flow autentikasi memakai stack Laravel Breeze + kustomisasi:
- Login umum: `Auth\AuthenticatedSessionController`.
- Login admin khusus URL: `Auth\AdminAuthController`.
- Registrasi user baru: `Auth\RegisteredUserController`.
- Reset password, konfirmasi password, verifikasi email: controller Breeze standar.

Validasi login ditangani oleh:
- `app/Http/Requests/Auth/LoginRequest.php`
- Menerapkan rate limit login (`RateLimiter`) dengan batas 5 percobaan per key email+IP.

### 4.2 Role User

Role user disimpan di kolom `users.role`.

Method helper di `app/Models/User.php`:
- `isAdmin()`
- `isPimpinan()`
- `isUser()`
- `canModify()` -> `true` untuk semua role kecuali `pimpinan`.

### 4.3 Middleware Otorisasi

1. `role` middleware (`app/Http/Middleware/RoleMiddleware.php`)
- Dipakai pada route closure `/admin` dan `/pimpinan`.
- Mekanisme: jika user tidak login atau role tidak sesuai, `abort(403, 'Akses ditolak')`.

2. `can.modify` middleware (`app/Http/Middleware/CheckCanModify.php`)
- Dipakai pada endpoint create/update/delete surat.
- Mekanisme: jika user login tapi `canModify()==false`, redirect ke route `surat` dengan flash error.

Perbedaan perilaku:
- `role:*` -> hard deny (`403`).
- `can.modify` -> soft deny (redirect + pesan).

### 4.4 Verifikasi Email

Mayoritas route bisnis dibungkus `['auth', 'verified']`, sehingga user harus:
1. login, dan
2. email sudah terverifikasi
sebelum mengakses dashboard/manajemen surat/laporan/profil.

## 5. Matriks Endpoint Backend

Berikut ringkasan endpoint penting (57 route total terdaftar di aplikasi):

### 5.1 Public dan Auth Guest

- `GET /` -> `home`
- `GET|POST /login`
- `GET|POST /admin/login`
- `GET|POST /register`
- `GET|POST /forgot-password`
- `GET /reset-password/{token}`
- `POST /reset-password`

### 5.2 Area Authenticated + Verified

- Dashboard:
  - `GET /dashboard` -> `DashboardController@index`
  - `GET /admin` -> closure + `role:admin`
  - `GET /pimpinan` -> closure + `role:pimpinan`
- Profil:
  - `GET|PATCH|DELETE /profile` (profil Breeze)
  - `GET|POST /profil` (profil custom instansi)
- Surat:
  - `GET /surat` -> halaman manajemen gabungan.
  - `Resource /surat-masuk`:
    - `index/show` readable untuk semua user auth+verified.
    - `create/store/edit/update/destroy` wajib `can.modify`.
    - tambahan: `/surat-masuk/{id}/download`, `/preview`.
  - `Resource /surat-keluar`:
    - bekerja di model data `SuratMasuk` (balasan menempel ke surat masuk).
    - `index/show/download/preview` readable semua auth+verified.
    - `create/store/edit/update/destroy` wajib `can.modify`.
    - tambahan: `DELETE /surat-keluar/{surat}/balasan` via `SuratController@destroyBalasan`.
  - `Resource /surat-resource`:
    - endpoint legacy/kompatibilitas, tetap aktif.
- Laporan:
  - `GET /laporan`
  - `GET /laporan/export/pdf/preview`
  - `GET /laporan/export/pdf`
  - `GET /laporan/export/excel`

## 6. Modul Domain: Surat Masuk

Controller utama: `app/Http/Controllers/SuratMasukController.php`.

### 6.1 Validasi Input Create/Update

Create (`store`):
- `asal_surat`: required string max 255
- `perihal`: required string max 500
- `perihal_lainnya`: nullable string max 500
- `tanggal_surat`: required date
- `file_surat`: required file `pdf|docx`, max 5MB

Update (`update`):
- sama seperti create, tapi `file_surat` menjadi nullable.

### 6.2 Aturan Bisnis

- Jika input `perihal == 'lainnya'`, nilai final perihal memakai `perihal_lainnya`.
- Status default surat baru: `Belum Dibalas`.
- Delete surat masuk juga menghapus:
  - `file_surat`
  - `file_balasan` (jika sudah ada balasan)

### 6.3 File Handling

Upload/akses file dipusatkan lewat helper private:
- `suratDisk()`:
  - baca disk dari `config('filesystems.surat_disk', 'public')`
  - validasi disk, fallback ke `public` jika disk invalid.
- `storeUploadedFile(...)`:
  - upload file ke direktori target (`surat-masuk`) di disk terpilih.
  - log error detail jika gagal.
- `resolveFilePath(...)`:
  - normalisasi path (`\` -> `/`, trim slash).
  - mencoba kandidat path lama (`public/...`, `storage/...`) untuk kompatibilitas.

Download/preview:
- `download()` -> `Storage::download(...)`
- `preview()` -> `Storage::response(...)` dengan `Content-Disposition: inline`.

## 7. Modul Domain: Surat Keluar (Balasan)

Controller utama: `app/Http/Controllers/SuratKeluarController.php`.

### 7.1 Konsep Penyimpanan

Walau bernama `SuratKeluarController`, implementasi aktif menggunakan model `SuratMasuk`:
- field balasan (`no_surat_balasan`, `tanggal_balasan`, `tujuan_surat`, `perihal_balasan`, `file_balasan`, `status`) diupdate pada baris `surat_masuk` yang sama.

Jadi secara data:
- Satu surat masuk merepresentasikan satu kemungkinan balasan surat keluar.
- Status balasan diturunkan dari keberadaan metadata/file balasan pada baris tersebut.

### 7.2 Validasi Input Balasan

Create (`store`):
- `surat_masuk_id`: required integer exists
- `no_surat`: nullable string max 255
- `tanggal_surat`: required date
- `tujuan_surat`: required string max 255
- `perihal`: required string
- `perihal_lainnya`: nullable string max 500
- `file_balasan`: required file `pdf|docx`, max 2MB

Update (`update`):
- sama, tetapi `file_balasan` nullable.

### 7.3 Aturan Bisnis Balasan

- `perihal == lainnya` -> gunakan `perihal_lainnya`.
- Nomor balasan auto-generate jika kosong:
  - format: `SK/{id-3-digit}/{tahun}`
- Saat simpan/update balasan, status dipaksa `Sudah Dibalas`.
- Saat hapus balasan, semua kolom balasan dinullkan dan status kembali `Belum Dibalas`.

### 7.4 File Handling Balasan

Mekanisme sama dengan surat masuk:
- upload via `storeUploadedFile(...)` ke folder `surat-keluar`.
- download/preview via disk `suratDisk()`.

## 8. Modul Agregator Surat (Legacy/Kompatibilitas)

Controller: `app/Http/Controllers/SuratController.php`.

Fungsi utama:
- Endpoint `GET /surat` sebagai halaman manajemen gabungan.
- Menyediakan fallback route resource `surat-resource`.
- Menyimpan jejak method legacy `storeMasuk`, `storeKeluar`, `updateBalasan`, dll.

Catatan:
- Ini membuat ada dua jalur logika CRUD serupa (`SuratController` vs `SuratMasukController`/`SuratKeluarController`).
- Dari route aktif, jalur utama domain operasional sekarang ada di controller spesifik (`SuratMasukController`, `SuratKeluarController`), sementara `surat-resource` dipertahankan untuk kompatibilitas.

## 9. Modul Dashboard

Controller: `app/Http/Controllers/DashboardController.php`.

Data yang dihitung:
- total surat masuk sepanjang waktu.
- total surat keluar (berdasarkan `file_balasan NOT NULL`).
- total belum dibalas (`status != 'Sudah Dibalas'` atau null).
- agregasi bulan ini vs bulan lalu.
- statistik kategori perihal:
  - `PKL` (exact match),
  - `Kunjungan` (exact match),
  - sisanya `Lainnya`.
- chart 12 bulan:
  - surat masuk per bulan,
  - surat keluar (balasan) per bulan.
- daftar 5 surat terbaru.

Implementasi memakai `Query Builder (DB::table)` langsung ke `surat_masuk`.

## 10. Modul Laporan dan Export

Controller: `app/Http/Controllers/LaporanController.php`.
Export class: `app/Exports/LaporanSuratExport.php`.

### 10.1 Filter Laporan

Parameter filter:
- `tanggal_mulai`
- `tanggal_akhir`
- `perihal`
- `status` (`semua`, `Sudah Dibalas`, atau selain itu diasumsikan belum dibalas)

Kondisi status laporan:
- `Sudah Dibalas` -> `file_balasan IS NOT NULL`
- lainnya -> `file_balasan IS NULL`

### 10.2 Export Excel

- Endpoint: `GET /laporan/export/excel`
- Menggunakan `Maatwebsite\Excel`.
- Class export menerapkan:
  - `FromCollection`, `WithHeadings`, `WithMapping`,
  - `ShouldAutoSize`, `WithStyles`, `WithTitle`.
- Kolom export mencakup link hyperlink untuk file surat masuk/keluar (`route('surat-masuk.download', id)` dan `route('surat-keluar.download', id)`).

### 10.3 Export PDF

- Endpoint:
  - `GET /laporan/export/pdf` (download)
  - `GET /laporan/export/pdf/preview` (stream inline)
- Menggunakan `Barryvdh\DomPDF`.
- View sumber PDF: `resources/views/laporan.pdf`.
- Opsi:
  - Paper `A4 landscape`
  - font default `Arial`
  - HTML5 parser + PHP enabled.

## 11. Modul Profil

Ada dua jalur profil:

1. Profil Breeze (`ProfileController`)
- endpoint `/profile`
- update via `ProfileUpdateRequest` (name/email/phone)
- delete account dengan verifikasi password saat ini.

2. Profil custom (`ProfilController`)
- endpoint `/profil`
- fokus update data user aktif:
  - `name`
  - `email` unik selain user sendiri
  - `phone`

## 12. Model dan Layer Data

### 12.1 `User` model

File: `app/Models/User.php`
- `fillable`: `name, email, phone, password, role`
- cast:
  - `email_verified_at` -> datetime
  - `password` -> hashed
- helper role:
  - `isAdmin()`, `isPimpinan()`, `isUser()`, `canModify()`.

### 12.2 `SuratMasuk` model

File: `app/Models/SuratMasuk.php`
- Table: `surat_masuk`
- fillable mencakup data surat masuk + balasan.
- cast tanggal:
  - `tanggal_surat` -> `date:Y-m-d`
  - `tanggal_balasan` -> `date:Y-m-d`
- scope `search()` untuk kolom `asal_surat`, `perihal`, `no_surat`, `no_surat_balasan`.

### 12.3 `SuratKeluar` model

File: `app/Models/SuratKeluar.php`
- Mengarah ke table `surat_keluar`.
- Menyediakan relation `belongsTo(SuratMasuk::class)`.

Catatan penting implementasi saat ini:
- Controller operasional balasan tidak memakai table `surat_keluar`, melainkan update table `surat_masuk`.
- Perlu sinkronisasi desain jika nanti ingin benar-benar memisahkan surat keluar ke table tersendiri.

## 13. Skema Database dan Evolusi Migration

### 13.1 Tabel `users`

Terbentuk dari migration dasar + alter:
- `id`
- `name`
- `email` (unique)
- `phone` (nullable, max 50)
- `email_verified_at` nullable
- `password`
- `role` default `user`
- `remember_token`
- `created_at`, `updated_at`

### 13.2 Tabel `surat_masuk`

Kolom inti:
- `id`
- `asal_surat`
- `perihal`
- `tanggal_surat`
- `file_surat` nullable
- `file_balasan` nullable
- `status` default `Belum Dibalas`
- `created_at`, `updated_at`

Kolom tambahan balasan (migration lanjutan):
- `no_surat_balasan` nullable
- `tanggal_balasan` nullable
- `tujuan_surat` nullable
- `perihal_balasan` nullable

### 13.3 Tabel Infrastruktur Laravel

Dibuat oleh migration standar:
- `sessions`
- `password_reset_tokens`
- `cache`, `cache_locks`
- `jobs`, `job_batches`, `failed_jobs`

### 13.4 Seeder

- `DatabaseSeeder` saat ini hanya membuat `Test User`.
- `AdminUserSeeder` membuat user admin dan pimpinan contoh, tetapi tidak dipanggil otomatis oleh `DatabaseSeeder`.

Jika butuh akun admin seed:
- jalankan manual: `php artisan db:seed --class=AdminUserSeeder`

## 14. Storage File dan Integrasi R2

Konfigurasi utama: `config/filesystems.php`.

### 14.1 Konsep Disk Surat

- `surat_disk` ditentukan dari env `SURAT_FILESYSTEM_DISK`.
- fallback ke `FILESYSTEM_DISK`, lalu fallback akhir `public`.
- Tujuan: file surat bisa dipisah dari disk default aplikasi.

### 14.2 Disk Tersedia

- `local` -> `storage/app/private`
- `public` -> `storage/app/public` + URL `/storage`
- `s3` -> AWS S3 generic
- `r2` -> Cloudflare R2 (driver `s3`)

### 14.3 Command Migrasi File ke R2

Tersedia artisan command di `routes/console.php`:
- `surat:migrate-to-r2`

Fitur command:
- memilih disk sumber dan target
- memilih direktori (`surat-masuk`, `surat-keluar`, dst)
- mode `--dry-run`
- opsi `--overwrite`
- opsi `--clear-proxy`
- progress bar + ringkasan hasil

Contoh:
```bash
php artisan surat:migrate-to-r2 --source=public --target=r2 --paths=surat-masuk,surat-keluar --dry-run
```

## 15. Error Handling dan Logging

### 15.1 Global Exception Reporter

Di `bootstrap/app.php`, terdapat report callback custom yang me-log:
- message
- file
- line
- full trace

### 15.2 Logging Upload/Storage

`SuratMasukController` dan `SuratKeluarController`:
- log warning saat file tidak ditemukan untuk preview/download.
- log error saat disk invalid atau upload exception.
- log info untuk payload request balasan (store/update).

### 15.3 Channel Log

Konfigurasi default logging mengikuti `config/logging.php`:
- channel default dari `LOG_CHANNEL` (umumnya `stack` -> `single`).
- file log default: `storage/logs/laravel.log`.

## 16. Konfigurasi Runtime Penting

### 16.1 Session/Cache/Queue

- Session driver default: `database`.
- Cache store default: `database`.
- Queue default: `database`.

Artinya tabel infrastruktur (`sessions`, `cache`, `jobs`) wajib tersedia di environment non-testing.

### 16.2 HTTPS Enforcement

`AppServiceProvider::boot()`:
- jika `APP_ENV === production`, URL dipaksa menggunakan `https`.

## 17. Deployment dan Startup Backend

### 17.1 Dockerfile

Startup command:
```bash
php artisan config:clear && php artisan migrate --force && php artisan storage:link && php -S 0.0.0.0:${PORT:-8080} -t public
```

Implikasi:
- migration dieksekusi tiap container start.
- symlink storage dibuat saat start.
- server menggunakan PHP built-in server.

### 17.2 Nixpacks (`nixpacks.toml`)

Build phase:
- install dependency PHP + Node
- `npm run build`
- `php artisan config:cache`, `route:cache`, `view:cache`

Start phase juga menjalankan:
- `config:clear`
- `migrate --force`
- `storage:link`
- `php -S ... -t public`

## 18. Testing Backend Saat Ini

Framework test: Pest + PHPUnit.

Temuan cakupan:
- test yang ada masih dominan test bawaan auth/profile Laravel.
- belum ada test khusus domain surat:
  - CRUD surat masuk
  - CRUD balasan surat keluar
  - middleware `can.modify`
  - laporan + export
  - command migrasi ke R2

Konfigurasi test (`phpunit.xml`):
- DB in-memory sqlite.
- queue sync.
- session array.

## 19. Catatan Teknis dan Risiko yang Perlu Diperhatikan

1. Kesenjangan model vs migration:
- `SuratMasuk` fillable memuat `no_surat` dan `perihal_lainnya_keluar`, tetapi migration aktif tidak menunjukkan kolom ini dibuat.

2. Model `SuratKeluar` belum terpakai sebagai sumber data utama:
- tidak ada migration `create_surat_keluar_table` di repo.
- domain balasan aktif disimpan di `surat_masuk`.

3. Duplikasi alur surat:
- ada controller legacy `SuratController` bersamaan dengan controller spesifik (`SuratMasukController`, `SuratKeluarController`).
- berpotensi membingungkan maintenance jika dua jalur tetap dipakai bersamaan.

4. Seeder admin tidak otomatis:
- `AdminUserSeeder` tidak dipanggil dari `DatabaseSeeder`.

5. Keamanan konfigurasi:
- file `.env.example` seharusnya berisi placeholder, bukan kredensial nyata.
- praktik aman: rotasi kredensial sensitif dan sanitasi `.env.example`.

## 20. Ringkas Alur Bisnis Utama

1. User login (admin/pimpinan) dan verifikasi email.
2. Admin (atau role non-pimpinan) menambahkan surat masuk + upload file.
3. Sistem menyimpan data ke `surat_masuk` dengan status awal `Belum Dibalas`.
4. Admin membuat balasan surat keluar untuk surat tertentu:
   - metadata balasan + file balasan diupdate di record `surat_masuk`.
   - status menjadi `Sudah Dibalas`.
5. Pimpinan dapat memonitor dashboard, surat, dan laporan tanpa hak modifikasi.
6. Laporan dapat difilter dan diekspor ke PDF/Excel.

---

Dokumen ini fokus pada backend aktual yang terimplementasi di source code saat ini, termasuk area legacy yang masih aktif demi kompatibilitas route lama.
