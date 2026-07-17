#!/bin/bash
# =============================================================
# restore.sh — Restore database dari file .sql
# Jalankan: bash scripts/restore.sh backups/toko_kayu_2024-01-01_12-00-00.sql
# =============================================================

ENV_FILE="$(dirname "$0")/../.env"
if [ -f "$ENV_FILE" ]; then
  source "$ENV_FILE" 2>/dev/null
fi

DB_HOST="${database.default.hostname:-localhost}"
DB_USER="${database.default.username:-root}"
DB_PASS="${database.default.password:-}"
DB_NAME="${database.default.database:-toko_kayu_kontan_jaya}"

BACKUP_FILE="$1"
if [ -z "$BACKUP_FILE" ]; then
  echo "❌ Usage: bash scripts/restore.sh <path/to/backup.sql>"
  exit 1
fi
if [ ! -f "$BACKUP_FILE" ]; then
  echo "❌ File backup tidak ditemukan: $BACKUP_FILE"
  exit 1
fi

echo "⚠️  PERINGATAN: Restore akan menimpa database '$DB_NAME'!"
read -p "Lanjutkan? (y/N): " confirm
if [[ "$confirm" != "y" && "$confirm" != "Y" ]]; then
  echo "Dibatalkan."
  exit 0
fi

echo "🔄 Memulai restore dari '$BACKUP_FILE'..."

if [ -z "$DB_PASS" ]; then
  mysql -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" < "$BACKUP_FILE"
else
  mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$BACKUP_FILE"
fi

if [ $? -eq 0 ]; then
  echo "✅ Restore berhasil!"
else
  echo "❌ Restore gagal!"
  exit 1
fi
