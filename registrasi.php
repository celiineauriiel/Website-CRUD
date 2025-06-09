<?php

require_once 'function.php';

if (isset($_POST["register"])) {
    if (registrasi($_POST) > 0) {
        // Menggunakan SweetAlert2 untuk pesan berhasil
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil!',
                        text: 'Pengguna baru berhasil ditambahkan. Silakan login.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then((result) => {
                        window.location.href = 'login.php'; // Redirect ke halaman login setelah berhasil
                    });
                });
              </script>";
    } else {
        // Logika error dari function.php sudah menangani SweetAlert2
        // echo mysqli_error($koneksi); // Baris ini tidak perlu karena SweetAlert sudah di function.php
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
    <link rel="stylesheet" href="css/login.css"> <title>Register | Aplikasi Tim</title>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="card-title fw-bold text-primary">REGISTER</h2>
                            <p class="text-secondary">Buat akun baru Anda untuk memulai.</p>
                        </div>

                        <form action="" method="post" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">USERNAME <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" class="form-control" name="username" id="username"
                                        placeholder="Masukkan Username" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">PASSWORD <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="Masukkan Password" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password2" class="form-label">KONFIRMASI PASSWORD <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" name="password2" id="password2"
                                        placeholder="Konfirmasi Password" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-primary btn-lg" type="submit" name="register">REGISTER</button>
                            </div>
                            <p class="text-center text-secondary">Sudah punya akun?
                                <a href="login.php" class="text-decoration-none text-primary fw-bold">LOGIN</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        // Validasi form registrasi di sisi client (opsional, karena validasi utama ada di PHP)
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                let hasError = false;

                const usernameInput = document.getElementById('username');
                const passwordInput = document.getElementById('password');
                const password2Input = document.getElementById('password2');

                // Validasi field required
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value) {
                        field.classList.add('is-invalid');
                        hasError = true;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Validasi konfirmasi password hanya di client side untuk feedback instan
                if (passwordInput.value !== password2Input.value) {
                    passwordInput.classList.add('is-invalid');
                    password2Input.classList.add('is-invalid');
                    hasError = true;
                    Swal.fire({
                        icon: 'error',
                        title: 'Konfirmasi Password Tidak Sesuai!',
                        text: 'Mohon ulangi password Anda.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                } else {
                    passwordInput.classList.remove('is-invalid');
                    password2Input.classList.remove('is-invalid');
                }

                if (hasError) {
                    event.preventDefault(); // Mencegah form disubmit jika ada error
                    if (!Swal.isVisible()) { // Hanya tampilkan jika belum ada SweetAlert lain
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