<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'function.php';

$total_mahasiswa_query = query("SELECT COUNT(nis) as total_students FROM siswa");
$total_mahasiswa = $total_mahasiswa_query[0]['total_students'];
$data_jurusan = query("SELECT jurusan, COUNT(nis) as total FROM siswa GROUP BY jurusan");
$data_jekel = query("SELECT jekel, COUNT(nis) as total FROM siswa GROUP BY jekel");
$data_umur = query("SELECT tgl_Lahir FROM siswa");
$usia_distribusi = []; 

$usia_distribusi['≤ 18'] = 0;
for ($i = 19; $i <= 22; $i++) {
    $usia_distribusi[(string)$i] = 0;
}
$usia_distribusi['≥ 23'] = 0;


foreach ($data_umur as $mhs) {
    $dob = new DateTime($mhs['tgl_Lahir']);
    $now = new DateTime();
    $age = $now->diff($dob)->y;

    if ($age <= 18) {
        $usia_distribusi['≤ 18']++;
    } elseif ($age >= 19 && $age <= 22) {
        $usia_distribusi[(string)$age]++;
    } elseif ($age >= 23) {
        $usia_distribusi['≥ 23']++;
    }
}

$data_ipk_raw = query("SELECT ipk FROM siswa WHERE ipk IS NOT NULL");
$ipk_distribusi = [
    '0.00 - 1.99' => 0,
    '2.00 - 2.99' => 0,
    '3.00 - 3.49' => 0,
    '3.50 - 4.00' => 0,
];

foreach ($data_ipk_raw as $mhs) {
    $ipk_val = (float)$mhs['ipk'];
    if ($ipk_val >= 0.00 && $ipk_val <= 1.99) {
        $ipk_distribusi['0.00 - 1.99']++;
    } elseif ($ipk_val >= 2.00 && $ipk_val <= 2.99) {
        $ipk_distribusi['2.00 - 2.99']++;
    } elseif ($ipk_val >= 3.00 && $ipk_val <= 3.49) {
        $ipk_distribusi['3.00 - 3.49']++;
    } elseif ($ipk_val >= 3.50 && $ipk_val <= 4.00) {
        $ipk_distribusi['3.50 - 4.00']++;
    }
}

$data_jalur_masuk = query("SELECT jalur_masuk, COUNT(nis) as total FROM siswa GROUP BY jalur_masuk");

header('Content-Type: application/json');
echo json_encode([
    'total_mahasiswa' => $total_mahasiswa,
    'jurusan' => $data_jurusan,
    'jekel' => $data_jekel,
    'umur' => $usia_distribusi,
    'ipk' => $ipk_distribusi,
    'jalur_masuk' => $data_jalur_masuk
]);