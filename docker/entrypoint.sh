#!/bin/sh
set -e

echo "==> Memulai container EduTech Festival BTIKP..."

# Tunggu database siap jika DB_HOST tersedia
if [ -n "$DB_HOST" ]; then
    echo "==> Menunggu database $DB_HOST:${DB_PORT:-3306} siap..."
    max_tries=30
    count=0
    until nc -z -v -w3 "$DB_HOST" "${DB_PORT:-3306}" 2>/dev/null || [ $count -ge $max_tries ]; do
        sleep 2
        count=$((count+1))
        echo "Menunggu koneksi database ($count/$max_tries)..."
    done
fi

# Pastikan folder penting ada dan memiliki hak akses yang benar
mkdir -p storage/app/public/profile-photos storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Buat symbolic link storage jika belum ada
if [ ! -L "public/storage" ]; then
    echo "==> Membuat storage symlink..."
    php artisan storage:link || true
fi

# Migrasi otomatis jika diaktifkan (default: true)
if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
    echo "==> Menjalankan database migration..."
    php artisan migrate --force || echo "Warning: Gagal menjalankan migrasi, lewati..."
fi

# Seeder otomatis jika diaktifkan (opsional)
if [ "${AUTO_SEED:-false}" = "true" ]; then
    echo "==> Menjalankan database seeder..."
    php artisan db:seed --force || echo "Warning: Gagal menjalankan seeder, lewati..."
fi

# Optimasi cache untuk production
if [ "${APP_ENV:-production}" = "production" ]; then
    echo "==> Mengoptimalkan cache konfigurasi dan rute..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> EduTech Festival BTIKP siap dijalankan!"
exec "$@"
