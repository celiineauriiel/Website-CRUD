# Sistem Admin Data Mahasiswa (Aplikasi CRUD dengan CI/CD & IaC)

Aplikasi web PHP sederhana untuk melakukan operasi Create, Read, Update, Delete (CRUD) pada data mahasiswa. Proyek ini dilengkapi dengan pipeline CI/CD otomatis menggunakan GitHub Actions, manajemen infrastruktur sebagai kode (IaC) dengan Terraform, dan pengujian unit dengan PHPUnit, dideploy ke Google Cloud Platform.

## Deskripsi Proyek

Aplikasi ini memungkinkan administrator untuk mengelola data mahasiswa, termasuk informasi pribadi, akademik, dan nilai. Fitur utama meliputi autentikasi pengguna, manajemen data mahasiswa (tambah, lihat, ubah, hapus), ekspor data, dan halaman dashboard untuk melihat ringkasan nilai.

## Fitur Utama

* Autentikasi Pengguna (Login & Registrasi)
* Manajemen Data Mahasiswa (CRUD)
* Input dan Manajemen Nilai, IPK, dan Jalur Masuk Mahasiswa
* Upload Gambar Profil Mahasiswa
* Pencarian dan Tampilan Detail Mahasiswa
* Halaman Dashboard untuk Melihat Ringkasan Data Mahasiswa
* Ekspor Data Mahasiswa ke format XLS dan CSV
* Desain Responsif dengan Bootstrap

## Teknologi & Tools yang Digunakan

* **Backend:** PHP (prosedural dengan `mysqli`)
* **Database:** MySQL (dikelola oleh Google Cloud SQL)
* **Frontend:** HTML, CSS, JavaScript
    * Framework & Library: Bootstrap 5, jQuery, SweetAlert2, GSAP, DataTables, AOS (sebagian besar via CDN)
* **Containerization:** Docker, `Dockerfile`
* **CI/CD (Continuous Integration/Continuous Deployment):**
    * GitHub Actions (`.github/workflows/main.yml`)
* **Cloud Platform:** Google Cloud Platform (GCP)
    * **Google Cloud Run:** Untuk hosting aplikasi ter-container.
    * **Google Cloud SQL:** Untuk layanan database terkelola.
    * **Google Artifact Registry:** Untuk penyimpanan *image Docker*.
    * **Google Cloud Monitoring:** Untuk logging dan metrik dasar.
* **Infrastructure as Code (IaC):**
    * Terraform (untuk memprovisikan resource GCP).
* **Unit Testing:**
    * PHPUnit (untuk pengujian unit kode PHP).
* **Manajemen Dependensi PHP:**
    * Composer
* **IDE (Lingkungan Pengembangan Lokal):**
    * Visual Studio Code (VS Code)

## Penyiapan Proyek & Instalasi (Pengembangan Lokal)

Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan pengembangan lokal Anda:

1.  **Prasyarat:**
    * Git
    * PHP (versi ^8.0 atau sesuai `composer.json`)
    * Composer
    * Docker Desktop (atau Docker Engine untuk Linux)
    * Terraform CLI
    * Server database MySQL lokal (misalnya dari XAMPP, MAMP, atau jalankan MySQL di Docker).

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
    * Buat sebuah database MySQL baru di server lokal Anda (misalnya, bernama `Data_siswa_dev`).
    * Pastikan file `function.php` Anda memiliki logika *fallback* untuk terhubung ke database lokal jika variabel lingkungan untuk cloud tidak ditemukan.

5.  **Impor Skema dan Data Database Lokal:**
    Perintah ini akan membuat tabel (`siswa`, `user`) dan mengisi data awal di database lokal Anda menggunakan file `data_siswa.sql`. Jalankan dari terminal di direktori utama proyek:
    ```bash
    mysql -u [USERNAME_DB_ANDA] -p [NAMA_DATABASE_ANDA] < db/data_siswa.sql
    ```
    Anda akan diminta untuk memasukkan password database Anda.

6.  **Jalankan Aplikasi Secara Lokal:**
    * Gunakan server web lokal Anda (XAMPP, MAMP, WAMP) dan arahkan *document root* ke direktori proyek.
    * Atau gunakan server web bawaan PHP (dari direktori utama proyek):
        ```bash
        php -S localhost:8000
        ```
    * Buka `http://localhost:8000` di browser Anda.

## Penyiapan Infrastruktur dengan Terraform (Satu Kali atau untuk Lingkungan Baru)

Terraform digunakan untuk memprovisikan infrastruktur inti di GCP. File konfigurasi Terraform ada di direktori `terraform/`.

1.  **Autentikasi ke GCP untuk Terraform:**
    Pastikan Anda sudah membuat Service Account untuk Terraform, mengunduh kunci JSON-nya, dan mengatur variabel lingkungan.

2.  **Navigasi ke Direktori Terraform:**
    ```bash
    cd terraform
    ```

3.  **Inisialisasi Terraform:**
    ```bash
    terraform init
    ```

4.  **Rencanakan dan Terapkan Perubahan Infrastruktur:**
    ```bash
    terraform plan -var="gcp_project_id=[ID_PROYEK_GCP_ANDA]"
    terraform apply -var="gcp_project_id=[ID_PROYEK_GCP_ANDA]"
    ```
    Ini akan membuat (atau memperbarui) resource seperti instance Cloud SQL, repositori Artifact Registry, dan layanan Cloud Run.

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

## Menjalankan Tes (PHPUnit)

Tes unit disimpan di direktori `tests/`.

* **Menjalankan Semua Tes (Lokal):**
    ```bash
    composer test
    ```
    Atau:
    ```bash
    ./vendor/bin/phpunit
    ```

## Aplikasi yang Sudah Di-deploy

Setelah pipeline CI/CD berhasil, aplikasi akan dapat diakses melalui URL yang disediakan oleh layanan Google Cloud Run Anda.
