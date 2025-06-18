# Sistem Admin Data Mahasiswa (Aplikasi CRUD dengan CI/CD & IaC)

Aplikasi web PHP sederhana untuk melakukan operasi Create, Read, Update, Delete (CRUD) pada data mahasiswa. Proyek ini dilengkapi dengan pipeline CI/CD otomatis menggunakan GitHub Actions, manajemen infrastruktur sebagai kode (IaC) dengan Terraform, pengujian unit dengan PHPUnit, serta monitoring dengan Prometheus & Grafana, yang dideploy ke Google Cloud Platform.

## Deskripsi Proyek

Aplikasi ini memungkinkan administrator untuk mengelola data mahasiswa, termasuk informasi pribadi dan akademik. Fitur utama meliputi autentikasi pengguna, manajemen data mahasiswa (tambah, lihat, ubah, hapus), ekspor data, dan sistem monitoring untuk memantau kesehatan aplikasi serta infrastruktur.

## Fitur Utama

* Autentikasi Pengguna (Login & Registrasi)
* Manajemen Data Mahasiswa (CRUD)
* Upload Gambar Profil Mahasiswa
* Pencarian dan Tampilan Detail Mahasiswa
* Ekspor Data Mahasiswa ke format XLS dan CSV
* Desain Responsif dengan Bootstrap
* Monitoring Aplikasi & Infrastruktur dengan Dashboard Grafana

## Teknologi & Tools yang Digunakan

* **Backend:** PHP (prosedural dengan `mysqli`)
* **Database:** MySQL (dikelola oleh Google Cloud SQL)
* **Frontend:** HTML, CSS, JavaScript
    * Framework & Library: Bootstrap 5, jQuery, SweetAlert2, GSAP, DataTables
* **Containerization:** Docker, `Dockerfile`
* **CI/CD (Continuous Integration/Continuous Deployment):**
    * GitHub Actions (`.github/workflows/main.yml`)
* **Cloud Platform:** Google Cloud Platform (GCP)
    * **Google Cloud Run:** Untuk hosting aplikasi ter-container.
    * **Google Cloud SQL:** Untuk layanan database terkelola.
    * **Google Artifact Registry:** Untuk penyimpanan *image Docker*.
* **Infrastructure as Code (IaC):**
    * Terraform (untuk memprovisikan resource GCP).
* **Unit Testing:**
    * PHPUnit (untuk pengujian unit kode PHP).
* **Monitoring & Observability:**
    * **Prometheus:** Untuk pengumpulan dan penyimpanan data metrik.
    * **Grafana:** Untuk visualisasi data dan pembuatan dashboard.
    * **Prometheus Pushgateway:** Sebagai jembatan untuk menerima metrik dari proses singkat seperti CI/CD.
* **Manajemen Dependensi PHP:**
    * Composer

## Penyiapan Sistem Monitoring (Prometheus & Grafana)

Sistem monitoring berjalan di atas VM (Virtual Machine) Google Compute Engine (GCE) yang terpisah.

1.  **Provisioning Server**: Buat sebuah instance GCE (misalnya, tipe `e2-medium`) dengan OS Ubuntu. Install `docker` dan `docker-compose` di dalamnya.

2.  **Konfigurasi Docker Compose**:
    * Lakukan SSH ke dalam instance GCE tersebut.
    * Buat sebuah direktori (misalnya, `my-monitoring`).
    * Di dalam direktori tersebut, buat file `docker-compose.yml` untuk mendefinisikan layanan `prometheus`, `grafana`, dan `pushgateway`.
    * Buat juga sub-direktori `prometheus/` yang berisi file `prometheus.yml` untuk mengkonfigurasi target scrape (yaitu Pushgateway).
    * Jalankan dengan `docker-compose up -d`.

3.  **Konfigurasi Firewall**:
    * Di Google Cloud Console, pada bagian **VPC network > Firewall**, buat dua *firewall rule* baru.
    * Izinkan traffic masuk (ingress) untuk port **TCP `3000`** (untuk akses Grafana).
    * Izinkan traffic masuk (ingress) untuk port **TCP `9091`** (untuk menerima data di Pushgateway).
    * Terapkan kedua rule ini ke GCE Anda menggunakan *network tags*.

