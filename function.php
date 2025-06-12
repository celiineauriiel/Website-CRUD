<?php
// MONITORING: Baris ini ditambahkan untuk memuat fungsi-fungsi monitoring kita.
require_once 'monitoring.php';

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
    $koneksi = mysqli_connect($db_host_env, $db_user_env, $db_pass_env, $db_name_env, 3306);
} else {
    // Fallback untuk lingkungan lokal (jika environment variables tidak di-set)
    // Menggunakan port 3309 seperti yang Anda sebutkan di kode lama (jika perlu)
    $koneksi = mysqli_connect("localhost", "root", "", "Data_siswa", 3306); 
}

// Cek koneksi
if (mysqli_connect_errno()) {
    // Di lingkungan cloud, menampilkan error langsung ke browser mungkin bukan praktik terbaik.
    // Logging error lebih diutamakan.
    error_log("Koneksi Database Gagal: " . mysqli_connect_error() . " (Host Env: " . $db_host_env . ")");

    // Memberikan pesan yang lebih umum kepada pengguna atau menggunakan SweetAlert jika memungkinkan.
    die("Tidak dapat terhubung ke database. Mohon coba beberapa saat lagi atau hubungi administrator.");
}
// --- AKHIR MODIFIKASI UNTUK CLOUD ---

