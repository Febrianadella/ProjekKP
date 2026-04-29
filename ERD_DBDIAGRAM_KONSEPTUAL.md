# ERD DBDiagram (Versi Konseptual / Target Design)

Salin blok `dbml` berikut ke [dbdiagram.io](https://dbdiagram.io).

Tujuan versi ini:
- Memisahkan data balasan ke tabel `surat_keluar` (normalisasi).
- Menambahkan relasi ke `users` untuk audit (siapa yang membuat/mengubah data).

```dbml
Enum user_role {
  admin
  pimpinan
  user
}

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
  role user_role [not null, default: 'user']
  remember_token varchar(100)
  created_at timestamp
  updated_at timestamp
}

Table surat_masuk {
  id bigint [pk, increment]
  asal_surat varchar(255) [not null]
  perihal varchar(255) [not null]
  tanggal_surat date [not null]
  file_surat varchar(255)
  status status_surat [not null, default: 'Belum Dibalas']
  created_by bigint [not null, note: 'User yang input surat masuk']
  updated_by bigint [note: 'User terakhir yang mengubah']
  created_at timestamp
  updated_at timestamp

  Indexes {
    tanggal_surat
    status
    created_by
  }
}

Table surat_keluar {
  id bigint [pk, increment]
  surat_masuk_id bigint [not null, unique, note: '1 surat keluar untuk 1 surat masuk']
  no_surat varchar(255)
  tanggal_surat date [not null]
  tujuan_surat varchar(255) [not null]
  perihal varchar(255) [not null]
  file_balasan varchar(255)
  created_by bigint [not null, note: 'User yang membuat balasan']
  updated_by bigint [note: 'User terakhir yang mengubah balasan']
  created_at timestamp
  updated_at timestamp

  Indexes {
    surat_masuk_id
    tanggal_surat
    created_by
  }
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
Ref: surat_masuk.created_by > users.id
Ref: surat_masuk.updated_by > users.id
Ref: surat_keluar.surat_masuk_id > surat_masuk.id
Ref: surat_keluar.created_by > users.id
Ref: surat_keluar.updated_by > users.id
```
