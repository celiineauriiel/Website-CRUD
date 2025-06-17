<!-- <!DOCTYPE html>
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
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    
    <title>Dashboard Analitik | Aplikasi Data Mahasiswa</title>
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a> </li>
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

    <div class="container my-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 rounded-4 p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="card-title fw-bold text-primary mb-3" id="dashboardTitle"><i
                                    class="bi bi-bar-chart-fill"></i> DASHBOARD ANALITIK</h2>
                            <p class="text-secondary">Ringkasan statistik data mahasiswa.</p>
                        </div>
                        <hr class="mb-4">

                        <div id="dashboardContent">
                            <div class="row text-center mb-4">
                                <div class="row mb-4 justify-content-center"> <div class="col-md-4"> <div class="p-3 border rounded shadow-sm">
                                        <h5 class="text-primary">Total Mahasiswa</h5>
                                        <h3 class="fw-bold" id="totalMahasiswa">0</h3>
                                    </div>
                                </div>
                            </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-start pb-0"> <h5 class="card-title text-center mb-0">Distribusi Mahasiswa per Jurusan</h5> <div class="chart-wrapper" style="width: 100%; max-width: 500px; height: 350px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;"> <canvas id="jurusanChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-start pb-0"> <h5 class="card-title text-center mb-0">Distribusi Usia Mahasiswa</h5> <div class="chart-wrapper" style="width: 100%; height: 350px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;"> <canvas id="umurChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-start pb-0"> <h5 class="card-title text-center mb-0">Distribusi IPK Terakhir</h5> <div class="chart-wrapper" style="width: 100%; height: 350px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;"> <canvas id="ipkChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-start pb-0"> <h5 class="card-title text-center mb-0">Distribusi Jalur Masuk</h5> <div class="chart-wrapper" style="width: 100%; max-width: 370px; height: 350px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;"> <canvas id="jalurMasukChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
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
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/TextPlugin.min.js"></script>

    <script>
        $(document).ready(function() {
            // GSAP Animations (jika diperlukan)
            gsap.registerPlugin(TextPlugin);
            gsap.to('#dashboardTitle', {
                duration: 1.5,
                delay: 0.5,
                text: {
                    value: '<i class="bi bi-bar-chart-fill"></i> DASHBOARD ANALITIK',
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

            // Permintaan AJAX untuk data dashboard
            $.ajax({
                url: "api_dashboard.php",
                method: "GET",
                dataType: "json",
                success: function(data) {
                    console.log("Data diterima dari API:", data);

                    // --- Render Grafik Jurusan ---
                    const labelsJurusan = data.jurusan.map(item => item.jurusan);
                    const dataJumlahJurusan = data.jurusan.map(item => item.total);

                    const ctxJurusan = document.getElementById('jurusanChart').getContext('2d');
                    new Chart(ctxJurusan, {
                        type: 'pie',
                        data: {
                            labels: labelsJurusan,
                            datasets: [{
                                label: 'Jumlah Mahasiswa per Jurusan',
                                data: dataJumlahJurusan,
                                backgroundColor: [
                                    '#001F3F', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6c757d'
                                ], // Sesuaikan warna
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false, // Penting: Agar ukuran chart bisa diatur
                            plugins: {
                                title: {
                                    display: false, // HILANGKAN JUDUL CHART.JS
                                },
                                legend: {
                                    display: true, // Tampilkan legenda
                                    position: 'right', // Pindah legenda ke kanan
                                    labels: {
                                        font: {
                                            family: 'Poppins, sans-serif',
                                            size: 14,
                                            weight: 'normal'
                                        },
                                        padding: 20, // Jarak antar item legenda
                                        usePointStyle: true, // Gaya titik
                                    }
                                },
                                // AKTIFKAN DAN KONFIGURASI DATALABELS UNTUK PERSENTASE DI IRISAN
                                datalabels: {
                                    display: true, // AKTIFKAN DATALABELS
                                    formatter: (value, ctx) => {
                                        // Pastikan nilai dikonversi menjadi float sebelum perhitungan
                                        const numericValue = parseFloat(value); 

                                        // Gunakan reduce untuk menjumlahkan secara robust, pastikan setiap item juga float
                                        let sum = ctx.chart.data.datasets[0].data.reduce((acc, curr) => acc + parseFloat(curr), 0);

                                        if (sum === 0) return ''; // Hindari pembagian nol

                                        let percentage = (numericValue * 100 / sum).toFixed(1) + "%";
                                        if (numericValue === 0) return '';
                                        return percentage; // HANYA TAMPILKAN PERSENTASE
                                    },
                                    color: '#fff', // Warna teks label
                                    font: {
                                        family: 'Poppins, sans-serif',
                                        size: 14, // Ukuran font
                                        weight: 'bold',
                                    },
                                    anchor: 'center', // Posisi label di tengah irisan
                                    align: 'center', // Penjajaran di tengah
                                    offset: 0, // Hapus offset
                                    padding: 6,
                                    backgroundColor: 'rgba(0, 0, 0, 0.4)', // Background semi-transparan
                                    borderRadius: 5,
                                    textShadow: false,
                                }
                            }
                        },
                        // PASTIKAN PLUGIN DATALABELS TERDAFTAR DI SINI
                        plugins: [ChartDataLabels]
                    });

                    // --- Render Grafik Umur Mahasiswa ---
                    const usiaData = data.umur;
                    const labelsUmurOrdered = ['≤ 18', '19', '20', '21', '22', '≥ 23'];
                    const dataJumlahUmurOrdered = labelsUmurOrdered.map(label => usiaData.hasOwnProperty(label) ? usiaData[label] : 0);

                    const ctxUmur = document.getElementById('umurChart').getContext('2d');
                    new Chart(ctxUmur, {
                        type: 'bar',
                        data: {
                            labels: labelsUmurOrdered,
                            datasets: [{
                                label: 'Jumlah Mahasiswa',
                                data: dataJumlahUmurOrdered,
                                backgroundColor: '#17a2b8',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                title: {
                                    display: false,
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        font: {
                                            family: 'Poppins, sans-serif',
                                            size: 14,
                                            weight: 'normal'
                                        },
                                        autoSkip: false,
                                        maxRotation: 0,
                                        minRotation: 0,
                                        padding: 10,
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        font: {
                                            family: 'Poppins, sans-serif',
                                            size: 14,
                                            weight: 'normal'
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // --- Render Grafik IPK Terakhir ---
                    const labelsIpk = Object.keys(data.ipk);
                    const dataJumlahIpk = Object.values(data.ipk);
                    const ctxIpk = document.getElementById('ipkChart').getContext('2d');
                    new Chart(ctxIpk, {
                        type: 'bar',
                        data: {
                            labels: labelsIpk,
                            datasets: [{
                                label: 'Jumlah Mahasiswa',
                                data: dataJumlahIpk,
                                backgroundColor: [
                                    '#dc3545',
                                    '#ffc107',
                                    '#17a2b8',
                                    '#28a745'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                title: {
                                    display: false,
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        font: {
                                            family: 'Poppins, sans-serif',
                                            size: 14,
                                            weight: 'normal'
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        font: {
                                            family: 'Poppins, sans-serif',
                                            size: 14,
                                            weight: 'normal'
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // --- Render Grafik Jalur Masuk ---
                    const labelsJalurMasuk = data.jalur_masuk.map(item => item.jalur_masuk);
                    const dataJumlahJalurMasuk = data.jalur_masuk.map(item => item.total);
                    const ctxJalurMasuk = document.getElementById('jalurMasukChart').getContext('2d');
                    new Chart(ctxJalurMasuk, {
                        type: 'doughnut',
                        data: {
                            labels: labelsJalurMasuk,
                            datasets: [{
                                label: 'Jumlah Mahasiswa',
                                data: dataJumlahJalurMasuk,
                                backgroundColor: [
                                    '#001F3F',
                                    '#17a2b8',
                                    '#ffc107',
                                    '#6c757d'
                                ],
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false, // <<<<< PENTING: TAMBAHKAN INI
                            plugins: {
                                title: {
                                    display: false,
                                },
                                legend: {
                                    display: true,
                                    position: 'right',
                                    labels: {
                                        font: {
                                            family: 'Poppins, sans-serif',
                                            size: 14,
                                            weight: 'normal'
                                        },
                                        padding: 20,
                                        usePointStyle: true,
                                    }
                                },
                                datalabels: {
                                    formatter: (value, ctx) => {
                                        let sum = 0;
                                        let dataArr = ctx.chart.data.datasets[0].data;
                                        dataArr.map(data => {
                                            sum += data;
                                        });
                                        let percentage = (value * 100 / sum).toFixed(1) + "%";
                                        if (value === 0) return '';
                                        return percentage;
                                    },
                                    color: '#fff',
                                    font: {
                                        family: 'Poppins, sans-serif',
                                        size: 14,
                                        weight: 'bold',
                                    },
                                    anchor: 'center',
                                    align: 'center',
                                    offset: 0,
                                    padding: 6,
                                    backgroundColor: 'rgba(0, 0, 0, 0.4)',
                                    borderRadius: 5,
                                    textShadow: false,
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });

                    // --- Tampilkan Angka Total Mahasiswa ---
                    $('#totalMahasiswa').text(data.total_mahasiswa); 

                },
                error: function(xhr, status, error) {
                    console.error("Error fetching dashboard data:", xhr.responseText);
                    $('#dashboardContent').html('<div class="alert alert-danger">Gagal memuat data dashboard. Silakan coba lagi nanti.</div>');
                }
            });
        });

        // Inisialisasi AOS (Animate On Scroll)
        AOS.init({
            once: true,
            duration: 800,
        });
    </script>
</body>

</html> -->