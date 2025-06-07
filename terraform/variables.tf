variable "gcp_project_id" {
  description = "pso-b-kel11"
  type        = string
}

variable "gcp_region" {
  description = "Region GCP utama (misalnya, asia-southeast2)."
  type        = string
  default     = "asia-southeast2" 
}

variable "db_instance_name" {
  description = "Nama untuk instance Cloud SQL."
  type        = string
  default     = "kelompok11" 
}

variable "db_name" {
  description = "Nama database di Cloud SQL."
  type        = string
  default     = "data_siswa"
}

variable "db_user_name" {
  description = "Nama user utama untuk database Cloud SQL."
  type        = string
  default     = "kelompok11user" 
}

variable "db_password" {
  description = "Password untuk user database Cloud SQL (sensitif!)."
  type        = string
  sensitive   = true
  default     = "kelompok11pass"
}

variable "artifact_registry_repo_name" {
  description = "Nama repositori Artifact Registry untuk Docker images."
  type        = string
  default     = "datasiswa" 
}

variable "cloud_run_service_name" {
  description = "Nama layanan Cloud Run."
  type        = string
  default     = "datasiswa"
}