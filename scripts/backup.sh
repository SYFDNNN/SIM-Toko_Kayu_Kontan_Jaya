#!/bin/bash
# =============================================================
# backup.sh — Backup database MySQL ke file .sql
# Jalankan: bash scripts/backup.sh
# =============================================================

# Load config dari .env jika ada
ENV_FILE="$(dirname "$0")/../.env"
if [ -f "$ENV_FILE" ]; then
  source "$ENV_FILE" 2>/dev/null
fi

DB_HOST="${database.default.hostname:-localhost}"
DB_USER="${database.default.username:-root}"
DB_PASS="${database.default.password:-}"
DB_NAME="${database.default.database:-toko_kayu_kontan_jaya}"
BACKUP_DIR="$(dirname "$0")/../backups"

mkdir -p "$BACKUP_DIR"
FILENAME="$BACKUP_DIR/${DB_NAME}_$(date +%Y-%m-%d_%H-%M-%S).sql"

echo "🔄 Memulai backup database '$DB_NAME'..."

if [ -z "$DB_PASS" ]; then
  mysqldump -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" > "$FILENAME"
else
  mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$FILENAME"
fi

if [ $? -eq 0 ]; then
  echo "✅ Backup berhasil: $FILENAME"
  echo "   Ukuran: $(du -sh "$FILENAME" | cut -f1)"
else
  echo "❌ Backup gagal!"
  exit 1
fi
