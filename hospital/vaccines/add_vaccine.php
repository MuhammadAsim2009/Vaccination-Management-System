<?php
// Essential includes for authentication, header, sidebar, and database connection
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$alert_msg = '';
$alert_type = '';

// Add Vaccine Logic
if(isset($_POST['add_btn'])) {
    // Get form data
    $name = $_POST['vaccine_name'];
    $age_group = $_POST['age_group'];
    $doses = $_POST['total_doses'];
    $status = $_POST['availability_status'];
    $description = $_POST['description'];

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO vaccines (vaccine_name, target_age_group, total_dose, availability_status, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $age_group, $doses, $status, $description);
    if($stmt->execute()) {
        $alert_msg = "New vaccine added successfully.";
        $alert_type = "success";
    } else {
        $alert_msg = "Error adding vaccine: " . $conn->error;
        $alert_type = "danger";
    }

}

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
            <div class="col-md-12" id="alertPlaceholder"></div>
        </div>

        <form id="addVaccineForm" method="POST" class="needs-validation" novalidate>
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
                                    <input type="text" class="form-control" name="vaccine_name" id="vaccineName" placeholder="e.g., MMR Vaccine" required>
                                    <div class="invalid-feedback">Please enter the vaccine name.</div>
                                </div>
                                <div class="col-md-8">
                                    <label for="ageGroup" class="form-label fw-semibold">Target Age Group <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="age_group" id="ageGroup" placeholder="e.g., 1-5 years, All ages" required>
                                    <div class="invalid-feedback">Please enter a target age group.</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="totalDoses" class="form-label fw-semibold">Total Doses <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="total_doses" id="totalDoses" min="1" value="1" required>
                                    <div class="invalid-feedback">Enter required doses.</div>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="4" placeholder="Brief description of the vaccine, its purpose, and any notes..."></textarea>
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
                                <select class="form-select" name="availability_status" id="availabilityStatus">
                                    <option value="available" selected>Available</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Action Buttons Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-grid gap-2">
                                <button type="submit" name="add_btn" class="btn btn-primary btn-lg fw-bold">
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

    // Handle form submission for UI feedback
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            showAlert("Error! Please fill out all required fields correctly.", "danger");
        }
        form.classList.add('was-validated');
    });

    // Alert Logic
    const alertMsg = <?= json_encode($alert_msg) ?>;
    const alertType = <?= json_encode($alert_type) ?>;
    
    if(alertMsg) {
        showAlert(alertMsg, alertType);
    }

    function showAlert(message, type) {
        const placeholder = document.getElementById('alertPlaceholder');
        if(!placeholder) return;
        
        const wrapper = document.createElement('div');
        wrapper.innerHTML = [
            `<div class="alert alert-${type} alert-dismissible fade show border-0 shadow-sm rounded-3 py-3" role="alert">`,
            `   <div class="d-flex align-items-center">`,
            `       <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} fs-4 me-3"></i>`,
            `       <div>${message}</div>`,
            `   </div>`,
            '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
            '</div>'
        ].join('');
        
        placeholder.append(wrapper);
    }
});
</script>

<?php include '../includes/footer.php'; ?>