<?php
// Aktifkan error reporting untuk debugging sementara. HAPUS ini di PRODUKSI!
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Memanggil atau membutuhkan file function.php
require_once 'function.php';

// Jika dataSiswa diklik maka
if (isset($_POST['dataSiswa'])) {
    $output = '';
    
    // Debugging: Lihat apa yang diterima dari POST
    // var_dump($_POST); // Aktifkan ini untuk melihat isi $_POST yang diterima
    // echo "<pre>"; print_r($_POST); echo "</pre>"; // Alternatif yang lebih rapi

    // Mengambil NIS dari POST dan membersihkannya
    $nis_to_show = mysqli_real_escape_string($koneksi, $_POST['dataSiswa']);
    
    // Debugging: Lihat NIS yang akan digunakan untuk query
    // echo "NIS yang digunakan untuk query: " . $nis_to_show . "<br>";

    // Mengambil data siswa dari database menggunakan fungsi query() kamu
    $siswa_detail = query("SELECT * FROM siswa WHERE nis = '$nis_to_show' LIMIT 1");

    // Pastikan data ditemukan. Jika tidak ditemukan, $siswa_detail akan berupa array kosong.
    if (empty($siswa_detail)) {
        echo '<div class="alert alert-warning" role="alert">Data mahasiswa tidak ditemukan untuk NIS: ' . htmlspecialchars($nis_to_show) . '</div>';
        exit;
    }

    // Ambil data baris pertama (dan satu-satunya)
    $row = $siswa_detail[0]; 

    $output .= '<div class="table-responsive">
                    <table class="table table-bordered">';
    
    // Bagian Gambar
    $output .= '
        <tr align="center">
            <td colspan="2">';
    // PERBAIKAN DI SINI: Cek apakah ada gambar atau tidak
    if (!empty($row['gambar'])) { // Jika nama gambar tidak kosong
        $output .= '<img src="img/' . htmlspecialchars($row['gambar']) . '" class="img-fluid rounded-circle mb-3" style="max-width: 150px; height: 150px; object-fit: cover;">';
    } else {
        // Tampilkan placeholder gambar jika tidak ada gambar
        $output .= '<img src="img/default.png" class="img-fluid rounded-circle mb-3" style="max-width: 150px; height: 150px; object-fit: cover;" alt="No Image">';
    }
    $output .= '
            </td>
        </tr>';
    
    // Bagian Detail Informasi Lainnya
    $output .= '
        <tr>
            <th width="40%">NIS</th>
            <td width="60%">' . htmlspecialchars($row['nis']) . '</td>
        </tr>
        <tr>
            <th width="40%">Nama</th>
            <td width="60%">' . htmlspecialchars($row['nama']) . '</td>
        </tr>
        <tr>
            <th width="40%">Tempat dan Tanggal Lahir</th>
            <td width="60%">' . htmlspecialchars($row['tmpt_Lahir']) . ', ' . date("d M Y", strtotime($row['tgl_Lahir'])) . '</td>
        </tr>
        <tr>
            <th width="40%">Jenis Kelamin</th>
            <td width="60%">' . htmlspecialchars($row['jekel']) . '</td>
        </tr>
        <tr>
            <th width="40%">Jurusan</th>
            <td width="60%">' . htmlspecialchars($row['jurusan']) . '</td>
        </tr>
        <tr>
            <th width="40%">E-Mail</th>
            <td width="60%">' . htmlspecialchars($row['email']) . '</td>
        </tr>
        <tr>
            <th width="40%">Alamat</th>
            <td width="60%">' . htmlspecialchars($row['alamat']) . '</td>
        </tr>';
    
    $output .= '</table></div>';
    
    // Tampilkan $output
    echo $output;

} else {
    // Jika tidak ada dataSiswa yang dikirimkan (akses langsung), berikan pesan error
    echo '<div class="alert alert-danger" role="alert">Akses tidak valid.</div>';
}
?>