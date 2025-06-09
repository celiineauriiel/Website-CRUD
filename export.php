<?php
// Memanggil atau membutuhkan file function.php
require_once 'function.php';

// Menampilkan semua data dari table siswa berdasarkan nis secara Descending
$siswa = query("SELECT * FROM siswa ORDER BY nis DESC");

// Menentukan format ekspor berdasarkan parameter GET
$format = isset($_GET['format']) ? $_GET['format'] : 'xls'; // Default ke xls

// Mengubah nama file dan header berdasarkan format
$filename_base = "Data Mahasiswa-" . date('Ymd');
$content_type = '';
$filename_ext = '';

switch ($format) {
    case 'csv':
        $content_type = 'text/csv';
        $filename_ext = '.csv';
        break;
    case 'xls':
    default: // Default ke XLS jika format tidak dikenal
        $content_type = 'application/vnd-ms-excel';
        $filename_ext = '.xls';
        break;
}

header("Content-type: " . $content_type);
header("Content-Disposition: attachment; filename=" . $filename_base . $filename_ext);

// Kodingan untuk export ke Excel (XLS) atau CSV
if ($format == 'xls') {
    // Output sebagai tabel HTML untuk XLS
?>
<table class="text-center" border="1">
    <thead class="text-center">
        <tr>
            <th>No.</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Tempat dan Tanggal Lahir</th>
            <th>Umur</th>
            <th>Jenis Kelamin</th>
            <th>Jurusan</th>
            <th>E-Mail</th>
            <th>Gambar</th> <th>Alamat</th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php $no = 1; ?>
        <?php foreach ($siswa as $row) : ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nis']); ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['tmpt_Lahir']); ?>, <?= htmlspecialchars($row['tgl_Lahir']); ?></td>
                <?php
                $now = time();
                $timeTahun = strtotime($row['tgl_Lahir']);
                $setahun = 31536000;
                $hitung = ($now - $timeTahun) / $setahun;
                ?>
                <td><?= floor($hitung); ?> Tahun</td>
                <td><?= htmlspecialchars($row['jekel']); ?></td>
                <td><?= htmlspecialchars($row['jurusan']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['gambar']); ?></td> <td><?= htmlspecialchars($row['alamat']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
} elseif ($format == 'csv') {
    // Output sebagai CSV
    $output = fopen('php://output', 'w'); // Buka output stream

    // Headers CSV (Nama Kolom)
    fputcsv($output, [
        'No.', 'NIS', 'Nama', 'Tempat dan Tanggal Lahir', 'Umur',
        'Jenis Kelamin', 'Jurusan', 'E-Mail', 'Gambar', 'Alamat'
    ]);

    // Data CSV
    $no = 1;
    foreach ($siswa as $row) {
        $umur = floor((time() - strtotime($row['tgl_Lahir'])) / 31536000) . ' Tahun';
        fputcsv($output, [
            $no++,
            $row['nis'],
            $row['nama'],
            $row['tmpt_Lahir'] . ', ' . $row['tgl_Lahir'],
            $umur,
            $row['jekel'],
            $row['jurusan'],
            $row['email'],
            $row['gambar'], // Data Gambar Ditambahkan
            $row['alamat']
        ]);
    }

    fclose($output); // Tutup output stream
}
exit; // Pastikan tidak ada output lain setelah ini
?>