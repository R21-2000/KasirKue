FROM php:8.2-cli

# Install alat dasar dan ekstensi PostgreSQL buat nyambung ke Supabase
RUN apt-get update -y && apt-get install -y libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Tarik Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Atur folder kerja
WORKDIR /app
COPY . /app

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# Buka akses folder biar Laravel nggak error 500
RUN chmod -R 777 storage bootstrap/cache

# Buka port 10000 (standarnya Render)
EXPOSE 10000

# Jalankan server
CMD php artisan serve --host=0.0.0.0 --port=10000
