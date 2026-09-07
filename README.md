# SIM Toko Kayu Kontan Jaya

Sistem informasi penjualan dan manajemen inventori untuk Toko Kayu Kontan Jaya. Aplikasi ini menyediakan dashboard, manajemen produk, stok masuk/keluar, POS kasir, transaksi, laporan, pengaturan toko, dan REST API.

## Fitur utama

- Autentikasi berbasis role: owner, admin, dan cashier
- Dashboard statistik penjualan dan inventori
- Manajemen produk dan kategori
- Import/export produk melalui CSV
- Pencatatan stok masuk dan stok keluar
- POS kasir dengan invoice transaksi
- Laporan penjualan dan export PDF/CSV
- REST API untuk produk, transaksi, inventori, dan dashboard
- Docker Compose dengan MySQL dan phpMyAdmin

## Menjalankan dengan Docker

Persyaratan: Docker Desktop aktif.

```bash
docker compose up -d --build
```

| Layanan | URL |
| --- | --- |
| Aplikasi | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |
| MySQL dari host | `localhost:3307` |

Login demo:

| Role | Email | Password |
| --- | --- | --- |
| Owner | owner@tokokayukontan.com | password123 |
| Kasir | kasir1@tokokayukontan.com | password123 |

> Kredensial di atas hanya untuk development/demo. Ganti sebelum deployment production.

## Perintah pengembangan

```bash
docker compose ps
docker compose logs -f app
docker compose exec app php spark routes
docker compose down
```

Database awal dibuat otomatis oleh MySQL saat volume database pertama kali dibuat. Untuk mengulang database development dari awal:

```bash
docker compose down -v
docker compose up -d --build
```

Perintah `down -v` menghapus data database Docker lokal.

## Struktur project

```text
├── app/                  # Source code CodeIgniter: config, controllers, models, views
├── database/             # SQL untuk inisialisasi database Docker
├── docker/               # Konfigurasi web server Apache
├── docs/                 # API, ERD, spesifikasi, dan panduan teknis
├── public/               # Document root dan asset publik
├── scripts/              # Script backup dan restore
├── tests/                # Unit dan feature tests
├── writable/             # Cache, log, session, dan upload runtime
├── Dockerfile
├── docker-compose.yml
├── composer.json
└── env.docker
```

Dokumentasi teknis tersedia di [docs/](docs/) dan endpoint API di [docs/API.md](docs/API.md).

## Stack

- PHP 8.3
- CodeIgniter 4
- MySQL 8.0
- Apache
- phpMyAdmin
- PHPUnit
