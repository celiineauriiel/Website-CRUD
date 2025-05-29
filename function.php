<?php
// Aktifkan error reporting untuk debugging. Di produksi, ini bisa diatur di server.
error_reporting(E_ALL);
ini_set('display_errors', 1); // Mungkin ingin disetel ke 0 di Cloud Run produksi nanti dan mengandalkan log.

// --- AWAL MODIFIKASI UNTUK CLOUD ---
// Ambil kredensial database dari Environment Variables yang di-set oleh Cloud Run
$db_host_env = getenv('DB_HOST'); // Ini akan berisi Instance Connection Name dari Cloud SQL
$db_user_env = getenv('DB_USER');
$db_pass_env = getenv('DB_PASS');
$db_name_env = getenv('DB_NAME');

$koneksi = null;

// Cek apakah variabel lingkungan untuk koneksi Cloud SQL tersedia
if ($db_host_env && $db_user_env && $db_name_env) {
    // Untuk koneksi ke Cloud SQL dari Cloud Run, biasanya menggunakan Unix Socket.
    // Path socket: /cloudsql/INSTANCE_CONNECTION_NAME
    // mysqli_connect() menerima null untuk host jika menggunakan socket.
    $socket_path = '/cloudsql/' . $db_host_env;
    $koneksi = mysqli_connect(null, $db_user_env, $db_pass_env, $db_name_env, null, $socket_path);
} else {
    // Fallback untuk lingkungan lokal (jika environment variables tidak di-set)
    // Anda bisa menyesuaikan ini jika nama variabel lokal berbeda atau jika Anda ingin error jika variabel cloud tidak ada.
    // Untuk sekarang, kita asumsikan jika variabel cloud tidak ada, kita coba koneksi lokal standar.
    $koneksi = mysqli_connect("localhost", "root", "", "Data_siswa", 3306);
}

// Cek koneksi
if (mysqli_connect_errno()) {
    // Di lingkungan cloud, menampilkan error langsung ke browser mungkin bukan praktik terbaik.
    // Logging error lebih diutamakan.
    error_log("Koneksi Database Gagal: " . mysqli_connect_error() . " (Host Env: " . $db_host_env . ")");

    // Memberikan pesan yang lebih umum kepada pengguna atau menggunakan SweetAlert jika memungkinkan.
    // Karena ini function.php yang mungkin di-include sebelum HTML, SweetAlert mungkin tidak langsung jalan.
    // Pertimbangkan untuk menangani ini di halaman yang memanggil, atau setidaknya buat pesan error yang aman.
    // Untuk sekarang, kita bisa hentikan eksekusi dengan pesan sederhana.
    die("Tidak dapat terhubung ke database. Mohon coba beberapa saat lagi atau hubungi administrator.");
    // Jika Anda ingin tetap menggunakan SweetAlert, pastikan skripnya sudah dimuat.
    // Contoh SweetAlert (mungkin tidak ideal di sini karena bisa jadi dipanggil sebelum HTML):
    /*
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Database Gagal!',
                        text: 'Terjadi masalah saat terhubung ke database.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                } else {
                    alert('Koneksi Database Gagal! Terjadi masalah saat terhubung ke database.');
                }
            });
          </script>";
    */
    // exit(); // Hentikan eksekusi jika koneksi gagal
}
// --- AKHIR MODIFIKASI UNTUK CLOUD ---

// membuat fungsi query dalam bentuk array
function query($query)
{
    // Koneksi database
    global $koneksi;

    // ... sisa kode function.php Anda (query, tambah, hapus, ubah, upload, registrasi) tidak perlu diubah ...
    // ... kecuali jika ada path file yang hardcoded yang perlu disesuaikan untuk lingkungan Docker/Cloud Run ...
    // ... (misalnya, path untuk upload gambar jika 'img/' tidak relatif terhadap skrip yang berjalan) ...
    // ... Namun, untuk 'img/', jika struktur direktori di Docker sama, seharusnya tetap bekerja.
    // LANJUTKAN DENGAN SISA KODE ANDA DARI SINI
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
    // Kolom baru dari data_siswa.sql
    $ipk = isset($data['ipk']) ? floatval($data['ipk']) : 0.0; // Pastikan ini ada di form addData.php
    $jalur_masuk = isset($data['jalur_masuk']) ? htmlspecialchars($data['jalur_masuk']) : ''; // Pastikan ini ada di form addData.php
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

    // Sesuaikan query INSERT dengan kolom baru
    $sql = "INSERT INTO siswa (nis, nama, tmpt_Lahir, tgl_Lahir, jekel, jurusan, ipk, jalur_masuk, email, gambar, alamat) 
            VALUES ('$nis', '$nama', '$tmpt_Lahir', '$tgl_Lahir', '$jekel', '$jurusan', '$ipk', '$jalur_masuk', '$email', '$gambar', '$alamat')";


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
    // Kolom baru dari data_siswa.sql
    $ipk = isset($data['ipk']) ? floatval($data['ipk']) : 0.0; // Pastikan ini ada di form ubah.php
    $jalur_masuk = isset($data['jalur_masuk']) ? htmlspecialchars($data['jalur_masuk']) : ''; // Pastikan ini ada di form ubah.php
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

    // Sesuaikan query UPDATE dengan kolom baru
    $sql = "UPDATE siswa SET 
                nama = '$nama', 
                tmpt_Lahir = '$tmpt_Lahir', 
                tgl_Lahir = '$tgl_Lahir', 
                jekel = '$jekel', 
                jurusan = '$jurusan', 
                ipk = '$ipk', 
                jalur_masuk = '$jalur_masuk', 
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
    if ($_FILES['gambar']['error'] === 4) { // Tidak ada file yang diunggah
        return ''; // Kembalikan string kosong jika tidak ada gambar baru
    }

    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    $extValid = ['jpg', 'jpeg', 'png'];
    $ext = explode('.', $namaFile);
    $ext = strtolower(end($ext)); // Ambil ekstensi file

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
        return false; // Gagal upload
    }

    // Batas ukuran file (misalnya 3MB)
    if ($ukuranFile > 3000000) {
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
        return false; // Gagal upload
    }

    // Generate nama file baru yang unik untuk menghindari konflik nama
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ext;

    // Pindahkan file yang diupload ke direktori tujuan ('img/')
    // Pastikan direktori 'img/' ada dan writable oleh server web.
    // Dalam Docker, direktori ini akan ada di dalam container.
    if (!is_dir('img/')) {
        mkdir('img/', 0755, true); // Buat direktori jika belum ada
    }

    if (move_uploaded_file($tmpName, 'img/' . $namaFileBaru)) {
        return $namaFileBaru; // Berhasil upload, kembalikan nama file baru
    } else {
        // Gagal memindahkan file (mungkin masalah permission atau path)
        error_log("Gagal memindahkan file: " . $tmpName . " ke " . 'img/' . $namaFileBaru . " - Periksa permission dan path.");
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Unggah Gambar!',
                        text: 'Terjadi masalah saat menyimpan gambar. Coba lagi atau hubungi administrator.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return false; // Gagal upload
    }
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
?>