# Arsitektur Project

Project menggunakan pola MVC CodeIgniter 4 dengan Apache sebagai web server dan Docker Compose sebagai environment development.

```text
Browser / API Client
        │
        ▼
public/index.php
        │
        ▼
app/Config/Routes.php
        │
        ├── app/Controllers/Web   # Halaman aplikasi
        ├── app/Controllers/Api   # Endpoint JSON
        ├── app/Models             # Akses dan aturan data
        └── app/Views              # Antarmuka HTML/PHP
                │
                ▼
             MySQL 8.0
```

## Pembagian folder

- `app/`: kode inti aplikasi dan konfigurasi CodeIgniter.
- `public/`: satu-satunya document root yang diekspos Apache.
- `database/`: SQL bootstrap khusus Docker.
- `docs/`: dokumentasi API, ERD, spesifikasi UI, dan panduan operasional.
- `tests/`: pengujian unit dan fitur.
- `writable/`: data runtime yang tidak termasuk source code.

Migration dan seeder PHP CodeIgniter tetap berada di `app/Database` agar dapat dijalankan melalui Spark. SQL di root `database/` digunakan oleh proses inisialisasi MySQL Docker.