## Penyiapan Proyek & Instalasi (Pengembangan Lokal)

Berikut adalah langkah-langkah untuk menjalankan aplikasi ini di lingkungan pengembangan lokal Anda:

1.  **Prasyarat:**
    * Git, PHP, Composer, Docker Desktop, Terraform CLI.
    * Server database MySQL lokal (misalnya dari XAMPP atau MAMP).

2.  **Clone Repositori:**
    ```bash
    git clone [URL_REPOSITORI_ANDA]
    cd [NAMA_REPOSITORI_ANDA]
    ```

3.  **Instal Dependensi PHP:**
    ```bash
    composer install
    ```

4.  **Konfigurasi Database Lokal:**
    * Buat sebuah database MySQL baru di server lokal Anda (misalnya, `Data_siswa`).
    * Pastikan file `function.php` Anda memiliki logika *fallback* untuk terhubung ke database lokal.

5.  **Impor Skema dan Data Database Lokal:**
    Jalankan perintah ini dari terminal di direktori utama proyek:
    ```bash
    mysql -u [USERNAME_DB_ANDA] -p [NAMA_DATABASE_ANDA] < db/data_siswa.sql
    ```
    Anda akan diminta untuk memasukkan password database Anda.

6.  **Jalankan Aplikasi Secara Lokal:**
    ```bash
    php -S localhost:8000
    ```
    Buka `http://localhost:8000` di browser Anda.
7.  **Set Up Terraform:**
   * Buat project di GCP
   * Install terraform CLI
   * Jalankan
    ```
    terraform init
    ```
   * Jalankan
    ```
    terraform plan -var="gcp_project_id=[ID_PROYEK_GCP_ANDA]" -var="db_password=[PASSWORD_DB_RAHASIA_UNTUK_APP_USER]"
    ```
   * Jalankan
    ```
    terraform apply -var="gcp_project_id=[ID_PROYEK_GCP_ANDA]" -var="db_password=[PASSWORD_DB_RAHASIA_UNTUK_APP_USER]"
    ```
## Alur Kerja CI/CD (GitHub Actions)

Pipeline CI/CD diatur dalam file `.github/workflows/main.yml`.

* **Pemicu:** Otomatis berjalan setiap kali ada `git push` ke *branch* `main`.
* **Langkah Utama Pipeline:**
    1.  Checkout Kode.
    2.  Setup PHP & Composer, lalu instal dependensi.
    3.  Jalankan PHPUnit Tests. Pipeline akan gagal jika ada tes yang tidak lolos.
    4.  Autentikasi ke GCP.
    5.  Build *Image Docker* menggunakan `Dockerfile`.
    6.  Push *Image Docker* ke Google Artifact Registry.
    7.  Deploy ke Google Cloud Run dengan *image* terbaru dan variabel lingkungan yang sesuai.
    8.  **Kirim Notifikasi Deploy**: Setelah deploy selesai (baik sukses maupun gagal), pipeline mengirimkan metrik status ke Prometheus Pushgateway.

## Menjalankan Tes (PHPUnit)

Tes unit disimpan di direktori `tests/`.

* **Menjalankan Semua Tes (Lokal):**
    ```bash
    composer test
    ```

## Aplikasi yang Sudah Di-deploy

Setelah pipeline CI/CD berhasil, aplikasi akan dapat diakses melalui URL yang disediakan oleh layanan Google Cloud Run Anda.

---

## Tantangan & Pemecahan Masalah (Problem Solving Journey)

Selama pengembangan proyek ini, beberapa tantangan teknis muncul. Berikut adalah catatan mengenai masalah yang dihadapi dan solusinya.

