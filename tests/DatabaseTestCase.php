<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

// Memuat file function.php agar fungsinya bisa diakses
require_once __DIR__ . '/../function.php';

class DatabaseTestCase extends TestCase
{
    protected static $koneksi_test;
    protected static $isInitialized = false;

    public static function setUpBeforeClass(): void
    {
        // Koneksi ke database tes HANYA SEKALI per kelas tes
        if (!self::$isInitialized) {
            self::$koneksi_test = mysqli_connect(
                $_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'],
                $_ENV['DB_NAME'], $_ENV['DB_PORT']
            );

            if (!self::$koneksi_test) {
                die("Koneksi ke database tes gagal: " . mysqli_connect_error());
            }
            self::$isInitialized = true;
        }
    }

    protected function setUp(): void
    {
        // Method ini berjalan SEBELUM setiap tes dieksekusi.
        // Kita ganti variabel global $koneksi dengan koneksi tes kita
        global $koneksi;
        $koneksi = self::$koneksi_test;

        // Pastikan skema tabel sudah ada di database tes
        $this->createSchemaIfNeeded($koneksi);

        // Kosongkan tabel sebelum setiap tes untuk menghindari data sisa dari tes sebelumnya
        mysqli_query($koneksi, "TRUNCATE TABLE user");
        mysqli_query($koneksi, "TRUNCATE TABLE siswa");
    }

    private function createSchemaIfNeeded($koneksi)
    {
        // Skrip SQL sederhana untuk membuat tabel jika belum ada
        $sql = "
            CREATE TABLE IF NOT EXISTS `siswa` (
              `nis` varchar(50) NOT NULL PRIMARY KEY, `nama` varchar(255) NOT NULL,
              `tmpt_Lahir` varchar(50) NOT NULL, `tgl_Lahir` date NOT NULL,
              `jekel` enum('Laki - Laki','Perempuan') NOT NULL,
              `jurusan` enum('Teknik Listrik','Teknik Komputer dan Jaringan','Multimedia','Rekayasa Perangkat Lunak','Geomatika','Mesin') NOT NULL,
              `ipk` FLOAT(4,2) NOT NULL, `jalur_masuk` VARCHAR(50) NOT NULL,
              `email` varchar(255) NOT NULL, `gambar` varchar(255) NOT NULL, `alamat` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `user` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `username` varchar(50) NOT NULL UNIQUE, `password` varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        mysqli_multi_query($koneksi, $sql);
        // Membersihkan hasil multi-query
        while (mysqli_next_result($koneksi)) {;}
    }

    public static function tearDownAfterClass(): void
    {
        // Tutup koneksi setelah semua tes di kelas ini selesai
        if (self::$koneksi_test) {
            mysqli_close(self::$koneksi_test);
            self::$isInitialized = false;
        }
    }
}