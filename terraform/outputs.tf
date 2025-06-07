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