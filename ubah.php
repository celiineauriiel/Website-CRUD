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

// Mengambil data dari nis dengan fungsi get
// NIS ini adalah primary key, jadi seharusnya selalu ada dan valid
$nis_to_edit = isset($_GET['nis']) ? mysqli_real_escape_string($koneksi, $_GET['nis']) : '';

// Mengambil data dari table siswa dari nis yang tidak sama dengan 0
// Menggunakan fungsi query() yang sudah ada
$siswa = query("SELECT * FROM siswa WHERE nis = '$nis_to_edit'");

$old_jurusan = ''; // Inisialisasi default
if (!empty($siswa)) {
    // Asumsi $siswa adalah array yang berisi satu baris data
    // Jadi kita ambil elemen pertama (indeks 0) dari array $siswa
    // Dan kemudian ambil nilai dari kolom 'jurusan'
    $old_jurusan = $siswa[0]['jurusan'];
} else {
    // Handle jika siswa tidak ditemukan (misalnya, redirect atau tampilkan pesan error)
    echo "Data siswa dengan NIS " . htmlspecialchars($nis_to_edit) . " tidak ditemukan.";
    // Mungkin Anda ingin menghentikan eksekusi script di sini
    exit();
}
// Jika data tidak ditemukan, redirect atau tampilkan pesan error
if (empty($siswa)) {
    // Menggunakan SweetAlert2 untuk pesan data tidak ditemukan
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Data Tidak Ditemukan!',
                    text: 'Data mahasiswa dengan NIS " . htmlspecialchars($nis_to_edit) . " tidak ditemukan.',
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then((result) => {
                    window.location.href = 'index.php';
                });
            });
          </script>";
    exit;
}

// Ambil data detail siswa (karena query mengembalikan array dari array)
$siswa_data = $siswa[0];

