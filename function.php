<?php
// Aktifkan error reporting untuk debugging sementara. HAPUS ini di PRODUKSI!
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi Database
$koneksi = mysqli_connect("localhost", "root", "", "Data_siswa", 3306);

// Cek koneksi
if (mysqli_connect_errno()) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Database Gagal!',
                    text: 'Terjadi masalah saat terhubung ke database: " . mysqli_connect_error() . "',
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            });
          </script>";
    exit(); // Hentikan eksekusi jika koneksi gagal
}


// membuat fungsi query dalam bentuk array
function query($query)
{
    // Koneksi database
    global $koneksi;

    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        error_log("Query Gagal: " . mysqli_error($koneksi) . " Query: " . $query);
        return [];
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_free_result($result);

    return $rows;
}

// Membuat fungsi tambah
function tambah($data)
{
    global $koneksi;

    $nis = htmlspecialchars($data['nis']);
    $nama = htmlspecialchars($data['nama']);
    $tmpt_Lahir = htmlspecialchars($data['tmpt_Lahir']);
    $tgl_Lahir = $data['tgl_Lahir'];
    $jekel = $data['jekel'];
    $jurusan = $data['jurusan'];
    $email = htmlspecialchars($data['email']);
    $alamat = htmlspecialchars($data['alamat']);

    // Validasi NIS duplikat
    $check_nis = mysqli_query($koneksi, "SELECT nis FROM siswa WHERE nis = '$nis'");
    if (mysqli_fetch_assoc($check_nis)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'NIS Sudah Terdaftar!',
                        text: 'NIS yang Anda masukkan sudah ada di database. Harap gunakan NIS lain.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return 0; // Mengembalikan 0 karena NIS duplikat
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Email Salah!',
                        text: 'Alamat email harus dalam format yang benar (contoh: user@example.com).',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return 0; // Mengembalikan 0 karena format email salah
    }

    // Panggil fungsi upload. FUNGSI UPLOAD SEKARANG MENGATASI KASUS TANPA GAMBAR
    $gambar = upload();

    if ($gambar === false) {
        return 0; // Mengembalikan 0 agar fungsi tambah() gagal
    }

    $sql = "INSERT INTO siswa (nis, nama, tmpt_Lahir, tgl_Lahir, jekel, jurusan, email, gambar, alamat) VALUES ('$nis', '$nama', '$tmpt_Lahir', '$tgl_Lahir', '$jekel', '$jurusan', '$email', '$gambar', '$alamat')";

    mysqli_query($koneksi, $sql);

    if (mysqli_error($koneksi)) {
        error_log("Insert Query Gagal: " . mysqli_error($koneksi) . " Query: " . $sql);
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menambahkan Data!',
                        text: 'Terjadi kesalahan database: " . addslashes(mysqli_error($koneksi)) . "',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
              </script>";
        return 0; // Mengembalikan 0 jika ada error database
    }

    return mysqli_affected_rows($koneksi);
}

// Membuat fungsi hapus
function hapus($nis)
{
    global $koneksi;

    mysqli_query($koneksi, "DELETE FROM siswa WHERE nis = '$nis'");

    if (mysqli_error($koneksi)) {
        error_log("Delete Query Gagal: " . mysqli_error($koneksi) . " NIS: " . $nis);
        return 0; // Mengembalikan 0 jika ada error database
    }
    
    return mysqli_affected_rows($koneksi);
}

