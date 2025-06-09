<?php
session_start();
// // Jika tidak bisa login maka balik ke login.php
// jika masuk ke halaman ini melalui url, maka langsung menuju halaman login
if (!isset($_SESSION['login'])) {
    header('location:login.php');
    exit;
}
// Memanggil atau membutuhkan file function.php
require_once 'function.php';

// Mengambil data dari nis dengan fungsi get
// $nis = $_GET['nis']; // <--- BARIS INI SALAH UNTUK REQUEST AJAX POST
// PERUBAHAN: Ambil data dari $_POST karena AJAX di index.php mengirimnya sebagai POST
$nis = $_POST['nis']; // <--- INI PERBAIKANNYA!

// Menyiapkan respons dalam format JSON
header('Content-Type: application/json');

// Jika fungsi hapus lebih dari 0/data terhapus, maka siapkan respons sukses
if (hapus($nis) > 0) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Data mahasiswa berhasil dihapus!'
    ]);
} else {
    // Jika fungsi hapus dibawah dari 0/data tidak terhapus, maka siapkan respons gagal
    echo json_encode([
        'status' => 'error', // Ubah status dari 'failed' menjadi 'error' agar konsisten dengan SweetAlert
        'message' => 'Data mahasiswa gagal dihapus! Kemungkinan data terkait.'
    ]);
}
exit; // Penting untuk menghentikan eksekusi setelah mengirim JSON
?>