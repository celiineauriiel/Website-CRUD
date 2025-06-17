output "cloud_sql_instance_connection_name" {
  description = "Instance Connection Name untuk Cloud SQL."
  value       = google_sql_database_instance.default.connection_name
}

output "cloud_run_service_url" {
  description = "URL layanan Cloud Run setelah deployment pertama (mungkin placeholder)."
  value       = google_cloud_run_v2_service.default.uri
}

output "artifact_registry_repository_url" {
  description = "URL repositori Artifact Registry."
  value       = "${var.gcp_region}-docker.pkg.dev/${var.gcp_project_id}/${var.artifact_registry_repo_name}"
}

output "monitoring_vm_external_ip" {
  description = "Alamat IP Eksternal untuk VM Server Monitoring (akses Grafana di http://34.101.208.120:3000/)."
  value       = google_compute_instance.monitoring_vm.network_interface[0].access_config[0].nat_ip
}