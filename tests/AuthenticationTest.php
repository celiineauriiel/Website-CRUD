<?php
// File: tests/AuthenticationTest.php
// Deskripsi: Menguji semua fungsionalitas terkait autentikasi: registrasi, login, dan logout.

namespace Tests;

// 'require_once' memastikan file ini hanya dimuat sekali saja.
require_once __DIR__ . '/../function.php';

class AuthenticationTest extends DatabaseTestCase
{
    /**
     * @test
     */
    public function registrasiBerhasilUntukUserBaru(): void
    {
        $_POST = [
            "username" => "userbaru",
            "password" => "password123",
            "password2" => "password123"
        ];
        
        // Menangkap dan membuang output (echo) dari fungsi
        ob_start();
        $hasil = registrasi($_POST);
        ob_end_clean();

        $this->assertEquals(1, $hasil, "Fungsi registrasi seharusnya mengembalikan 1 jika berhasil.");

        // Verifikasi langsung ke database tes
        $result = mysqli_query(self::$koneksi_test, "SELECT * FROM user WHERE username = 'userbaru'");
        $this->assertEquals(1, mysqli_num_rows($result));
        $user_data = mysqli_fetch_assoc($result);
        $this->assertEquals(md5('password123'), $user_data['password']);
    }

    /**
     * @test
     */
    public function registrasiGagalJikaUsernameSudahAda(): void
    {
        ob_start();
        registrasi([ "username" => "userlama", "password" => "passlama", "password2" => "passlama" ]);
        ob_end_clean();

        $_POST = [
            "username" => "userlama", "password" => "password123", "password2" => "password123"
        ];
        
        ob_start();
        $hasil = registrasi($_POST);
        ob_end_clean();

        $this->assertFalse((bool)$hasil, "Fungsi registrasi seharusnya mengembalikan false/0 jika username sudah ada.");
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function loginBerhasilDenganKredensialYangBenar(): void
    {
        $username = 'userlogin';
        $password = 'passlogin';

        // Koneksi DB untuk proses terpisah
        $koneksi = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'], $_ENV['DB_PORT']);
        $this->assertNotFalse($koneksi, "Koneksi DB untuk proses terpisah gagal.");
        
        // Buat user di DB tes
        mysqli_query($koneksi, "INSERT INTO user (username, password) VALUES ('$username', MD5('$password'))");
        
        // Pastikan sesi dimulai di sini
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Simulasikan login
        $_POST['login'] = true;
        $_POST['username'] = $username;
        $_POST['password'] = $password;

        // Panggil file login.php untuk memproses logika
        ob_start();
        processLogin($koneksi, $_POST['username'], $_POST['password']);
        ob_end_clean();

        // Aseri bahwa session 'login' telah di-set
        $this->assertTrue(isset($_SESSION['login']) && $_SESSION['login'] === true);
        $this->assertEquals($username, $_SESSION['username']);
    }

}
