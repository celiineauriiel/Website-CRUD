<?php
// File: tests/DatabaseTestCase.php
// Deskripsi: Kelas dasar untuk semua tes yang membutuhkan database.
//            Mengelola koneksi dan pembersihan database tes secara otomatis.

namespace Tests;

use PHPUnit\Framework\TestCase;

class DatabaseTestCase extends TestCase
{
    protected static ?\mysqli $koneksi_test = null;

    // Method ini berjalan sekali sebelum semua tes di kelas ini dimulai.
    public static function setUpBeforeClass(): void
    {
        // Koneksi database untuk pengujian
        if (self::$koneksi_test === null) {
            self::$koneksi_test = self::get_db_connection(); // Fungsi get_db_connection() didefinisikan di sini

            if (!self::$koneksi_test) {
                die("Koneksi ke database tes gagal: " . mysqli_connect_error());
            }
        }
    }

    // Fungsi get_db_connection() untuk koneksi database pengujian
    public static function get_db_connection(): ?\mysqli
    {
        // Ambil kredensial database dari environment atau variabel lokal
        $db_host = $_ENV['DB_HOST'] ?? 'localhost'; // Default ke localhost jika tidak ada environment variable
        $db_user = $_ENV['DB_USER'] ?? 'root';
        $db_pass = $_ENV['DB_PASS'] ?? '';
        $db_name = $_ENV['DB_NAME'] ?? 'data_siswa_test'; // Nama database untuk pengujian
        $db_port = $_ENV['DB_PORT'] ?? '3306'; // Port default MySQL

        // Membuat koneksi ke database
        $koneksi = new \mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

        // Periksa apakah koneksi berhasil
        if ($koneksi->connect_error) {
            die("Koneksi gagal: " . $koneksi->connect_error);
        }

        return $koneksi;
    }

    // Method ini berjalan SEBELUM setiap tes ('test...') dieksekusi.
    protected function setUp(): void
    {
        // Ganti variabel global $koneksi dengan koneksi tes kita.
        global $koneksi;
        $koneksi = self::$koneksi_test;

        // Pastikan skema tabel sudah ada di database tes
        $this->createSchemaIfNeeded($koneksi);
        
        // Kosongkan tabel sebelum setiap tes untuk hasil yang konsisten
        mysqli_query($koneksi, "TRUNCATE TABLE user");
        mysqli_query($koneksi, "TRUNCATE TABLE siswa");
    }

    private function createSchemaIfNeeded(\mysqli $koneksi): void
    {
        // Skrip SQL untuk membuat tabel jika belum ada.
        $sql = "
            CREATE TABLE IF NOT EXISTS `siswa` (
              `nis` varchar(50) NOT NULL PRIMARY KEY, `nama` varchar(255) NOT NULL,
              `tmpt_Lahir` varchar(50) NOT NULL, `tgl_Lahir` date NOT NULL,
              `jekel` enum('Laki - Laki','Perempuan') NOT NULL,
              `jurusan` enum('Teknik Elektro','Teknik Biomedik','Teknik Komputer','Teknik Informatika','Sistem Informasi','Teknologi Informasi') NOT NULL,
              `ipk` FLOAT(4,2) NOT NULL,
              `jalur_masuk` VARCHAR(50) NOT NULL,
              `email` varchar(255) NOT NULL,
              `gambar` varchar(255) NULL, `alamat` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `user` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `username` varchar(50) NOT NULL UNIQUE,
              `password` varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        mysqli_multi_query($koneksi, $sql);
        while (mysqli_more_results($koneksi) && mysqli_next_result($koneksi)) {;}
    }

    // Method ini berjalan sekali setelah semua tes di kelas ini selesai.
    public static function tearDownAfterClass(): void
    {
        if (self::$koneksi_test) {
            mysqli_close(self::$koneksi_test);
            self::$koneksi_test = null;
        }
    }
}
