# Gunakan image PHP resmi dengan Apache
FROM php:8.2-apache

# Instal ekstensi PHP yang dibutuhkan (mysqli untuk koneksi database)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Set Apache to listen on port 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Set direktori kerja di dalam container
WORKDIR /var/www/html

# Salin semua file aplikasi dari direktori saat ini ke direktori kerja di container
COPY . /var/www/html/

# Apache akan berjalan di port 80
EXPOSE 8080

# Perintah default untuk menjalankan Apache saat container dimulai
CMD ["apache2-foreground"]