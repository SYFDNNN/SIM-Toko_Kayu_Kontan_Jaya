# Panduan Docker

## Persyaratan

- Docker Desktop terinstal dan sedang berjalan
- Port `8080`, `8081`, dan `3307` tidak sedang digunakan aplikasi lain

## Menjalankan aplikasi

Dari root project:

```bash
docker compose up -d --build
docker compose ps
```

Buka aplikasi di [http://localhost:8080](http://localhost:8080) dan phpMyAdmin di [http://localhost:8081](http://localhost:8081).

## Konfigurasi database

Konfigurasi development Docker berada di [`env.docker`](../env.docker). Database diinisialisasi dari:

- [`database/migrations/all_migrations.sql`](../database/migrations/all_migrations.sql)
- [`database/seeds/demo_seed.sql`](../database/seeds/demo_seed.sql)

File SQL tersebut hanya dijalankan otomatis ketika volume MySQL masih kosong.

## Troubleshooting

```bash
docker compose logs -f app
docker compose logs -f db
docker compose restart
```

Untuk mengulang database demo dari awal:

```bash
docker compose down -v
docker compose up -d --build
```

Perintah tersebut menghapus volume database lokal.
