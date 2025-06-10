# SOLUSI: Ganti versi PHP dari 8.0 ke 8.2 untuk memenuhi persyaratan Composer
FROM php:8.2-apache

# Instal ekstensi PHP yang dibutuhkan (mysqli untuk koneksi database)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Set Apache to listen on port 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Set direktori kerja di dalam container
WORKDIR /var/www/html

# Instal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin file definisi dependensi terlebih dahulu
COPY composer.json composer.lock* ./

# Jalankan composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Salin sisa file aplikasi Anda
COPY . /var/www/html/

# Pastikan izin file sudah benar
RUN chown -R www-data:www-data /var/www/html

# Port yang akan diekspos
EXPOSE 8080

# Perintah default untuk menjalankan Apache
CMD ["apache2-foreground"]