// Membuat fungsi ubah
function ubah($data)
{
    global $koneksi;

    $nis = $data['nis']; // NIS tidak diubah
    $nama = htmlspecialchars($data['nama']);
    $tmpt_Lahir = htmlspecialchars($data['tmpt_Lahir']);
    $tgl_Lahir = $data['tgl_Lahir'];
    $jekel = $data['jekel'];
    $jurusan = $data['jurusan'];
    $email = htmlspecialchars($data['email']);
    $alamat = htmlspecialchars($data['alamat']);

    $gambarLama = htmlspecialchars($data['gambarLama']);

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Email Salah!',
                        text: 'Alamat email harus dalam format yang benar (contoh: user@example.com).',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return 0; // Mengembalikan 0 karena format email salah
    }

    // Cek apakah ada file gambar baru yang diunggah
    if ($_FILES['gambar']['error'] === 4) {
        $gambar = $gambarLama; // Gunakan gambar lama
    } else {
        $gambar = upload();
        if ($gambar === false) { // Jika upload gagal (misal: bukan gambar, ukuran terlalu besar)
            return 0; // Mengembalikan 0 agar fungsi ubah() gagal
        }
    }

    $sql = "UPDATE siswa SET 
                nama = '$nama', 
                tmpt_Lahir = '$tmpt_Lahir', 
                tgl_Lahir = '$tgl_Lahir', 
                jekel = '$jekel', 
                jurusan = '$jurusan', 
                email = '$email', 
                gambar = '$gambar', 
                alamat = '$alamat' 
            WHERE nis = '$nis'";

    mysqli_query($koneksi, $sql);

    if (mysqli_error($koneksi)) {
        error_log("Update Query Gagal: " . mysqli_error($koneksi) . " Query: " . $sql);
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengubah Data!',
                        text: 'Terjadi kesalahan database: " . addslashes(mysqli_error($koneksi)) . "',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
              </script>";
        return 0; // Mengembalikan 0 jika ada error database
    }

    return mysqli_affected_rows($koneksi);
}

// Membuat fungsi upload gambar
function upload()
{
    if ($_FILES['gambar']['error'] === 4) {
        return '';
    }

    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    $extValid = ['jpg', 'jpeg', 'png'];
    $ext = explode('.', $namaFile);
    $ext = strtolower(end($ext));

    if (!in_array($ext, $extValid)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Salah!',
                        text: 'Yang Anda upload bukanlah gambar (jpg, jpeg, png)!',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return false;
    }

    if ($ukuranFile > 3000000) { // 3MB
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran File Terlalu Besar!',
                        text: 'Ukuran gambar Anda terlalu besar! (Max 3MB)',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return false;
    }

    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ext;

    if (!move_uploaded_file($tmpName, 'img/' . $namaFileBaru)) {
        error_log("Gagal memindahkan file: " . $tmpName . " ke " . 'img/' . $namaFileBaru);
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Unggah Gambar!',
                        text: 'Terjadi masalah saat menyimpan gambar. Coba lagi!',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return false;
    }

    return $namaFileBaru;
}

function registrasi($data)
{
    global $koneksi;

    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($koneksi, $data["password"]);
    $password2 = mysqli_real_escape_string($koneksi, $data["password2"]);

    // cek username sudah ada atau belum
    $result = mysqli_query($koneksi, "SELECT username FROM user WHERE username = '$username'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Username Sudah Terdaftar!',
                        text: 'Silakan gunakan username lain.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return false;
    }

    // cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Konfirmasi Password Tidak Sesuai!',
                        text: 'Mohon ulangi password Anda.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return false;
    }

    // REVISI KRUSIAL: TIDAK MENGHASH PASSWORD DENGAN password_hash() lagi
    // Kembali ke menyimpan password plain (atau MD5) jika itu yang Anda inginkan
    // Karena Anda bilang "kesimpan di database seusai yang diinput aja"
    // Saya akan menggunakan MD5 seperti kode login.php lama Anda
    $password = md5($password); 

    // tambahkan user baru ke database
    mysqli_query($koneksi, "INSERT INTO user VALUES('', '$username', '$password')");

    // Cek error query jika ada untuk debugging
    if (mysqli_error($koneksi)) {
        error_log("Registrasi Query Gagal: " . mysqli_error($koneksi));
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Registrasi!',
                        text: 'Terjadi kesalahan database: " . addslashes(mysqli_error($koneksi)) . "',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
              </script>";
        return 0; // Mengembalikan 0 jika ada error database
    }

    return mysqli_affected_rows($koneksi);
}