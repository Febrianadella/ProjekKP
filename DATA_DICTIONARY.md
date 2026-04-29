# Data Dictionary

Dokumen ini merangkum struktur tabel database berdasarkan migration yang ada di project.

## Ringkasan Tabel

| Tabel | Fungsi |
|---|---|
| users | Data akun pengguna aplikasi |
| surat_masuk | Data surat masuk dan data balasan surat keluar |

## Tabel: `users`

| Kolom | Tipe | Null | Key/Index | Default | Keterangan |
|---|---|---|---|---|---|
| id | BIGINT UNSIGNED | Tidak | PK | auto increment | ID user |
| name | VARCHAR(255) | Tidak | - | - | Nama user |
| email | VARCHAR(255) | Tidak | UNIQUE | - | Email login |
| phone | VARCHAR(50) | Ya | - | NULL | Nomor telepon |
| email_verified_at | TIMESTAMP | Ya | - | NULL | Waktu verifikasi email |
| password | VARCHAR(255) | Tidak | - | - | Password hash |
| role | VARCHAR(255) | Tidak | - | `user` | Role: `admin`, `pimpinan`, `user` |
| remember_token | VARCHAR(100) | Ya | - | NULL | Token remember me |
| created_at | TIMESTAMP | Ya | - | NULL | Timestamp dibuat |
| updated_at | TIMESTAMP | Ya | - | NULL | Timestamp diubah |

## Tabel: `surat_masuk`

| Kolom | Tipe | Null | Key/Index | Default | Keterangan |
|---|---|---|---|---|---|
| id | BIGINT UNSIGNED | Tidak | PK | auto increment | ID surat masuk |
| asal_surat | VARCHAR(255) | Tidak | - | - | Instansi/pengirim surat |
| perihal | VARCHAR(255) | Tidak | - | - | Perihal surat masuk |
| tanggal_surat | DATE | Tidak | - | - | Tanggal pada surat masuk |
| file_surat | VARCHAR(255) | Ya | - | NULL | Path file surat masuk |
| no_surat_balasan | VARCHAR(255) | Ya | - | NULL | Nomor surat balasan |
| tanggal_balasan | DATE | Ya | - | NULL | Tanggal surat balasan |
| tujuan_surat | VARCHAR(255) | Ya | - | NULL | Tujuan surat balasan |
| perihal_balasan | TEXT | Ya | - | NULL | Perihal surat balasan |
| file_balasan | VARCHAR(255) | Ya | - | NULL | Path file surat balasan |
| status | ENUM('Belum Dibalas','Sudah Dibalas') | Tidak | - | `Belum Dibalas` | Status tindak lanjut surat |
| created_at | TIMESTAMP | Ya | - | NULL | Timestamp dibuat |
| updated_at | TIMESTAMP | Ya | - | NULL | Timestamp diubah |

## Relasi Antar Tabel (Aktual)

Pada schema database yang sedang dipakai, tabel `users` dan `surat_masuk` memang **tidak memiliki relasi langsung**. Penyebabnya adalah tabel `surat_masuk` tidak mempunyai kolom foreign key seperti `user_id`, `created_by`, atau `updated_by` yang mengacu ke `users.id`. Karena itu, secara ERD fisik kedua tabel tersebut berdiri sendiri.

Hubungan antara `users` dan modul surat terjadi di **level aplikasi**, bukan di level foreign key database. Tabel `users` dipakai untuk autentikasi login dan otorisasi berdasarkan kolom `role`, lalu hak akses ke fitur surat dikontrol oleh middleware `role` dan `can.modify` pada routing Laravel.

Selain itu, data balasan surat keluar juga **tidak disimpan di tabel terpisah**. Implementasi aktif saat ini menaruh data balasan langsung pada record `surat_masuk` yang sama melalui kolom:

- `no_surat_balasan`
- `tanggal_balasan`
- `tujuan_surat`
- `perihal_balasan`
- `file_balasan`
- `status`

Dengan demikian, relasi bisnis yang berjalan saat ini adalah:

1. `users` mengelola akses ke sistem.
2. `surat_masuk` menyimpan data surat masuk sekaligus data balasannya.
3. Hubungan pengguna dengan data surat bersifat prosedural di kode program, bukan relasi referensial antar tabel.

## Catatan Penting

1. Tidak ada migration aktif untuk tabel `surat_keluar`. Operasional balasan surat saat ini disimpan langsung di tabel `surat_masuk`.
2. Kolom `no_surat`, `perihal_lainnya`, dan `perihal_lainnya_keluar` muncul di model `SuratMasuk`, tetapi tidak ditemukan pada migration tabel `surat_masuk`.
3. Sebagian relasi bersifat logis di level aplikasi (contoh `sessions.user_id` ke `users.id`) tanpa foreign key constraint di database.

## Narasi ERD Revisi

Pada implementasi basis data SIFORA saat ini, entitas utama yang digunakan dalam proses bisnis adalah `users` dan `surat_masuk`. Namun, kedua entitas tersebut belum memiliki hubungan langsung pada level basis data karena tabel `surat_masuk` tidak mempunyai foreign key yang mengacu ke tabel `users`. Tabel `users` berfungsi sebagai penyimpan data akun, autentikasi, dan otorisasi pengguna melalui atribut seperti `id`, `name`, `email`, `phone`, `password`, dan `role`. Sementara itu, tabel `surat_masuk` menjadi entitas inti yang menyimpan seluruh data surat masuk, meliputi asal surat, perihal, tanggal surat, file surat, serta data balasan seperti nomor balasan, tanggal balasan, tujuan surat, perihal balasan, file balasan, dan status surat. Dengan desain ini, hubungan antara pengguna dan pengelolaan surat tidak dimodelkan sebagai relasi foreign key, melainkan berlangsung pada layer aplikasi melalui proses login dan pembatasan hak akses berdasarkan role. Oleh karena itu, ERD aktual sistem lebih tepat dijelaskan sebagai dua entitas utama yang berdiri sendiri, sedangkan keterkaitan proses bisnisnya terjadi pada logika aplikasi, bukan pada constraint relasional di database.
