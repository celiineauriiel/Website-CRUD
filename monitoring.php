<?php
// File: monitoring.php

require __DIR__ . '/vendor/autoload.php';

use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use Prometheus\PushGateway;
use Prometheus\Gauge;

function getRegistry() {
    // Gunakan registry yang sama di seluruh request untuk konsistensi
    static $registry = null;
    if ($registry === null) {
        $registry = new CollectorRegistry(new InMemory());
    }
    return $registry;
}

// Fungsi untuk menambah counter
function record_counter(string $name, string $help, array $labels = []) {
    $registry = getRegistry();
    $counter = $registry->getOrRegisterCounter('php', $name, $help, array_keys($labels));
    $counter->inc(array_values($labels));
}

// Fungsi untuk mengirim metrik ke Pushgateway
function push_metrics() {
    // Ambil alamat Pushgateway dari environment variable
    $pushgatewayAddress = getenv('PROMETHEUS_PUSHGATEWAY_ADDRESS');
    if (!$pushgatewayAddress) {
        // Jangan lakukan apa-apa jika tidak dikonfigurasi
        return;
    }

    $registry = getRegistry();
    $pushGateway = new PushGateway($pushgatewayAddress);
    // 'php_app' adalah nama job, 'instance_id' bisa di-generate secara acak
    // atau menggunakan variabel dari Cloud Run jika tersedia.
    $pushGateway->push($registry, 'php_app', ['instance_id' => 'cloud_run']);
}

?>