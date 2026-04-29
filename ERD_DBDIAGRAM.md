# ERD DBDiagram (Schema Aktual `db_surat_bbpbl`)

Salin blok `dbml` di bawah ini ke [dbdiagram.io](https://dbdiagram.io).

Catatan:
- Tabel `users` dan `surat_masuk` pada schema aktual memang belum memiliki relasi langsung karena belum ada kolom foreign key seperti `user_id`, `created_by`, atau `updated_by` pada `surat_masuk`.
- Relasi `sessions.user_id -> users.id` dan `password_reset_tokens.email -> users.email` di bawah ini dimodelkan sebagai relasi logis (karena di DB saat ini belum ada FK constraint fisik).
- Tabel `surat_keluar` tidak dimasukkan karena memang belum ada di schema aktual.

```dbml
Enum status_surat {
  "Belum Dibalas"
  "Sudah Dibalas"
}

Table users {
  id bigint [pk, increment]
  name varchar(255) [not null]
  email varchar(255) [not null, unique]
  phone varchar(50)
  email_verified_at timestamp
  password varchar(255) [not null]
  remember_token varchar(100)
  created_at timestamp
  updated_at timestamp
  role varchar(255) [not null, default: 'user']
}

Table surat_masuk {
  id bigint [pk, increment]
  asal_surat varchar(255) [not null]
  perihal varchar(255) [not null]
  tanggal_surat date [not null]
  file_surat varchar(255)
  no_surat_balasan varchar(255)
  tanggal_balasan date
  tujuan_surat varchar(255)
  perihal_balasan text
  file_balasan varchar(255)
  status status_surat [not null, default: 'Belum Dibalas']
  created_at timestamp
  updated_at timestamp
}

Table password_reset_tokens {
  email varchar(255) [pk]
  token varchar(255) [not null]
  created_at timestamp
}

Table sessions {
  id varchar(255) [pk]
  user_id bigint
  ip_address varchar(45)
  user_agent text
  payload longtext [not null]
  last_activity int [not null]

  Indexes {
    user_id
    last_activity
  }
}

Ref: sessions.user_id > users.id
Ref: password_reset_tokens.email > users.email

```

Penjelasan singkat ERD aktual:

- `users` dipakai untuk autentikasi dan otorisasi berbasis role.
- `surat_masuk` dipakai sebagai tabel inti arsip surat sekaligus penyimpanan data balasan.
- Tidak ada relasi langsung `users -> surat_masuk` pada database; hubungan keduanya terjadi di level aplikasi melalui middleware akses.
