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