### 1. Masalah Koneksi dan Otentikasi
* **Masalah:** Mengalami kesulitan menghubungkan berbagai layanan (seperti Terraform dan PHPUnit lokal) ke Google Cloud Platform, yang menghasilkan error seperti `could not find default credentials` dan `Access denied`.
* **Solusi:**
    * **Membuat Service Account Khusus:** Untuk setiap kebutuhan (Terraform, CI/CD), dibuatkan Service Account terpisah di GCP dengan izin yang spesifik (Prinsip Hak Akses Minimal).
    * **Menggunakan Kunci JSON:** Kunci JSON dari Service Account diunduh dan digunakan untuk otentikasi.
    * **Mengatur Variabel Lingkungan:** Untuk pengembangan lokal, variabel lingkungan `GOOGLE_APPLICATION_CREDENTIALS` diatur secara permanen di Windows agar menunjuk ke lokasi file kunci JSON, yang menyelesaikan masalah otentikasi Terraform.

### 2. Error pada Pipeline CI/CD di GitHub Actions
* **Masalah:** Pipeline awal gagal di beberapa langkah, seperti tidak menemukan `Dockerfile` atau mengalami masalah versi dependensi.
* **Solusi:**
    * **Struktur Direktori:** Memastikan `Dockerfile` dan direktori `.github/workflows` berada di *root* proyek.
    * **Konflik Versi PHP:** Error terjadi karena versi PHPUnit yang ditentukan di `composer.lock` membutuhkan versi PHP yang lebih tinggi (`>=8.1`) daripada yang dikonfigurasi di pipeline (`8.0`). Solusinya adalah menyamakan versi PHP di pipeline GitHub Actions (`.github/workflows/main.yml`) dengan yang dibutuhkan oleh dependensi.

### 3. Kegagalan Pengujian Otomatis dengan PHPUnit
* **Masalah:** Saat menjalankan `composer test`, muncul banyak error fatal (`Cannot redeclare query()`) dan kegagalan tes (`Failed asserting that 0 matches expected 1`).
* **Solusi:**
    * **Menggunakan `require_once`:** Akar masalahnya adalah penggunaan `require 'function.php';` di banyak file, yang menyebabkan file dimuat berulang kali dan koneksi database ter-reset selama tes. Solusinya adalah mengganti semua `require` menjadi `require_once` di seluruh file aplikasi.
    * **Modernisasi `function.php`:** Kode di `function.php` di-refactor secara signifikan. Sebuah fungsi pusat `get_db_connection()` dibuat untuk mengelola koneksi database secara konsisten (menggunakan *Singleton Pattern*). Semua fungsi lain kemudian diubah untuk memanggil fungsi pusat ini, menghilangkan ketergantungan pada variabel global `$koneksi`.
    * **Keamanan Database:** Fungsi-fungsi diubah untuk menggunakan **Prepared Statements** (`mysqli_prepare`, `mysqli_stmt_bind_param`, `mysqli_stmt_execute`) untuk mencegah serangan SQL Injection, dan menggunakan `password_hash()` serta `password_verify()` untuk manajemen password yang aman, menggantikan `md5()`.
    * **Menggunakan Database Tes:** Awalnya ada keraguan, namun akhirnya disepakati untuk menggunakan database terpisah (`Data_siswa_test`) khusus untuk pengujian. Ini memastikan data asli tidak pernah terganggu dan hasil tes selalu konsisten karena setiap tes dimulai dari keadaan bersih (`TRUNCATE TABLE`).

### 4. Konfigurasi Terraform
* **Masalah:** Saat menjalankan `terraform plan`, muncul error `Missing required argument "service"` dan `Unsupported argument "name"` pada resource IAM Cloud Run.
* **Solusi:** Berdasarkan dokumentasi Terraform, blok resource `google_cloud_run_service_iam_member` diperbaiki dengan mengganti argumen `name` menjadi `service` untuk mereferensikan nama layanan Cloud Run dengan benar.

Dengan menyelesaikan tantangan-tantangan ini, proyek ini menjadi lebih kuat, aman, dan memiliki alur kerja pengembangan yang profesional.

### Link Aplikasi
Untuk mengakses aplikasi database mahasiswa ada pada link berikut
https://datasiswa-76068092363.asia-southeast2.run.app/

## Link Dokumentasi
Untuk Dokumentasi yang lebih Lengkap, dapat dilihat melalui link berikut:
https://intip.in/DokumentasiKelompok11
