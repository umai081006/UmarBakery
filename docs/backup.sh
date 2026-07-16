#!/bin/bash
# ============================================================
# Umar Bakery — PostgreSQL Automated Backup Script
# ============================================================
# SETUP:
#   1. Copy this script to /usr/local/bin/umarbakery-backup.sh
#   2. Make executable: chmod +x /usr/local/bin/umarbakery-backup.sh
#   3. Add to crontab (run as root or postgres user):
#      crontab -e
#      0 2 * * * /usr/local/bin/umarbakery-backup.sh >> /var/log/umarbakery-backup.log 2>&1
# ============================================================

set -euo pipefail

# ---- Configuration ----
DB_NAME="umar_bakery"
DB_USER="postgres"
DB_HOST="127.0.0.1"
DB_PORT="5432"
BACKUP_DIR="/var/backups/umarbakery"
RETENTION_DAYS=7
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="${BACKUP_DIR}/db_${DATE}.sql.gz"
LOG_PREFIX="[$(date '+%Y-%m-%d %H:%M:%S')] UMAR BAKERY BACKUP:"

# ---- Create backup directory if not exists ----
mkdir -p "${BACKUP_DIR}"
chmod 700 "${BACKUP_DIR}"

echo "${LOG_PREFIX} Starting backup of database '${DB_NAME}'..."

# ---- Perform backup ----
PGPASSWORD="${DB_PASSWORD}" pg_dump \
    -h "${DB_HOST}" \
    -p "${DB_PORT}" \
    -U "${DB_USER}" \
    -d "${DB_NAME}" \
    --format=plain \
    --no-password \
    | gzip -9 > "${BACKUP_FILE}"

if [ $? -eq 0 ]; then
    FILESIZE=$(du -sh "${BACKUP_FILE}" | cut -f1)
    echo "${LOG_PREFIX} SUCCESS. Backup saved: ${BACKUP_FILE} (${FILESIZE})"
else
    echo "${LOG_PREFIX} FAILED. Backup did not complete successfully."
    exit 1
fi

# ---- Cleanup old backups ----
echo "${LOG_PREFIX} Removing backups older than ${RETENTION_DAYS} days..."
find "${BACKUP_DIR}" -name "db_*.sql.gz" -mtime +${RETENTION_DAYS} -delete
echo "${LOG_PREFIX} Cleanup complete."

# ---- Verify backup is readable ----
if gzip -t "${BACKUP_FILE}" 2>/dev/null; then
    echo "${LOG_PREFIX} Integrity check PASSED."
else
    echo "${LOG_PREFIX} WARNING: Integrity check FAILED on ${BACKUP_FILE}."
    exit 2
fi

echo "${LOG_PREFIX} Backup complete."

# ============================================================
# RESTORE PROCEDURE (for disaster recovery):
#
#   gunzip -c /var/backups/umarbakery/db_YYYY-MM-DD_HH-MM-SS.sql.gz \
#     | psql -h 127.0.0.1 -U postgres -d umar_bakery
#
# Before restore, ensure database is empty or recreated:
#   psql -U postgres -c "DROP DATABASE umar_bakery;"
#   psql -U postgres -c "CREATE DATABASE umar_bakery;"
# ============================================================
