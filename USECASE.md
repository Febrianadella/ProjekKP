# Use Case SIFORA (Surat BBPBL)

## Ringkasan
SIFORA adalah aplikasi pengelolaan surat masuk dan balasan surat keluar untuk BBPBL.  
Fitur utama aplikasi mencakup manajemen surat, monitoring status balasan, serta pembuatan laporan dengan ekspor PDF/Excel.

## Aktor
1. `Admin`
2. `Pimpinan` (akses baca/monitoring)

## Use Case Utama
1. `Mendaftarkan Akun`
2. `Masuk ke Sistem`
3. `Memverifikasi Email`
4. `Melihat Dashboard`
5. `Mengelola Surat Masuk`
6. `Menambahkan Surat Masuk`
7. `Mengubah Surat Masuk`
8. `Menghapus Surat Masuk`
9. `Mengelola Balasan Surat Keluar`
10. `Menambahkan Balasan Surat Keluar`
11. `Mengubah Balasan Surat Keluar`
12. `Menghapus Balasan Surat Keluar`
13. `Melihat Surat`
14. `Mempratinjau Surat`
15. `Mengunduh Surat`
16. `Melihat Laporan`
17. `Memfilter Laporan`
18. `Mengekspor Laporan PDF`
19. `Mengekspor Laporan Excel`
20. `Melihat Profil`
21. `Memperbarui Profil`

## Hak Akses Singkat
- `Admin`: mengakses fitur operasional surat dan seluruh fitur monitoring.
- `Pimpinan`: mengakses registrasi, monitoring, laporan, dan profil tanpa aksi ubah data surat.

## Kode PlantUML (Use Case Diagram)
```plantuml
@startuml
left to right direction

actor "Admin" as Admin
actor "Pimpinan" as Pimpinan
Pimpinan -[hidden]-> Admin

rectangle "SIFORA - Sistem Informasi Persuratan BBPBL" {
  usecase "Mendaftarkan Akun" as UC1
  usecase "Masuk ke Sistem" as UC2
  usecase "Memverifikasi Email" as UC3
  usecase "Melihat Dashboard" as UC4

  usecase "Mengelola Surat Masuk" as UC5
  usecase "Menambahkan Surat Masuk" as UC6
  usecase "Mengubah Surat Masuk" as UC7
  usecase "Menghapus Surat Masuk" as UC8

  usecase "Mengelola Balasan Surat Keluar" as UC9
  usecase "Menambahkan Balasan Surat Keluar" as UC10
  usecase "Mengubah Balasan Surat Keluar" as UC11
  usecase "Menghapus Balasan Surat Keluar" as UC12

  usecase "Melihat Surat" as UC13
  usecase "Mempratinjau Surat" as UC14
  usecase "Mengunduh Surat" as UC15

  usecase "Melihat Laporan" as UC16
  usecase "Memfilter Laporan" as UC17
  usecase "Mengekspor Laporan PDF" as UC18
  usecase "Mengekspor Laporan Excel" as UC19

  usecase "Melihat Profil" as UC20
  usecase "Memperbarui Profil" as UC21
}

UC2 --> Admin
UC3 --> Admin
UC4 --> Admin
UC5 --> Admin
UC9 --> Admin
UC13 --> Admin
UC16 --> Admin
UC20 --> Admin
UC21 --> Admin

Pimpinan --> UC1
Pimpinan --> UC2
Pimpinan --> UC3
Pimpinan --> UC4
Pimpinan --> UC13
Pimpinan --> UC16
Pimpinan --> UC20
Pimpinan --> UC21

UC5 ..> UC6 : <<include>>
UC5 ..> UC7 : <<include>>
UC5 ..> UC8 : <<include>>

UC9 ..> UC10 : <<include>>
UC9 ..> UC11 : <<include>>
UC9 ..> UC12 : <<include>>
UC9 ..> UC13 : <<include>>

UC14 ..> UC13 : <<extend>>
UC15 ..> UC13 : <<extend>>

UC17 ..> UC16 : <<extend>>
UC18 ..> UC16 : <<extend>>
UC19 ..> UC16 : <<extend>>

UC21 ..> UC20 : <<extend>>
@enduml
```
