FROM php:8.2-apache

# 1. ติดตั้ง dependencies (ใช้ libaio-dev เพื่อให้ผ่าน)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libaio-dev \
    wget

# 2. ติดตั้ง PHP Extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. ติดตั้ง Oracle Instant Client และ OCI8
WORKDIR /opt/oracle
RUN wget https://download.oracle.com/otn_software/linux/instantclient/191000/instantclient-basic-linux.x64-19.10.0.0.0dbru.zip && \
    wget https://download.oracle.com/otn_software/linux/instantclient/191000/instantclient-sdk-linux.x64-19.10.0.0.0dbru.zip && \
    unzip instantclient-basic-linux.x64-19.10.0.0.0dbru.zip && \
    unzip instantclient-sdk-linux.x64-19.10.0.0.0dbru.zip && \
    rm -rf *.zip

RUN echo /opt/oracle/instantclient_19_10 > /etc/ld.so.conf.d/oracle-instantclient.conf && ldconfig
ENV LD_LIBRARY_PATH /opt/oracle/instantclient_19_10

RUN echo "instantclient,/opt/oracle/instantclient_19_10" | pecl install oci8-3.0.1 && \
    docker-php-ext-enable oci8

# 4. จัดการ Apache และ Code
WORKDIR /var/www/html
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

COPY . /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80