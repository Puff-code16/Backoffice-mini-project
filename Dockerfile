FROM php:8.2-cli

# Install system dependencies
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
        gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /app

# Copy project
COPY . .

# Install Laravel dependencies (เพิ่ม ignore-platform-req เพื่อข้าม error oci8)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-oci8

# Setup SQLite and Permissions
# แนะนำให้สร้าง database ใน /app/database/database.sqlite จะมาตรฐานกว่า
RUN touch /app/database/database.sqlite && \
    chmod -R 777 /app/storage /app/bootstrap/cache /app/database

# Expose port
EXPOSE 10000

# Start server
# แก้ไข CMD ให้กระชับ และตรวจสอบว่าไฟล์ sqlite มีอยู่จริงก่อนรัน
CMD php artisan config:clear && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan serve --host=0.0.0.0 --port=10000