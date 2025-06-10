<?php
session_start();
require_once 'function.php';
// Jika bisa login maka ke index.php
if (isset($_SESSION['login'])) {
    header('location:index.php');
    exit;
}

// jika tombol yang bernama login diklik
if (isset($_POST['login'])) {
    
    // Panggil fungsi yang baru kita buat
    $loginSuccess = processLogin($koneksi, $_POST['username'], $_POST['password']);

    // Periksa hasil dari fungsi
    if ($loginSuccess) {
        // Jika fungsi mengembalikan true, barulah lakukan redirect dan exit
        header('location:index.php');
        exit;
    } else {
        // Jika fungsi mengembalikan false, set variabel error
        $error = true;
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
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


    <title>Login | Aplikasi Tim</title>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="card-title fw-bold text-primary">LOGIN</h2>
                            <p class="text-secondary">Selamat Datang! Silakan masuk untuk melanjutkan.</p>
                        </div>

                        <?php if (isset($error)) : ?>
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Login Gagal!',
                                    text: 'Username atau Password Salah!',
                                    confirmButtonText: 'OK',
                                    buttonsStyling: false,
                                    customClass: { confirmButton: 'btn btn-primary' }
                                });
                            });
                        </script>
                        <?php endif; ?>

                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">USERNAME</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" class="form-control" id="username"
                                        placeholder="Masukkan Username Anda" name="username" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">PASSWORD</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="password"
                                        placeholder="Masukkan Password Anda" name="password" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label" for="remember">REMEMBER ME</label>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-primary btn-lg" type="submit" name="login">Login</button>
                            </div>
                            <p class="text-center text-secondary">BELUM PUNYA AKUN?
                                <a href="registrasi.php" class="text-decoration-none text-primary fw-bold">REGISTER</a> </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous">
    </script>
</body>

</html>