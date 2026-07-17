# Cara Menjalankan dengan Docker

## Persyaratan
- Install **Docker Desktop**: https://www.docker.com/products/docker-desktop
- Pastikan Docker Desktop sudah berjalan (ada icon Docker di taskbar)

---

## Langkah-langkah

### 1. Copy file konfigurasi Docker
Pastikan folder project berisi:
```
toko-kayu-kontan-jaya/
├── Dockerfile
├── docker-compose.yml
├── env.docker          ← rename jadi .env
└── ...
```

### 2. Rename env.docker menjadi .env
```bash
# Windows
rename env.docker .env

# Mac/Linux
mv env.docker .env
```

### 3. Jalankan Docker
Buka terminal di folder project, lalu:
```bash
docker-compose up -d
```

Tunggu 2-3 menit pertama kali (download image + setup database).

### 4. Cek status container
```bash
docker-compose ps
```
Semua container harus berstatus **Up**.

### 5. Buka aplikasi
- **Aplikasi**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081

### 6. Login
| Role  | Email                         | Password |
|-------|-------------------------------|----------|
| Owner | owner@tokokayukontan.com      | password |
| Kasir | kasir1@tokokayukontan.com     | password |

---

## Perintah Docker Berguna

```bash
# Jalankan container
docker-compose up -d

# Stop container
docker-compose down

# Lihat log aplikasi
docker-compose logs app

# Lihat log database
docker-compose logs db

# Masuk ke container app
docker-compose exec app bash

# Jalankan spark command
docker-compose exec app php spark migrate
docker-compose exec app php spark db:seed DatabaseSeeder

# Restart container
docker-compose restart
```

---

## Troubleshooting

**Port sudah dipakai:**
Edit `docker-compose.yml`, ubah port:
```yaml
ports:
  - "8082:80"   # ganti 8080 ke port lain
```

**Database error:**
```bash
docker-compose down -v   # hapus semua data
docker-compose up -d     # mulai ulang
```

**Permission error:**
```bash
docker-compose exec app chmod -R 777 writable/
```