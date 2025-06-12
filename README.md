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
    * Buat sebuah database MySQL baru di server lokal Anda (misalnya, `Data_siswa_dev`).
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