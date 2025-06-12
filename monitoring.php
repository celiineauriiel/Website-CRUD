<?php
// File: monitoring.php (Versi Debug)

// Pastikan autoloader dimuat
require __DIR__ . '/vendor/autoload.php';

use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use Prometheus\PushGateway;
// Tambahkan 'use' untuk Exception agar bisa ditangkap
use GuzzleHttp\Exception\GuzzleException;

function getRegistry() {
    static $registry = null;
    if ($registry === null) {
        $registry = new CollectorRegistry(new InMemory());
    }
    return $registry;
}

function record_counter(string $name, string $help, array $labels = []) {
    $registry = getRegistry();
    $counter = $registry->getOrRegisterCounter('php', $name, $help, array_keys($labels));
    $counter->inc(array_values($labels));
}

// <<< FUNGSI YANG KITA MODIFIKASI SECARA SIGNIFIKAN >>>
function push_metrics() {
    // Menulis log ke Cloud Run Logs
    error_log("DEBUG: Fungsi push_metrics() DIPANGGIL.");

    $pushgatewayAddress = getenv('PROMETHEUS_PUSHGATEWAY_ADDRESS');
    if (!$pushgatewayAddress) {
        error_log("DEBUG: GAGAL. Environment variable 'PROMETHEUS_PUSHGATEWAY_ADDRESS' tidak ditemukan.");
        return;
    }
    error_log("DEBUG: Alamat Pushgateway ditemukan: " . $pushgatewayAddress);

    $registry = getRegistry();
    $pushGateway = new PushGateway($pushgatewayAddress);

    try {
        error_log("DEBUG: Mencoba mengirim metrik ke Pushgateway...");
        $pushGateway->push($registry, 'php_app', ['instance_id' => 'cloud_run']);
        error_log("DEBUG: SUKSES. Metrik berhasil dikirim.");

    } catch (GuzzleException $e) {
        // Menangkap SEMUA kemungkinan error dari Guzzle (library HTTP)
        error_log("DEBUG: GAGAL MENGIRIM METRIK. Exception: " . $e->getMessage());
    }
}
?>