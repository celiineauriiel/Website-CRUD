<?php
// File: tests/DatabaseTestCase.php (Diperbaiki)

namespace Tests;

use PHPUnit\Framework\TestCase;

class DatabaseTestCase extends TestCase
{
    protected static ?\mysqli $koneksi_test = null;

    public static function setUpBeforeClass(): void
    {
        if (self::$koneksi_test === null) {
            self::$koneksi_test = self::get_db_connection();
            if (!self::$koneksi_test) {
                die("Koneksi ke database tes gagal: " . mysqli_connect_error());
            }
        }
    }

    public static function get_db_connection(): ?\mysqli
    {
        // Ambil kredensial dari environment variable yang diset oleh CI/CD pipeline
        $db_host = $_ENV['DB_HOST'] ?? 'localhost';
        $db_user = $_ENV['DB_USER'] ?? 'root';
        $db_pass = $_ENV['DB_PASS'] ?? '';
        $db_name = $_ENV['DB_NAME'] ?? 'data_siswa_test';
        $db_port = $_ENV['DB_PORT'] ?? 3306;

        $koneksi = new \mysqli($db_host, $db_user, $db_pass, $db_name, (int)$db_port);
        if ($koneksi->connect_error) {
            die("Koneksi gagal: " . $koneksi->connect_error);
        }
        return $koneksi;
    }

    protected function setUp(): void
    {
        // Ganti koneksi global dengan koneksi tes
        global $koneksi;
        $koneksi = self::$koneksi_test;

        // Pastikan skema tabel sudah ada
        $this->createSchemaIfNeeded($koneksi);
        
        // Kosongkan tabel sebelum setiap tes
        mysqli_query($koneksi, "TRUNCATE TABLE user");
        mysqli_query($koneksi, "TRUNCATE TABLE siswa");
    }

    private function createSchemaIfNeeded(\mysqli $koneksi): void
    {
        // PERBAIKAN: Mengubah kolom 'jurusan' menjadi VARCHAR agar lebih fleksibel
        // dan tidak terikat pada daftar ENUM yang lama.
        $sql = "
            CREATE TABLE IF NOT EXISTS siswa (
              nis varchar(50) NOT NULL PRIMARY KEY,
              nama varchar(255) NOT NULL,
              tmpt_Lahir varchar(50) NOT NULL,
              tgl_Lahir date NOT NULL,
              jekel enum('Laki - Laki','Perempuan') NOT NULL,
              jurusan VARCHAR(100) NOT NULL, 
              ipk FLOAT(4,2) NOT NULL,
              jalur_masuk VARCHAR(50) NOT NULL,
              email varchar(255) NOT NULL,
              gambar varchar(255) NULL,
              alamat text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS user (
              id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              username varchar(50) NOT NULL UNIQUE,
              password varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        mysqli_multi_query($koneksi, $sql);
        // Membersihkan hasil query ganda
        while (mysqli_more_results($koneksi) && mysqli_next_result($koneksi)) {;}
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$koneksi_test) {
            mysqli_close(self::$koneksi_test);
            self::$koneksi_test = null;
        }
    }
}