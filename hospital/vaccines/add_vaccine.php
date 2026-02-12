<?php
/**
 * Add New Vaccine Page
 * Hospital Panel - Vaccination Management System
 * 
 * This page provides a frontend interface for hospital staff to add a new
 * type of vaccine into the system's inventory.
 * 
 * @path /hospital/vaccines/add_vaccine.php
 */

// Essential includes for authentication, header, sidebar, and footer
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<!-- Main Content -->
<main class="mt-5 pt-3">
    <div class="container-fluid">

        <!-- 1. Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="vaccine_list.php" class="text-decoration-none text-black">Vaccines</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add New Vaccine</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary">Add New Vaccine</h2>
                <p class="text-muted">Register a new vaccine type into the hospital's inventory system.</p>
            </div>
        </div>

        <!-- UI Alerts Placeholder -->
        <div class="row">
            <div class="col-md-12">
                <!-- Success Alert -->
                <div id="successAlert" class="alert alert-success d-none alert-dismissible fade show shadow-sm rounded-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fs-4 me-3"></i>
                        <div>
                            <strong>Success!</strong> The new vaccine has been added to the system. (UI Mockup)
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <!-- Error Alert -->
                <div id="errorAlert" class="alert alert-danger d-none alert-dismissible fade show shadow-sm rounded-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
                        <div>
                            <strong>Error!</strong> Please fill out all required fields correctly.
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <form id="addVaccineForm" class="needs-validation" novalidate>
            <div class="row g-4">
                <!-- Left Column: Main Info -->
                <div class="col-lg-8">
                    <!-- 2. Vaccine Information Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-file-medical-alt me-2 text-primary"></i>Vaccine Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="vaccineName" class="form-label fw-semibold">Vaccine Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="vaccineName" placeholder="e.g., MMR Vaccine" required>
                                    <div class="invalid-feedback">Please enter the vaccine name.</div>
                                </div>
                                <div class="col-md-8">
                                    <label for="ageGroup" class="form-label fw-semibold">Target Age Group <span class="text-danger">*</span></label>
                                    <select class="form-select" id="ageGroup" required>
                                        <option value="" selected disabled>Select age group...</option>
                                        <option value="0-6">0–6 months</option>
                                        <option value="6-12">6–12 months</option>
                                        <option value="1-5">1–5 years</option>
                                        <option value="all">All ages</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a target age group.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="totalDoses" class="form-label fw-semibold">Total Doses <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="totalDoses" min="1" value="1" required>
                                    <div class="invalid-feedback">Enter required doses.</div>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control" id="description" rows="4" placeholder="Brief description of the vaccine, its purpose, and any notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Status & Actions -->
                <div class="col-lg-4">
                    <!-- 3. Availability & Status Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-warehouse me-2 text-success"></i>Availability & Status</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-0">
                                <label for="availabilityStatus" class="form-label fw-semibold">Availability Status</label>
                                <select class="form-select" id="availabilityStatus">
                                    <option value="Available" selected>Available</option>
                                    <option value="Unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Action Buttons Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="fas fa-plus-circle me-2"></i>Add Vaccine
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-2"></i>Reset Form
                                </button>
                                <a href="vaccine_list.php" class="btn btn-light border mt-2">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the form and alert elements
    const form = document.getElementById('addVaccineForm');
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');

    // Handle form submission for UI feedback
    form.addEventListener('submit', function(event) {
        // Prevent the default form submission
        event.preventDefault();
        event.stopPropagation();

        // Hide alerts initially
        successAlert.classList.add('d-none');
        errorAlert.classList.add('d-none');

        // Check form validity using Bootstrap's built-in method
        if (form.checkValidity()) {
            // If the form is valid, show a success message (simulation)
            console.log('Form is valid. Simulating submission...');
            
            // Show success alert
            successAlert.classList.remove('d-none');
            
            // Scroll to the top to make the alert visible
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Reset the form after a short delay
            setTimeout(() => {
                form.reset();
                form.classList.remove('was-validated');
            }, 2000);

        } else {
            // If the form is invalid, show an error message
            console.log('Form is invalid.');
            errorAlert.classList.remove('d-none');
        }

        // Add Bootstrap's 'was-validated' class to show validation feedback
        form.classList.add('was-validated');
    });

    // Optional: Reset validation state when the reset button is clicked
    form.addEventListener('reset', function() {
        form.classList.remove('was-validated');
        successAlert.classList.add('d-none');
        errorAlert.classList.add('d-none');
    });
});
</script>

<?php include '../includes/footer.php'; ?>