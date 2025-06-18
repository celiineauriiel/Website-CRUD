<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('location:login.php');
    exit;
}

// Memanggil atau membutuhkan file function.php
require_once 'function.php';

// Menampilkan semua data dari table siswa berdasarkan nis secara Descending
$siswa = query("SELECT * FROM siswa ORDER BY nis DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm text-uppercase fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Sistem Admin Data Mahasiswa FTEIC ITS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <!-- add dashboard -->
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white ms-2" href="logout.php">Logout&nbsp;<i class="bi bi-box-arrow-right"></i></a> 
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <?php
        // Menampilkan pesan status penambahan data
        if (isset($_SESSION['status_tambah'])) {
            $type = $_SESSION['status_tambah']['type'];
            $message = $_SESSION['status_tambah']['message'];
            echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
            echo $message;
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['status_tambah']);
        }
        // Menampilkan pesan status ubah data (PERUBAHAN DI SINI)
        if (isset($_SESSION['status_ubah'])) {
            $type = $_SESSION['status_ubah']['type'];
            $message = $_SESSION['status_ubah']['message'];
            echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
            echo $message;
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['status_ubah']);
        }
        ?>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 rounded-4 p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="card-title fw-bold text-primary mb-3" id="dataSiswaTitle"><i
                                    class="bi bi-card-checklist"></i> DATA MAHASISWA</h2>
                            <p class="text-secondary">Daftar lengkap mahasiswa yang terdaftar dalam sistem.</p>
                        </div>
                        <hr class="mb-4">

                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <a href="addData.php" class="btn btn-primary" data-aos="fade-right" data-aos-duration="800"
                                data-aos-delay="1200">
                                <i class="bi bi-person-plus-fill"></i> Tambah Data Baru
                            </a>
                            <div class="dropdown" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="1600">
                                <button class="btn btn-success dropdown-toggle" type="button" id="dropdownExport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> Ekspor Data
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownExport">
                                    <li><a class="dropdown-item" href="export.php?format=xls" target="_blank">Ekspor ke XLS</a></li>
                                    <li><a class="dropdown-item" href="export.php?format=csv" target="_blank">Ekspor ke CSV</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="data" class="table table-striped table-hover text-center" style="width:100%">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Umur</th>
                                        <th>Jurusan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($siswa as $row) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td class="text-start"><?= htmlspecialchars($row['nama']); ?></td>
                                        <td><?= htmlspecialchars($row['jekel']); ?></td>
                                        <?php
                                        $now = time();
                                        $timeTahun = strtotime($row['tgl_Lahir']);
                                        $setahun = 31536000;
                                        $hitung = ($now - $timeTahun) / $setahun;
                                        ?>
                                        <td><?= floor($hitung); ?> Tahun</td>
                                        <td><?= htmlspecialchars($row['jurusan']); ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-info btn-sm text-white detail"
                                                    data-bs-toggle="modal" data-bs-target="#detailSiswaModal"
                                                    data-id="<?= htmlspecialchars($row['nis']); ?>" title="Detail">
                                                    <i class="bi bi-info-circle-fill"></i>
                                                </button>
                                                <a href="ubah.php?nis=<?= htmlspecialchars($row['nis']); ?>"
                                                    class="btn btn-warning btn-sm" title="Ubah">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="#" class="btn btn-danger btn-sm delete-btn"
                                                    data-id="<?= htmlspecialchars($row['nis']); ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama']); ?>" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailSiswaModal" tabindex="-1" aria-labelledby="detailSiswaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailSiswaModalLabel"><i class="bi bi-info-circle-fill"></i> Detail
                        Data Mahasiswa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailSiswaContent">
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-5 py-3 bg-dark text-white text-center">
        <div class="row">
            <div class="col" id="about">
                <h5 class="fw-bold text-uppercase mb-3">Tentang Aplikasi</h5>
                <p class="mb-0">
                    Aplikasi ini dikembangkan untuk memudahkan pengelolaan data mahasiswa.
                </p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/TextPlugin.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#data').DataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ]
            });

            // Fungsi Detail menggunakan Modal Bootstrap dan AJAX
            $('.detail').on('click', function() {
                var nis = $(this).data('id');
                console.log('NIS yang diklik:', nis); // Tambahan untuk debugging

                $.ajax({
                    // Tambahkan timestamp acak untuk mencegah caching agresif
                    url: "detail.php?_t=" + new Date().getTime(),
                    method: "post",
                    data: {
                        dataSiswa: nis
                    },
                    cache: false, // Penting: Pastikan caching AJAX dinonaktifkan
                    success: function(data) {
                        console.log('Data yang diterima:', data); // Tambahan untuk debugging
                        $('#detailSiswaContent').html(data);
                        $('#detailSiswaModal').modal("show");
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText); // Tambahan untuk debugging
                        Swal.fire('Error!', 'Terjadi kesalahan saat memuat detail: ' + xhr.statusText, 'error');
                    }
                });
            });

            // Fungsi untuk konfirmasi hapus menggunakan SweetAlert2 dan AJAX
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                var nis = $(this).data('id');
                var namaSiswa = $(this).data('nama');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data mahasiswa " + namaSiswa + " akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'hapus.php',
                            type: 'POST',
                            data: {
                                nis: nis
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire(
                                        'Berhasil!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data: ' + xhr.statusText, 'error');
                            }
                        });
                    }
                });
            });
        });

        // Inisialisasi AOS (Animate On Scroll)
        AOS.init({
            once: true,
            duration: 800,
        });

        // GSAP Animations
        gsap.registerPlugin(TextPlugin);
        gsap.to('#dataSiswaTitle', {
            duration: 1.5,
            delay: 0.5,
            text: {
                value: '<i class="bi bi-card-checklist"></i> DATA MAHASISWA',
                split: 'words'
            },
            ease: 'power1.out'
        });
        gsap.from('.navbar', {
            duration: 1,
            y: '-100%',
            opacity: 0,
            ease: 'bounce',
        });
        gsap.from('.card', {
            duration: 1.5,
            y: '50%',
            opacity: 0,
            ease: 'power3.out',
            delay: 0.7
        });
    </script>
</body>

</html>