FROM php:8.2-cli

# Install dependencies (เขียนให้ถูก syntax 100%)
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

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /app

# Copy project
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Fix permissions
RUN chmod -R 777 storage bootstrap/cache

# Create SQLite + migrate + seed
RUN touch /tmp/database.sqlite && \
    php artisan config:clear && \
    php artisan migrate --force && \
    php artisan db:seed --force

# Expose port
EXPOSE 10000

# Start server
CMD php artisan serve --host=0.0.0.0 --port=10000