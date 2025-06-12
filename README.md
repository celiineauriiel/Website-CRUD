# Sistem Admin Data Mahasiswa (Aplikasi CRUD dengan CI/CD & IaC)

Aplikasi web PHP sederhana untuk melakukan operasi Create, Read, Update, Delete (CRUD) pada data mahasiswa. Proyek ini dilengkapi dengan pipeline CI/CD otomatis menggunakan GitHub Actions, manajemen infrastruktur sebagai kode (IaC) dengan Terraform, migrasi database dengan Phinx, dan pengujian unit dengan PHPUnit, dideploy ke Google Cloud Platform.

## Deskripsi Proyek

Aplikasi ini memungkinkan administrator untuk mengelola data mahasiswa, termasuk informasi pribadi, akademik, dan nilai. Fitur utama meliputi autentikasi pengguna, manajemen data mahasiswa (tambah, lihat, ubah, hapus), ekspor data, dan halaman dashboard untuk melihat ringkasan nilai.

## Fitur Utama

* Autentikasi Pengguna (Login & Registrasi)
* Manajemen Data Mahasiswa (CRUD)
* Input dan Manajemen Nilai, IPK, dan Jalur Masuk Mahasiswa
* Upload Gambar Profil Mahasiswa
* Pencarian dan Tampilan Detail Mahasiswa
* Halaman Dashboard untuk Melihat Nilai Mahasiswa
* Ekspor Data Mahasiswa ke format XLS dan CSV
* Desain Responsif dengan Bootstrap

## Teknologi & Tools yang Digunakan

* **Backend:** PHP (prosedural dengan `mysqli`)
* **Database:** MySQL (dikelola oleh Google Cloud SQL)
* **Frontend:** HTML, CSS, JavaScript
* **Containerization:** Docker, `Dockerfile`
* **CI/CD (Continuous Integration/Continuous Deployment):**
    * GitHub Actions (`.github/workflows/main.yml`)
* **Cloud Platform:** Google Cloud Platform (GCP)
    * **Google Cloud Run:** Untuk hosting aplikasi ter-container.
    * **Google Cloud SQL:** Untuk layanan database MySQL terkelola.
    * **Google Artifact Registry:** Untuk penyimpanan *image Docker*.
    * **Google Cloud Monitoring:** Untuk logging dan metrik dasar.
* **Infrastructure as Code (IaC):**
    * Terraform (untuk memprovisikan resource GCP seperti Cloud SQL, Artifact Registry, Cloud Run Service).
* **Unit Testing:**
    * PHPUnit (untuk pengujian unit kode PHP).
* **Manajemen Dependensi PHP:**
    * Composer
* **IDE (Lingkungan Pengembangan Lokal):**
    * Visual Studio Code (VS Code)
* **Monitoring:**
    * GCP Monitoring, Prometheus dan Grafana

## Penyiapan Proyek & Instalasi (Pengembangan Lokal)

Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan pengembangan lokal Anda:

1.  **Prasyarat:**
    * Git
    * PHP (versi ^8.2 atau sesuai `composer.json`)
    * Composer
    * Docker Desktop (atau Docker Engine untuk Linux)
    * Terraform CLI
    * Google Cloud SDK (`gcloud` CLI) (opsional untuk pengembangan lokal jika tidak berinteraksi langsung dengan GCP dari lokal)
    * Server database MySQL lokal (misalnya dari XAMPP, MAMP, atau jalankan MySQL di Docker).

2.  **Clone Repositori:**
    ```bash
    git clone [https://github.com/](https://github.com/)[USERNAME_ANDA]/[NAMA_REPOSITORI_ANDA].git
    cd [NAMA_REPOSITORI_ANDA]
    ```

3.  **Instal Dependensi PHP:**
    ```bash
    composer install
    ```

4.  **Konfigurasi Database Lokal:**
    * Buat database MySQL baru di server lokal Anda (misalnya, bernama `Data_siswa_dev`).
    * Edit file `phinx.php`. Sesuaikan konfigurasi untuk *environment* `development` agar menunjuk ke database lokal Anda (host, nama database, user, password).
    * (Jika belum dilakukan) Modifikasi bagian koneksi database di `function.php` agar memiliki *fallback* ke koneksi database lokal jika variabel lingkungan untuk cloud tidak ditemukan (seperti contoh yang sudah kita diskusikan).

