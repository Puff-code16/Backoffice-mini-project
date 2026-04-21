FROM php:8.2-apache

# 1. ติดตั้ง dependencies พื้นฐานและ libaio1 (จำเป็นสำหรับ Oracle)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libaio1 \
    wget

# 2. ติดตั้ง PHP Extensions พื้นฐาน
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. ติดตั้ง Oracle Instant Client และ OCI8
WORKDIR /opt/oracle
RUN wget https://download.oracle.com/otn_software/linux/instantclient/191000/instantclient-basic-linux.x64-19.10.0.0.0dbru.zip && \
    wget https://download.oracle.com/otn_software/linux/instantclient/191000/instantclient-sdk-linux.x64-19.10.0.0.0dbru.zip && \
    unzip instantclient-basic-linux.x64-19.10.0.0.0dbru.zip && \
    unzip instantclient-sdk-linux.x64-19.10.0.0.0dbru.zip && \
    rm -rf *.zip

# ตั้งค่า Library Path ให้ระบบรู้จัก Oracle Client
RUN echo /opt/oracle/instantclient_19_10 > /etc/ld.so.conf.d/oracle-instantclient.conf && ldconfig
ENV LD_LIBRARY_PATH /opt/oracle/instantclient_19_10

# ติดตั้ง OCI8 Extension (สำหรับ PHP 8.2 ใช้เวอร์ชัน 3.0.1)
RUN echo "instantclient,/opt/oracle/instantclient_19_10" | pecl install oci8-3.0.1 && \
    docker-php-ext-enable oci8

# 4. กลับมาจัดการเรื่อง Apache และ Code
WORKDIR /var/www/html
RUN a2enmod rewrite

# ตั้งค่าตำแหน่งไฟล์ Public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# คัดลอกโค้ด
COPY . /var/www/html

# ติดตั้ง Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# ตั้งค่าสิทธิ์โฟลเดอร์
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80