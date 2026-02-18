<?php
// Essential includes
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Check if vaccine ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location.href='vaccine_list.php';</script>";
    exit();
}

// Get vaccine ID from URl
$vaccine_id = $_GET['id'];

// Fetch data from datebase
$stmt_vaccine = $conn->prepare("SELECT * FROM vaccines WHERE id = ?");
$stmt_vaccine->bind_param("i", $vaccine_id);
$stmt_vaccine->execute();
$result_vaccine = $stmt_vaccine->get_result();
$row = $result_vaccine->fetch_assoc();

// Prepare vaccine data for display
$vaccine = [
    'id' => $row['id'],
    'name' => $row['vaccine_name'],
    'age_group' => $row['target_age_group'],
    'doses' => $row['total_dose'] ?? 1,
    'status' => ucfirst($row['availability_status']),
    'description' => $row['description'],
    'created_at' => date('M d, Y', strtotime($row['created_at']))
];

// Alerts variables
$update_msg = '';
$update_type = '';

// Update vaccine details
if(isset($_POST['update_btn'])) {
    $vaccine_id = $_POST['vaccine_id'];
    $name = $_POST['vaccine_name'];
    $age_group = $_POST['target_age_group'];
    $doses = $_POST['total_dose'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    $stmt_update = $conn->prepare("UPDATE vaccines SET vaccine_name = ?, target_age_group = ?, total_dose = ?, availability_status = ?, description = ? WHERE id = ?");
    $stmt_update->bind_param("ssissi", $name, $age_group, $doses, $status, $description, $vaccine_id);
    if($stmt_update->execute()) {
        $update_msg = "Vaccine details updated successfully.";
        $update_type = "success";
        
        // Refresh data for display
        $vaccine['name'] = $name;
        $vaccine['age_group'] = $age_group;
        $vaccine['doses'] = $doses;
        $vaccine['status'] = ucfirst($status);
        $vaccine['description'] = $description;
    } else {
        $update_msg = "Error updating vaccine: " . $conn->error;
        $update_type = "danger";
    } 
}

?>

<!-- Main Content -->
<main class="mt-5 pt-3">
    <div class="container-fluid">

        <!-- 1. Page Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="vaccine_list.php" class="text-decoration-none text-dark">Vaccines</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Update Vaccine</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary mb-0">Update Vaccine Details</h2>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="vaccine_list.php" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Alerts Placeholder -->
        <div class="row">
            <div class="col-12" id="alertPlaceholder"></div>
        </div>

        <form id="updateVaccineForm" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="vaccine_id" value="<?= $vaccine['id'] ?>">
            <div class="row g-4">
                
                <!-- Left Column: Edit Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-edit me-2 text-primary"></i>Edit Information</h5>
                        </div>
                        <div class="card-body p-4">
                            
                            <!-- Basic Info Section -->
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Basic Details</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label for="vaccineName" class="form-label fw-semibold">Vaccine Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-syringe text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" name="vaccine_name" id="vaccineName" value="<?= $vaccine['name'] ?>" required>
                                        <div class="invalid-feedback">Please enter vaccine name.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Specs Section -->
                            <h6 class="text-uppercase text-muted small fw-bold mb-3 pt-2 border-top">Medical Specifications</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="ageGroup" class="form-label fw-semibold">Target Age Group <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-child text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="ageGroup" name="target_age_group" value="<?= $vaccine['age_group'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="doses" class="form-label fw-semibold">Doses Required <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-prescription-bottle text-muted"></i></span>
                                        <input type="number" class="form-control border-start-0" id="doses" name="total_dose" min="1" value="<?= $vaccine['doses'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter vaccine details..."><?= $vaccine['description'] ?></textarea>
                                </div>
                            </div>

                            <!-- Availability Status Section -->
                            <h6 class="text-uppercase text-muted small fw-bold mb-3 pt-2 border-top">Availability Status</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="status" class="form-label fw-semibold">Availability</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="available" <?= $vaccine['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                                        <option value="unavailable" <?= $vaccine['status'] == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mt-5 pt-3 border-top">
                                <button type="submit" name="update_btn" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm">
                                    <i class="fas fa-save me-2"></i>Update Vaccine
                                </button>
                                <button type="reset" class="btn btn-light border btn-lg px-4 rounded-pill">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right Column: Preview & Summary -->
                <div class="col-lg-4">
                    
                    <!-- Preview Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white position-relative overflow-hidden">
                        <!-- Decorative Circle -->
                        <div class="position-absolute top-0 end-0 p-3 opacity-25">
                            <i class="fas fa-vial fa-5x"></i>
                        </div>
                        
                        <div class="card-body p-4 position-relative z-1">
                            <h6 class="text-uppercase text-white-50 small fw-bold mb-3">Live Preview</h6>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-white text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-syringe fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0" id="previewName"><?= $vaccine['name'] ?></h5>
                                    <small class="text-white-50" id="previewId">#<?= $vaccine['id'] ?></small>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-white-50 d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Age Group</small>
                                    <span class="fw-medium" id="previewAge"><?= $vaccine['age_group'] ?></span>
                                </div>
                                <div class="col-6">
                                    <small class="text-white-50 d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Doses</small>
                                    <span class="fw-medium" id="previewDoses"><?= $vaccine['doses'] ?></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-white-50 d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Description</small>
                                <p class="small text-white-50 mb-0" id="previewDescription" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= $vaccine['description'] ?></p>
                            </div>

                            <div class="d-flex justify-content-end align-items-end">
                                <span class="badge bg-white text-primary rounded-pill px-3 py-2" id="previewStatus">
                                    <?= $vaccine['status'] ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle me-2 text-info"></i>Update Guidelines</h6>
                            <ul class="list-unstyled small text-muted mb-0 d-grid gap-2">
                                <li><i class="fas fa-check text-success me-2"></i>Verify vaccine name and target age group accuracy.</li>
                                <li><i class="fas fa-check text-success me-2"></i>Ensure dose count follows national immunization schedule.</li>
                                <li><i class="fas fa-check text-success me-2"></i>Setting status to 'Unavailable' stops new appointments.</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateVaccineForm');

    // Live Preview Elements
    const inputs = {
        name: document.getElementById('vaccineName'),
        age: document.getElementById('ageGroup'),
        doses: document.getElementById('doses'),
        description: document.getElementById('description'),
        status: document.getElementById('status')
    };

    const previews = {
        name: document.getElementById('previewName'),
        age: document.getElementById('previewAge'),
        doses: document.getElementById('previewDoses'),
        description: document.getElementById('previewDescription'),
        status: document.getElementById('previewStatus')
    };

    // Live Update Logic
    Object.keys(inputs).forEach(key => {
        if (inputs[key]) {
            inputs[key].addEventListener('input', () => {
                previews[key].textContent = inputs[key].value || '-';
            });
        }
    });

    // Handle Reset to update Live Preview
    form.addEventListener('reset', function() {
        setTimeout(() => {
            Object.keys(inputs).forEach(key => {
                if (inputs[key]) previews[key].textContent = inputs[key].value || '-';
            });
        }, 0);
    });

    // Form Submission
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Alert Logic
    const alertMsg = <?= json_encode($update_msg) ?>;
    const alertType = <?= json_encode($update_type) ?>;
    
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