5.  **Jalankan Migrasi Database Lokal:**
    Perintah ini akan membuat tabel-tabel (`siswa`, `user`, `phinxlog`) di database lokal Anda berdasarkan file migrasi di `db/migrations/`.
    ```bash
    vendor/bin/phinx migrate -e development
    ```
    Atau jika Anda sudah mengatur skrip di `composer.json`:
    ```bash
    composer phinx migrate -e development
    ```

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
    Pastikan Anda sudah membuat Service Account untuk Terraform, mengunduh kunci JSON-nya, dan mengatur variabel lingkungan:
    ```bash
    export GOOGLE_APPLICATION_CREDENTIALS="/path/ke/kunci-sa-terraform.json"
    # Atau set secara permanen di sistem Anda
    ```

2.  **Navigasi ke Direktori Terraform:**
    ```bash
    cd terraform
    ```

3.  **Inisialisasi Terraform:**
    (Hanya perlu dijalankan sekali per direktori atau jika ada perubahan provider)
    ```bash
    terraform init
    ```

4.  **Rencanakan Perubahan Infrastruktur:**
    (Ganti placeholder dengan nilai Anda)
    ```bash
    terraform plan -var="gcp_project_id=[ID_PROYEK_GCP_ANDA]" -var="db_password=[PASSWORD_DB_RAHASIA_UNTUK_APP_USER]"
    ```

5.  **Terapkan Perubahan Infrastruktur:**
    (Ketik `yes` saat diminta konfirmasi)
    ```bash
    terraform apply -var="gcp_project_id=[ID_PROYEK_GCP_ANDA]" -var="db_password=[PASSWORD_DB_RAHASIA_UNTUK_APP_USER]"
    ```
    * Ini akan membuat (atau memperbarui) resource seperti:
        * Instance Google Cloud SQL.
        * Database dan user di dalam instance Cloud SQL.
        * Repositori Google Artifact Registry.
        * Definisi dasar layanan Google Cloud Run.

## Alur Kerja CI/CD (GitHub Actions)

Pipeline CI/CD diatur dalam file `.github/workflows/main.yml`.

* **Pemicu:** Otomatis berjalan setiap kali ada `git push` ke *branch* `main`.
* **Langkah Utama Pipeline:**
    1.  **Checkout Kode:** Kode terbaru diunduh.
    2.  **Setup PHP & Composer:** Lingkungan PHP disiapkan, dependensi diinstal (`composer install`).
    3.  **Jalankan PHPUnit Tests:** Tes unit dijalankan (`composer test`). Pipeline akan gagal jika ada tes yang tidak lolos.
    4.  **(Opsional) SonarQube/SonarCloud Analysis:** Jika dikonfigurasi, analisis kualitas kode dijalankan.
    5.  **Autentikasi ke GCP:** Menggunakan Service Account khusus CI/CD via GitHub Secrets.
    6.  **Setup Cloud SQL Auth Proxy:** Untuk koneksi aman ke Cloud SQL dari *runner* GitHub Actions.
    7.  **Jalankan Migrasi Database (Phinx):** `php vendor/bin/phinx migrate -e production` dijalankan untuk menerapkan perubahan skema database ke Google Cloud SQL. Pipeline akan gagal jika migrasi tidak berhasil.
    8.  **Build *Image Docker*:** Aplikasi PHP dikemas menjadi *image Docker* menggunakan `Dockerfile`.
    9.  **Push *Image Docker*:** *Image* diunggah ke Google Artifact Registry.
    10. **Deploy ke Google Cloud Run:** Layanan Cloud Run diperbarui dengan *image Docker* terbaru dan variabel lingkungan yang sesuai (termasuk kredensial database).

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

Setelah pipeline CI/CD berhasil, aplikasi akan dapat diakses melalui URL yang disediakan oleh layanan Google Cloud Run Anda. Anda bisa melihat URL ini di detail layanan Cloud Run di GCP Console atau dari output Terraform jika dikonfigurasi.