// Jika fungsi ubah lebih dari 0/data terubah, maka munculkan alert dibawah
if (isset($_POST['ubah'])) {
    // Menangkap output dari fungsi ubah (yang mungkin berisi SweetAlert script dari function.php)
    ob_start(); // Mulai buffering output
    $ubah_result = ubah($_POST); // Panggil fungsi ubah
    $ubah_output = ob_get_clean(); // Ambil outputnya

    if ($ubah_result > 0) {
        // Jika berhasil, atur pesan sukses di session untuk index.php
        $_SESSION['status_ubah'] = [
            'type' => 'success',
            'message' => 'Data mahasiswa berhasil diubah!'
        ];
        header('location:index.php');
        exit;
    } else {
        // Jika gagal, cek apakah fungsi ubah sudah mengeluarkan SweetAlert.
        // Jika $ubah_output tidak kosong, berarti SweetAlert sudah di-echo oleh function.php.
        if (!empty($ubah_output)) {
            echo $ubah_output; // Tampilkan SweetAlert dari function.php
            // Data form akan tetap terisi karena tidak ada redirect HTTP di sini
            // (karena SweetAlert sudah di-echo, kita tidak bisa lagi melakukan header redirect)
        } else {
            // Jika tidak ada SweetAlert spesifik dari function.php (mungkin karena error lain yang tidak spesifik),
            // maka tampilkan SweetAlert default dari ubah.php sendiri.
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengubah Data!',
                            text: 'Terjadi kesalahan saat mengubah data. Mohon cek kembali isian form.',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    });
                  </script>";
            // Data form akan tetap terisi karena tidak ada redirect HTTP di sini
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

    <title>Ubah Data Mahasiswa</title>
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
        // Ini HANYA untuk pesan yang diset oleh ubah.php sendiri.
        // Pesan dari function.php sudah di-echo langsung ke output buffering di atas.
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

        <div class="row my-5">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 rounded-4 p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="card-title fw-bold text-primary mb-3" id="ubahDataTitle"><i
                                    class="bi bi-pencil-square"></i> UBAH DATA MAHASISWA</h2>
                            <p class="text-secondary">Formulir untuk mengubah data mahasiswa yang sudah ada.</p>
                        </div>
                        <hr class="mb-4">

                        <form action="" method="post" enctype="multipart/form-data" novalidate>
                            <input type="hidden" name="gambarLama" value="<?= htmlspecialchars($siswa_data['gambar']); ?>">
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS</label>
                                <input type="number" class="form-control" id="nis" value="<?= htmlspecialchars($siswa_data['nis']); ?>"
                                    name="nis" readonly>
                                <div class="invalid-feedback">
                                    NIS harus tepat 10 digit angka dan 4 digit awal harus sesuai dengan jurusan.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama"
                                    placeholder="Masukkan Nama Lengkap" name="nama" autocomplete="off" required
                                    value="<?= htmlspecialchars($siswa_data['nama']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="tmpt_Lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tmpt_Lahir"
                                    placeholder="Masukkan Tempat Lahir" name="tmpt_Lahir" autocomplete="off" required
                                    value="<?= htmlspecialchars($siswa_data['tmpt_Lahir']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="tgl_Lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tgl_Lahir" name="tgl_Lahir"
                                    required value="<?= htmlspecialchars($siswa_data['tgl_Lahir']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jekel" id="Laki - Laki"
                                            value="Laki - Laki" required
                                            <?= ($siswa_data['jekel'] == 'Laki - Laki') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="Laki - Laki">Laki - Laki</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jekel" id="Perempuan"
                                            value="Perempuan" required
                                            <?= ($siswa_data['jekel'] == 'Perempuan') ? 'checked' : ''; ?>>
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
                                <input type="number" step="0.01" min="0.00" max="4.00" class="form-control" id="ipk" placeholder="Masukkan IPK (contoh: 3.75)" name="ipk" value="<?= htmlspecialchars($siswa_data['ipk']); ?>">
                                <div class="form-text text-secondary">Masukkan nilai IPK (contoh: 3.75). Kosongkan jika belum tersedia.</div>
                            </div>
                            <div class="mb-3">
                                <label for="jalur_masuk" class="form-label">Jalur Masuk <span class="text-danger">*</span></label>
                                <select class="form-select" id="jalur_masuk" name="jalur_masuk" required>
                                    <option value="">Pilih Jalur Masuk</option>
                                    <option value="SNBP" <?= ($siswa_data['jalur_masuk'] == 'SNBP') ? 'selected' : ''; ?>>SNBP</option>
                                    <option value="SNBT" <?= ($siswa_data['jalur_masuk'] == 'SNBT') ? 'selected' : ''; ?>>SNBT</option>
                                    <option value="Mandiri" <?= ($siswa_data['jalur_masuk'] == 'Mandiri') ? 'selected' : ''; ?>>Mandiri</option>
                                    <option value="Lainnya" <?= ($siswa_data['jalur_masuk'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-Mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" placeholder="Masukkan E-Mail"
                                    name="email" autocomplete="off" required
                                    value="<?= htmlspecialchars($siswa_data['email']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Gambar</label>
                                <div class="mb-2">
                                    <?php if (!empty($siswa_data['gambar'])) : ?>
                                        <img src="img/<?= htmlspecialchars($siswa_data['gambar']); ?>" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" alt="Gambar Saat Ini">
                                    <?php else : ?>
                                        <img src="img/default.png" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" alt="No Image">
                                    <?php endif; ?>
                                </div>
                                <input class="form-control" id="gambar" name="gambar" type="file">
                                <div class="form-text text-secondary">Kosongkan jika tidak ingin mengubah gambar. Hanya menerima format JPG, JPEG, PNG.</div>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat" rows="3" name="alamat"
                                    placeholder="Masukkan Alamat Lengkap" autocomplete="off" required><?= htmlspecialchars($siswa_data['alamat']); ?></textarea>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="index.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary" name="ubah">Ubah Data</button>
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
        gsap.to('#ubahDataTitle', {
            duration: 1.5,
            delay: 0.5,
            text: {
                value: '<i class="bi bi-pencil-square"></i> UBAH DATA MAHASISWA',
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
        // SCRIPT UNTUK VALIDASI FORM DENGAN SWEETALERT2 dan validasi NIS berdasarkan Jurusan
        // ===========================================
        document.addEventListener('DOMContentLoaded', function() {
            const nisInput = document.getElementById('nis');
            const jurusanSelect = document.getElementById('jurusan');
            const form = document.querySelector('form');

            // Map JavaScript dari PHP untuk prefix NIS
            const jurusanNisPrefixes = <?php echo json_encode($jurusan_nis_prefixes); ?>;

            function validateNisJurusanConsistency() {
                const nisValue = nisInput.value;
                const selectedJurusan = jurusanSelect.value;
                let isValid = true;
                let feedbackMessage = 'NIS harus tepat 10 digit angka dan 4 digit awal harus sesuai dengan jurusan.';

                if (nisValue.length === 10 && /^\d+$/.test(nisValue) && selectedJurusan) {
                    const expectedPrefix = jurusanNisPrefixes[selectedJurusan];
                    const nisPrefix = nisValue.substring(0, 4);

                    if (expectedPrefix && nisPrefix !== expectedPrefix) {
                        isValid = false;
                        feedbackMessage = 6 digit awal NIS (${nisPrefix}) tidak cocok dengan kode jurusan ${selectedJurusan} (harus ${expectedPrefix}).;
                    }
                } else if (nisValue.length !== 10 || !/^\d+$/.test(nisValue)) {
                    isValid = false; // NIS tidak 10 digit atau bukan angka
                } else if (!selectedJurusan) {
                    // NIS 10 digit tapi jurusan belum dipilih
                    isValid = false;
                    feedbackMessage = 'Mohon pilih jurusan terlebih dahulu untuk memvalidasi NIS.';
                }

                if (!isValid) {
                    nisInput.classList.add('is-invalid');
                    nisInput.classList.remove('is-valid');
                    nisInput.nextElementSibling.textContent = feedbackMessage;
                } else {
                    nisInput.classList.remove('is-invalid');
                    nisInput.classList.add('is-valid');
                }
                return isValid;
            }

            // Panggil validasi saat halaman dimuat dan NIS/Jurusan berubah
            validateNisJurusanConsistency(); // Untuk inisialisasi pada saat load halaman ubah
            jurusanSelect.addEventListener('change', validateNisJurusanConsistency);


            form.addEventListener('submit', function(event) {
                let hasError = false;

                // Validasi field required
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (field.type === 'radio' && field.name === 'jekel') {
                        const radioGroup = form.querySelectorAll(input[name="${field.name}"]);
                        if (!Array.from(radioGroup).some(radio => radio.checked)) {
                            hasError = true;
                            if (radioGroup.length > 0) radioGroup[0].classList.add('is-invalid');
                        } else {
                            if (radioGroup.length > 0) radioGroup[0].classList.remove('is-invalid');
                        }
                    } else if (!field.value || (field.type === 'select-one' && field.value === '')) {
                        field.classList.add('is-invalid');
                        hasError = true;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Validasi Jalur Masuk
                const jalurMasukSelect = document.getElementById('jalur_masuk');
                if (jalurMasukSelect.value === '' && jalurMasukSelect.hasAttribute('required')) {
                    jalurMasukSelect.classList.add('is-invalid');
                    hasError = true;
                } else {
                    jalurMasukSelect.classList.remove('is-invalid');
                }

                // PENTING: Validasi konsistensi NIS dan Jurusan saat submit
                if (!validateNisJurusanConsistency()) {
                    hasError = true;
                }


                if (hasError) {
                    event.preventDefault();
                    if (!Swal.isVisible()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Form Belum Lengkap atau Ada Kesalahan!',
                            text: 'Mohon isi semua field yang wajib diisi (ditandai dengan *) dan periksa validasi NIS.',
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