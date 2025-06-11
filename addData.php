<?php
session_start();
// Jika tidak bisa login maka balik ke login.php
// jika masuk ke halaman ini melalui url, maka langsung menuju halaman login
if (!isset($_SESSION['login'])) {
    header('location:login.php');
    exit;
}

// Memanggil atau membutuhkan file function.php
require_once 'function.php';

// Inisialisasi variabel untuk menyimpan nilai form
$old_nis = isset($_POST['nis']) ? htmlspecialchars($_POST['nis']) : '';
$old_nama = isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '';
$old_tmpt_Lahir = isset($_POST['tmpt_Lahir']) ? htmlspecialchars($_POST['tmpt_Lahir']) : '';
$old_tgl_Lahir = isset($_POST['tgl_Lahir']) ? htmlspecialchars($_POST['tgl_Lahir']) : '';
$old_jekel = isset($_POST['jekel']) ? htmlspecialchars($_POST['jekel']) : '';
$old_jurusan = isset($_POST['jurusan']) ? htmlspecialchars($_POST['jurusan']) : '';
$old_ipk = isset($_POST['ipk']) ? htmlspecialchars($_POST['ipk']) : '';
$old_jalur_masuk = isset($_POST['jalur_masuk']) ? htmlspecialchars($_POST['jalur_masuk']) : '';
$old_email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$old_alamat = isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : '';

