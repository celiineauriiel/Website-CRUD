<?php
// File: tests/AuthenticationTest.php (SUDAH DIPERBAIKI)

namespace Tests;

class AuthenticationTest extends DatabaseTestCase
{
    /**
     * @test
     */
    public function registrasiBerhasilUntukUserBaru()
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
    public function registrasiGagalJikaUsernameSudahAda()
    {
        // Siapkan user yang sudah ada
        ob_start(); // Tangkap output dari registrasi pertama
        registrasi([
            "username" => "userlama",
            "password" => "passlama",
            "password2" => "passlama"
        ]);
        ob_end_clean();

        // Coba registrasi lagi dengan username yang sama
        $_POST = [
            "username" => "userlama",
            "password" => "password123",
            "password2" => "password123"
        ];
        
        // Tangkap dan buang output dari pemanggilan kedua
        ob_start();
        $hasil = registrasi($_POST);
        ob_end_clean();

        $this->assertFalse($hasil, "Fungsi registrasi seharusnya mengembalikan false jika username sudah ada.");
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function loginBerhasilDenganKredensialYangBenar()
    {
        // 1. Daftarkan user dulu
        $username = 'userlogin';
        $password = 'passlogin';
        mysqli_query(self::$koneksi_test, "INSERT INTO user (username, password) VALUES ('$username', MD5('$password'))");

        // 2. Simulasikan login
        $_POST['login'] = true;
        $_POST['username'] = $username;
        $_POST['password'] = $password;

        // 3. Panggil file login.php untuk memproses logika
        ob_start();
        include __DIR__ . '/../login.php';
        ob_end_clean();

        // 4. Aseri bahwa session 'login' telah di-set
        $this->assertTrue($_SESSION['login']);
        $this->assertEquals($username, $_SESSION['username']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function logoutBerhasilMenghapusSession()
    {
        // 1. Simulasikan kondisi login
        session_start();
        $_SESSION['login'] = true;
        $_SESSION['username'] = 'user_yang_akan_logout';

        $this->assertTrue(isset($_SESSION['login']));

        // 2. Panggil file logout.php
        ob_start();
        include __DIR__ . '/../logout.php';
        ob_end_clean();

        // 3. Aseri bahwa session sudah tidak ada lagi
        $this->assertFalse(isset($_SESSION['login']));
        $this->assertEmpty($_SESSION);
    }
}