FROM php:8.2-cli

# ติดตั้ง dependencies ให้ครบในคำสั่งเดียว
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install \
    pdo \
    pdo_sqlite \
    mbstring \
    bcmath \
    gd

# ติดตั้ง composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ตั้ง working directory
WORKDIR /app

# copy project
COPY . .

# install laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# permission
RUN chmod -R 777 storage bootstrap/cache

# สร้าง sqlite + migrate + seed
RUN touch /tmp/database.sqlite && \
    php artisan config:clear && \
    php artisan migrate --force && \
    php artisan db:seed --force

# expose port
EXPOSE 10000

# run app
CMD php artisan serve --host=0.0.0.0 --port=10000