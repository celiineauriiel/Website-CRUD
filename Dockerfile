# Gunakan image PHP resmi dengan Apache
FROM php:8.0-apache

# Instal ekstensi PHP yang dibutuhkan (mysqli untuk koneksi database)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Set direktori kerja di dalam container
WORKDIR /var/www/html

# Salin semua file aplikasi dari direktori saat ini ke direktori kerja di container
# Termasuk folder img, css, db (jika ada file lain selain schema.sql yang dibutuhkan saat runtime)
COPY . /var/www/html/

# (Opsional, jika ada masalah permission) Berikan permission ke direktori web server
# RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Apache akan berjalan di port 80
EXPOSE 80

# Perintah default untuk menjalankan Apache saat container dimulai
CMD ["apache2-foreground"]