// Jika fungsi tambah lebih dari 0/data tersimpan, maka munculkan alert dibawah
if (isset($_POST['simpan'])) {
    // Menangkap output dari fungsi tambah (yang mungkin berisi SweetAlert script dari function.php)
    ob_start(); // Mulai buffering output
    $tambah_result = tambah($_POST); // Panggil fungsi tambah
    $tambah_output = ob_get_clean(); // Ambil outputnya

    if ($tambah_result > 0) {
        $_SESSION['status_tambah'] = [
            'type' => 'success',
            'message' => 'Data mahasiswa berhasil ditambahkan!'
        ];
        header('location:index.php');
        exit;
    } else {
        // Jika fungsi tambah dari 0/data tidak tersimpan,
        // cek apakah SweetAlert sudah di-echo oleh function.php
        if (!empty($tambah_output)) {
            echo $tambah_output; // Tampilkan SweetAlert dari function.php
        } else {
            // Jika tidak ada SweetAlert spesifik dari function.php, tampilkan SweetAlert default
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js'></script>";
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menambahkan Data!',
                            text: 'Terjadi kesalahan saat menambahkan data. Mohon cek kembali isian form.',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    });
                  </script>";
        }
    }
}
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="css/style.css">

    <title>Tambah Data Mahasiswa</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm text-uppercase fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Sistem Admin Data Mahasiswa</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a> </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white ms-2" href="logout.php">Logout <i
                                class="bi bi-box-arrow-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-3"> 
        <?php
        // Menampilkan pesan status jika ada (dari proses submit yang gagal)
        if (isset($_SESSION['status_tambah'])) {
            $type = $_SESSION['status_tambah']['type'];
            $message = $_SESSION['status_tambah']['message'];
            echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
            echo $message;
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['status_tambah']);
        }
        ?>

        <div class="row my-5">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 rounded-4 p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="card-title fw-bold text-primary mb-3" id="addDataTitle"><i
                                    class="bi bi-person-plus-fill"></i> TAMBAH DATA MAHASISWA</h2>
                            <p class="text-secondary">Isi formulir di bawah ini untuk menambahkan data mahasiswa baru.</p>
                        </div>
                        <hr class="mb-4">

                        <form action="" method="post" enctype="multipart/form-data" novalidate>
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="nis" placeholder="Masukkan NIS" min="1"
                                    name="nis" autocomplete="off" required
                                    minlength="10" maxlength="10" value="<?= $old_nis; ?>"> <div class="invalid-feedback">
                                    NIS harus tepat 10 digit angka.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama"
                                    placeholder="Masukkan Nama Lengkap" name="nama" autocomplete="off" required
                                    value="<?= $old_nama; ?>"> </div>
                            <div class="mb-3">
                                <label for="tmpt_Lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tmpt_Lahir"
                                    placeholder="Masukkan Tempat Lahir" name="tmpt_Lahir" autocomplete="off" required
                                    value="<?= $old_tmpt_Lahir; ?>"> </div>
                            <div class="mb-3">
                                <label for="tgl_Lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tgl_Lahir" name="tgl_Lahir"
                                    max="2006-01-01" required value="<?= $old_tgl_Lahir; ?>"> </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jekel" id="Laki - Laki"
                                            value="Laki - Laki" required <?= ($old_jekel == 'Laki - Laki') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="Laki - Laki">Laki - Laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jekel" id="Perempuan"
                                            value="Perempuan" required <?= ($old_jekel == 'Perempuan') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="Perempuan">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" id="jurusan" name="jurusan" required>
                                    <option value="">Pilih Jurusan</option>
                                    <option value="Teknik Elektro" <?= ($old_jurusan == 'Teknik Elektro') ? 'selected' : ''; ?>>Teknik Elektro</option>
                                    <option value="Teknik Biomedik" <?= ($old_jurusan == 'Teknik Biomedik') ? 'selected' : ''; ?>>Teknik Biomedik</option>
                                    <option value="Teknik Komputer" <?= ($old_jurusan == 'Teknik Komputer') ? 'selected' : ''; ?>>Teknik Komputer</option>
                                    <option value="Teknik Informatika" <?= ($old_jurusan == 'Teknik Informatika') ? 'selected' : ''; ?>>Teknik Informatika</option>
                                    <option value="Sistem Informasi" <?= ($old_jurusan == 'Sistem Informasi') ? 'selected' : ''; ?>>Sistem Informasi</option>
                                    <option value="Teknologi Informasi" <?= ($old_jurusan == 'Teknologi Informasi') ? 'selected' : ''; ?>>Teknologi Informasi</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ipk" class="form-label">IPK Terakhir</label>
                                <input type="number" step="0.01" min="0.00" max="4.00" class="form-control" id="ipk" placeholder="Masukkan IPK (contoh: 3.75)" name="ipk" value="<?= $old_ipk; ?>">
                                <div class="form-text text-secondary">Masukkan nilai IPK (contoh: 3.75). Kosongkan jika belum tersedia.</div>
                            </div>
                            <div class="mb-3">
                                <label for="jalur_masuk" class="form-label">Jalur Masuk <span class="text-danger">*</span></label>
                                <select class="form-select" id="jalur_masuk" name="jalur_masuk" required>
                                    <option value="">Pilih Jalur Masuk</option>
                                    <option value="SNBP" <?= ($old_jalur_masuk == 'SNBP') ? 'selected' : ''; ?>>SNBP</option>
                                    <option value="SNBT" <?= ($old_jalur_masuk == 'SNBT') ? 'selected' : ''; ?>>SNBT</option>
                                    <option value="Mandiri" <?= ($old_jalur_masuk == 'Mandiri') ? 'selected' : ''; ?>>Mandiri</option>
                                    <option value="Lainnya" <?= ($old_jalur_masuk == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-Mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" placeholder="Masukkan E-Mail"
                                    name="email" autocomplete="off" required value="<?= $old_email; ?>"> </div>
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Gambar</label>
                                <input class="form-control" id="gambar" name="gambar" type="file">
                                <div class="form-text text-secondary">Hanya menerima format JPG, JPEG, PNG (Opsional)</div> </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat" rows="3" name="alamat"
                                    placeholder="Masukkan Alamat Lengkap" autocomplete="off" required><?= $old_alamat; ?></textarea> </div>
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="index.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary" name="simpan">Simpan Data</button>
                            </div>
                        </form>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/TextPlugin.min.js"></script>

    <script>
        gsap.registerPlugin(TextPlugin);
        gsap.to('#addDataTitle', {
            duration: 1.5,
            delay: 0.5,
            text: {
                value: '<i class="bi bi-person-plus-fill"></i> TAMBAH DATA MAHASISWA',
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

        // ===========================================
        // SCRIPT UNTUK VALIDASI NIS 10 DIGIT DENGAN SWEETALERT2
        // ===========================================
        document.addEventListener('DOMContentLoaded', function() {
            const nisInput = document.getElementById('nis');
            const form = document.querySelector('form');
            const jurusanSelect = document.getElementById('jurusan');

            nisInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, ''); // Hapus karakter non-digit

                if (this.value.length !== 10) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });

            form.addEventListener('submit', function(event) {
                let hasError = false;

                // Validasi NIS (karena ini sudah diatur dengan SweetAlert di PHP, kita bisa tambahkan validasi frontend yang spesifik juga)
                if (nisInput.value.length !== 10) {
                    event.preventDefault();
                    nisInput.classList.add('is-invalid');
                    hasError = true;
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'NIS harus tepat 10 digit angka!',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    return; // Hentikan validasi lain jika NIS sudah salah
                }

                // Validasi awalan NIS berdasarkan Jurusan (frontend, agar feedback instan)
                const nisPrefix = nisInput.value.substring(0, 7); // Ambil 7 digit pertama
                let expectedPrefix = '';
                switch(jurusanSelect.value) {
                    case 'T. Elektro': expectedPrefix = '5022221'; break;
                    case 'T. Biomedik': expectedPrefix = '5023221'; break;
                    case 'T. Komputer': expectedPrefix = '5024221'; break;
                    case 'Teknik Informatika': expectedPrefix = '5025221'; break;
                    case 'Sistem Informasi': expectedPrefix = '5026221'; break;
                    case 'Teknologi Informasi': expectedPrefix = '5027';
                        if (nisInput.value.substring(0, 4) !== expectedPrefix) {
                            hasError = true;
                            nisInput.classList.add('is-invalid');
                            Swal.fire({
                                icon: 'error',
                                title: 'Awalan NIS Tidak Sesuai Jurusan!',
                                html: 'Untuk jurusan <b>' + jurusanSelect.value + '</b>, NIS harus diawali dengan <b>' + expectedPrefix + '</b>.',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                            event.preventDefault();
                            return;
                        }
                        break;
                }

                if (jurusanSelect.value !== 'Teknologi Informasi' && nisPrefix !== expectedPrefix && expectedPrefix !== '') {
                    hasError = true;
                    nisInput.classList.add('is-invalid');
                    Swal.fire({
                        icon: 'error',
                        title: 'Awalan NIS Tidak Sesuai Jurusan!',
                        html: 'Untuk jurusan <b>' + jurusanSelect.value + '</b>, NIS harus diawali dengan <b>' + expectedPrefix + '</b>.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    event.preventDefault();
                    return;
                }


                // Validasi field required lainnya (tetap dilakukan)
                const requiredFields = form.querySelectorAll('[required]:not([type="radio"])'); // Exclude radio for specific handling
                requiredFields.forEach(field => {
                    if (!field.value || (field.type === 'select-one' && field.value === '')) {
                        field.classList.add('is-invalid');
                        hasError = true;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Validasi radio button group (Jenis Kelamin)
                const jekelRadios = form.querySelectorAll('input[name="jekel"]');
                const jekelChecked = Array.from(jekelRadios).some(radio => radio.checked);
                if (!jekelChecked) {
                    if (jekelRadios.length > 0) jekelRadios[0].classList.add('is-invalid');
                    hasError = true;
                } else {
                    if (jekelRadios.length > 0) jekelRadios[0].classList.remove('is-invalid');
                }

                // Validasi Jalur Masuk (jika required)
                const jalurMasukSelect = document.getElementById('jalur_masuk');
                if (jalurMasukSelect.value === '' && jalurMasukSelect.hasAttribute('required')) {
                    jalurMasukSelect.classList.add('is-invalid');
                    hasError = true;
                } else {
                    jalurMasukSelect.classList.remove('is-invalid');
                }

                if (hasError) {
                    event.preventDefault();
                    if (!Swal.isVisible()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Form Belum Lengkap!',
                            text: 'Mohon isi semua field yang wajib diisi (ditandai dengan *)',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                }
            });
        });
    </script>
</body>

</html>