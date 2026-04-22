FROM php:8.2-cli

# ติดตั้ง system dependencies
RUN apt-get update && apt-get install -y \
    git unzip sqlite3 libsqlite3-dev \
    libpng-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install pdo pdo_sqlite mbstring bcmath gd

# ติดตั้ง composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ตั้ง working directory
WORKDIR /app

# copy project
COPY . .

# ติดตั้ง dependencies
RUN composer install --no-dev --optimize-autoloader

# ตั้ง permission (สำคัญ)
RUN chmod -R 777 storage bootstrap/cache

# สร้าง sqlite + migrate + seed
RUN touch /tmp/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --force

# เปิด port
EXPOSE 10000

# start server
CMD php artisan serve --host=0.0.0.0 --port=10000