FROM php:8.2-cli

# ติดตั้ง dependencies
RUN apt-get update && apt-get install -y \
    git unzip sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# ติดตั้ง composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# set working dir
WORKDIR /app

# copy project
COPY . .

# install laravel dependencies
RUN composer install

# สร้าง sqlite db + migrate + seed
RUN touch /tmp/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --force

# start server
CMD php artisan serve --host=0.0.0.0 --port=10000