// --- AWAL TAMBAHAN VALIDASI ---
// Array asosiatif untuk memetakan jurusan ke 4 digit awal NIS
// Ini bisa diatur dalam file konfigurasi terpisah jika daftar terlalu panjang
$jurusan_nis_prefixes = [
    'Teknik Elektro' => '5022',
    'Teknik Biomedik' => '5023',
    'Teknik Komputer' => '5024',
    'Teknik Informatika' => '5025',
    'Sistem Informasi' => '5026',
    'Teknologi Informasi' => '5027',
];
// --- AKHIR TAMBAHAN VALIDASI ---


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
    global $koneksi, $jurusan_nis_prefixes; // Akses array prefix jurusan

    $nis = htmlspecialchars($data['nis']);
    $nama = htmlspecialchars($data['nama']);
    $tmpt_Lahir = htmlspecialchars($data['tmpt_Lahir']);
    $tgl_Lahir = $data['tgl_Lahir'];
    $jekel = $data['jekel'];
    $jurusan = htmlspecialchars($data['jurusan']);
    $email = htmlspecialchars($data['email']);
    $alamat = htmlspecialchars($data['alamat']);
    // Pastikan nilai IPK dan jalur_masuk diambil dengan benar, sesuai input form
    $ipk = isset($data['ipk']) ? htmlspecialchars($data['ipk']) : ''; 
    $jalur_masuk = isset($data['jalur_masuk']) ? htmlspecialchars($data['jalur_masuk']) : '';

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

    // --- AWAL VALIDASI TAMBAHAN (dari kode lama) ---
    // Validasi NIS 10 digit dan hanya angka
    if (strlen($nis) !== 10 || !ctype_digit($nis)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format NIS Salah!',
                        text: 'NIS harus terdiri dari 10 digit angka.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
            </script>";
        return 0;
    }

    // Validasi 4 digit awal NIS sesuai jurusan
    if (array_key_exists($jurusan, $jurusan_nis_prefixes)) {
        $expected_prefix = $jurusan_nis_prefixes[$jurusan];
        $nis_prefix = substr($nis, 0, 4); // Ambil 4 digit pertama NIS
        if ($nis_prefix !== $expected_prefix) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'NIS Tidak Sesuai Jurusan!',
                            text: '4 digit awal NIS (\"" . htmlspecialchars($nis_prefix) . "\") tidak cocok dengan kode jurusan " . htmlspecialchars($jurusan) . " (harus \"" . htmlspecialchars($expected_prefix) . "\").',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    });
                </script>";
            return 0;
        }
    } else {
        // Jurusan tidak dikenal dalam array prefix
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jurusan Tidak Valid!',
                        text: 'Jurusan yang dipilih tidak dikenali atau tidak memiliki prefix NIS yang valid.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
            </script>";
        return 0;
    }

    // Validasi format email (sudah ada, kita biarkan)
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

    // Validasi IPK
    // Konversi IPK ke float untuk validasi numerik, lalu kembalikan ke string jika perlu untuk query
    $ipk_float = floatval($ipk); 
    if (!empty($ipk) && (!is_numeric($ipk) || $ipk_float < 0.00 || $ipk_float > 4.00)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format IPK Salah!',
                        text: 'IPK harus berupa angka antara 0.00 dan 4.00.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
            </script>";
        return 0;
    }

    // Validasi Jalur Masuk
    if (empty($jalur_masuk)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jalur Masuk Kosong!',
                        text: 'Mohon pilih jalur masuk mahasiswa.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
            </script>";
        return 0;
    }
    // --- AKHIR VALIDASI TAMBAHAN ---

    // Panggil fungsi upload. FUNGSI UPLOAD SEKARANG MENGATASI KASUS TANPA GAMBAR
    $gambar = upload();

    // Cek apakah upload gambar gagal. Jika gagal, kembalikan 0.
    if ($gambar === false) {
        return 0; 
    }

    // HATI-HATI: Pastikan urutan kolom di query ini cocok dengan urutan di database Anda.
    // Jika ada gambar (tidak false dan tidak string kosong), masukkan kolom gambar.
    if (!empty($gambar)) {
        $sql = "INSERT INTO siswa (nis, nama, tmpt_lahir, tgl_lahir, jekel, jurusan, ipk, jalur_masuk, email, gambar, alamat)
                VALUES ('$nis', '$nama', '$tmpt_Lahir', '$tgl_Lahir', '$jekel', '$jurusan', '$ipk_float', '$jalur_masuk', '$email', '$gambar', '$alamat')";
    } else {
        // Jika tidak ada gambar, jangan masukkan kolom gambar ke query
        $sql = "INSERT INTO siswa (nis, nama, tmpt_lahir, tgl_lahir, jekel, jurusan, ipk, jalur_masuk, email, alamat)
                VALUES ('$nis', '$nama', '$tmpt_Lahir', '$tgl_Lahir', '$jekel', '$jurusan', '$ipk_float', '$jalur_masuk', '$email', '$alamat')";
    }
    
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

    // MONITORING: Blok ini ditambahkan untuk mengirim metrik jika data berhasil ditambahkan.
    $affected_rows = mysqli_affected_rows($koneksi);
    if ($affected_rows > 0) {
        record_counter('data_added_total', 'Total new student data added.');
        push_metrics();
    }
    return $affected_rows;
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
    global $koneksi, $jurusan_nis_prefixes; // Akses array prefix jurusan

    $nis = $data['nis']; // NIS tidak diubah (biasanya readonly)
    $nama = htmlspecialchars($data['nama']);
    $tmpt_Lahir = htmlspecialchars($data['tmpt_Lahir']);
    $tgl_Lahir = $data['tgl_Lahir'];
    $jekel = $data['jekel'];
    $jurusan = htmlspecialchars($data['jurusan']);
    $email = htmlspecialchars($data['email']);
    $alamat = htmlspecialchars($data['alamat']);
    // Pastikan nilai IPK dan jalur_masuk diambil dengan benar, sesuai input form
    $ipk = isset($data['ipk']) ? htmlspecialchars($data['ipk']) : '';
    $jalur_masuk = isset($data['jalur_masuk']) ? htmlspecialchars($data['jalur_masuk']) : '';

    $gambarLama = htmlspecialchars($data['gambarLama']);

    // --- AWAL VALIDASI TAMBAHAN (dari kode lama) ---
    // Validasi NIS 10 digit (jika NIS bisa diubah, tapi di sini diset readonly, jadi hanya perlu validasi format)
    if (strlen($nis) !== 10 || !ctype_digit($nis)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format NIS Salah!',
                        text: 'NIS harus terdiri dari 10 digit angka.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
             </script>";
        return 0;
    }

    // Validasi 4 digit awal NIS sesuai jurusan (untuk jaga-jaga jika ada manipulasi form)
    if (array_key_exists($jurusan, $jurusan_nis_prefixes)) {
        $expected_prefix = $jurusan_nis_prefixes[$jurusan];
        $nis_prefix = substr($nis, 0, 4);
        if ($nis_prefix !== $expected_prefix) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'NIS Tidak Sesuai Jurusan!',
                            text: '4 digit awal NIS (\"" . htmlspecialchars($nis_prefix) . "\") tidak cocok dengan kode jurusan " . htmlspecialchars($jurusan) . " (harus \"" . htmlspecialchars($expected_prefix) . "\").',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    });
                </script>";
            return 0;
        }
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jurusan Tidak Valid!',
                        text: 'Jurusan yang dipilih tidak dikenali atau tidak memiliki prefix NIS yang valid.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
            </script>";
        return 0;
    }

    // Validasi format email (sudah ada, biarkan)
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
        return 0;
    }
    
    // Validasi IPK
    $ipk_float = floatval($ipk);
    if (!empty($ipk) && (!is_numeric($ipk) || $ipk_float < 0.00 || $ipk_float > 4.00)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format IPK Salah!',
                        text: 'IPK harus berupa angka antara 0.00 dan 4.00.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
            </script>";
        return 0;
    }

    // Validasi Jalur Masuk
    if (empty($jalur_masuk)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jalur Masuk Kosong!',
                        text: 'Mohon pilih jalur masuk mahasiswa.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                });
            </script>";
        return 0;
    }
    // --- AKHIR VALIDASI TAMBAHAN ---

    // Cek apakah ada file gambar baru yang diunggah atau tidak
    // Jika ada gambar baru (error code bukan 4), panggil fungsi upload
    // Jika tidak ada gambar baru (error code 4), gunakan gambarLama
    $gambar = $gambarLama; // Default, gunakan gambar lama
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== 4) { // Cek apakah ada input file dan bukan error NO_FILE
        $gambarBaru = upload();
        if ($gambarBaru === false) { // Jika upload gagal (misal: bukan gambar, ukuran terlalu besar)
            return 0; // Mengembalikan 0 agar fungsi ubah() gagal
        } else {
            $gambar = $gambarBaru; // Gunakan gambar baru yang berhasil diupload
        }
    }


    // Sesuaikan query UPDATE dengan kolom baru dan gambar
    $sql = "UPDATE siswa SET 
                nama = '$nama', 
                tmpt_Lahir = '$tmpt_Lahir', 
                tgl_Lahir = '$tgl_Lahir', 
                jekel = '$jekel', 
                jurusan = '$jurusan', 
                ipk = '$ipk_float', 
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
// Fungsi ini sekarang mengembalikan nama file baru, string kosong jika tidak ada file, atau false jika gagal.
function upload()
{
    // Jika tidak ada file yang diunggah (error code 4), atau tidak ada $_FILES['gambar']
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] === 4) { 
        return ''; // Kembalikan string kosong jika tidak ada gambar baru
    }

    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    $extValid = ['jpg', 'jpeg', 'png'];
    $ext = explode('.', $namaFile);
    $ext = strtolower(end($ext)); // Ambil ekstensi file

    // Validasi ekstensi
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
        return false; // Gagal upload
    }

    // Generate nama file baru yang unik untuk menghindari konflik nama
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ext;

    // Pastikan direktori 'img/' ada dan writable oleh server web.
    if (!is_dir('img/')) {
        mkdir('img/', 0755, true); // Buat direktori jika belum ada
    }

    if (move_uploaded_file($tmpName, 'img/' . $namaFileBaru)) {
        return $namaFileBaru; // Berhasil upload, kembalikan nama file baru
    } else {
        // Gagal memindahkan file (mungkin masalah permission atau path)
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

    // Menyimpan password sebagai MD5
    $password = md5($password); 

    // tambahkan user baru ke database
    // Perhatikan: query INSERT INTO user VALUES('', '$username', '$password') mungkin butuh kolom eksplisit
    // Jika kolom pertama di tabel 'user' adalah auto-increment ID, string kosong akan bekerja.
    // Namun, praktik terbaik adalah menyebutkan kolom secara eksplisit: INSERT INTO user (username, password)
    mysqli_query($koneksi, "INSERT INTO user (username, password) VALUES('$username', '$password')");

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

function processLogin(mysqli $koneksi, string $username, string $password): bool
{
    // Menggunakan MD5 seperti kode asli
    $password_input_hashed = md5($password);

    // PERINGATAN: Query ini rentan terhadap SQL Injection.
    // Sebaiknya gunakan prepared statements di kemudian hari untuk keamanan yang lebih baik.
    $username_escaped = mysqli_real_escape_string($koneksi, $username); // Tambahkan escaping untuk keamanan minimal
    $result = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username_escaped'");

    // Periksa apakah query berhasil dan ada baris yang ditemukan
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi password 
        if ($password_input_hashed === $row['password']) {
            // Jika berhasil, atur sesi
            $_SESSION['login'] = true;
            $_SESSION['username'] = $row['username'];
            
            return true;
        }
    }
    
    // MONITORING: Blok ini ditambahkan untuk mencatat login yang gagal.
    record_counter('logins_total', 'Total user logins.', ['status' => 'failed']);
    push_metrics();

    // Jika username tidak ditemukan atau password salah, kembalikan 'false'
    return false;
}
?>