terraform {
  required_providers {
    google = {
      source  = "hashicorp/google"
      version = "~> 5.0" # Gunakan versi terbaru yang sesuai
    }
  }
}

provider "google" {
  project = var.gcp_project_id
  region  = var.gcp_region
}