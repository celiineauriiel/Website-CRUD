# Mengaktifkan API yang dibutuhkan oleh semua resource
resource "google_project_service" "compute" {
  project = var.gcp_project_id
  service = "compute.googleapis.com"
}
resource "google_project_service" "sqladmin" {
  project = var.gcp_project_id
  service = "sqladmin.googleapis.com"
}
resource "google_project_service" "artifactregistry" {
  project = var.gcp_project_id
  service = "artifactregistry.googleapis.com"
}

# Membuat VM (Virtual Machine) di Google Compute Engine
resource "google_compute_instance" "monitoring_vm" {
  project      = var.gcp_project_id
  name         = var.monitoring_vm_name
  machine_type = var.monitoring_vm_type
  zone         = var.gcp_zone

  # Memberi 'tag' agar aturan firewall bisa diterapkan
  tags = ["monitoring-server"]

  boot_disk {
    initialize_params {
      image = "ubuntu-os-cloud/ubuntu-2204-lts" # Menggunakan image Ubuntu
    }
  }

  network_interface {
    network = "default"
    access_config {
      // Dikosongkan agar GCP memberikan alamat IP eksternal secara otomatis
    }
  }

  # Skrip yang berjalan otomatis saat VM pertama kali dibuat
  # untuk menginstal Docker dan Docker Compose.
  metadata_startup_script = <<-EOT
    #!/bin/bash
    # Update sistem & install prasyarat
    apt-get update -y
    apt-get install -y ca-certificates curl gnupg
    
    # Tambahkan GPG key resmi Docker
    install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
    chmod a+r /etc/apt/keyrings/docker.gpg
    
    # Tambahkan repositori Docker
    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
      $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
      tee /etc/apt/sources.list.d/docker.list > /dev/null
      
    # Update lagi dengan repo Docker baru & install
    apt-get update -y
    apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
  EOT

  depends_on = [google_project_service.compute]
}

# Membuat Aturan Firewall untuk Grafana (Port 3000)
resource "google_compute_firewall" "allow_grafana" {
  project = var.gcp_project_id
  name    = "allow-grafana-ingress"
  network = "default"
  allow {
    protocol = "tcp"
    ports    = ["3000"]
  }
  source_ranges = ["0.0.0.0/0"] # Izinkan dari semua alamat IP
  target_tags   = ["monitoring-server"]
}

# Membuat Aturan Firewall untuk Prometheus Pushgateway (Port 9091)
resource "google_compute_firewall" "allow_pushgateway" {
  project = var.gcp_project_id
  name    = "allow-pushgateway-ingress"
  network = "default"
  allow {
    protocol = "tcp"
    ports    = ["9091"]
  }
  source_ranges = ["0.0.0.0/0"]
  target_tags   = ["monitoring-server"]
}

# cloud_sql.tf
resource "google_sql_database_instance" "default" {
  name             = var.db_instance_name
  region           = var.gcp_region
  database_version = "MYSQL_8_0" # Atau versi lain yang Anda inginkan

  settings {
    tier    = "db-f1-micro" # Mulai dengan yang kecil untuk hemat biaya
    # Anda bisa menambahkan konfigurasi lain seperti backup, IP, dll.
    ip_configuration {
      ipv4_enabled = true # Perlu untuk Cloud SQL Auth Proxy jika konek via IP, atau untuk akses langsung jika diizinkan
      # Untuk koneksi Cloud Run via socket, public IP tidak wajib jika private IP digunakan.
      # Namun, untuk kemudahan koneksi awal/migrasi dari GHA, proxy via IP bisa lebih mudah disetup.
    }
  }

  deletion_protection = false # Set true untuk produksi agar tidak mudah terhapus
}

resource "google_sql_database" "default_db" {
  name     = var.db_name
  instance = google_sql_database_instance.default.name
}

resource "google_sql_user" "default_user" {
  name     = var.db_user_name
  instance = google_sql_database_instance.default.name
  password = var.db_password
}

# artifact_registry.tf
resource "google_artifact_registry_repository" "docker_repo" {
  location      = var.gcp_region
  repository_id = var.artifact_registry_repo_name
  description   = "Docker repository for datasiswa application"
  format        = "DOCKER"
}

# cloud_run.tf
resource "google_cloud_run_v2_service" "default" {
  name     = var.cloud_run_service_name
  location = var.gcp_region

  template {
    containers {
      image = "gcr.io/cloudrun/placeholder" # Placeholder image, akan diupdate oleh CI/CD
    }
    # Set agar bisa diakses tanpa autentikasi (jika diinginkan)
  }

  # Izinkan akses publik (unauthenticated)
  # Anda bisa membuatnya lebih ketat jika perlu
  # Ini akan membuat IAM binding
}
resource "google_project_service" "run" {
  service = "run.googleapis.com"
}
resource "google_cloud_run_service_iam_member" "allow_public" {
  location = google_cloud_run_v2_service.default.location
  project  = google_cloud_run_v2_service.default.project // Bisa juga tidak perlu jika project sama dengan provider
  service  = google_cloud_run_v2_service.default.name  // <-- PERBAIKAN: Gunakan 'service'
  role     = "roles/run.invoker"
  member   = "allUsers"
}