<?php
// Reusable includes
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Get hospital ID from URL
$hospital_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$update_success = false;
$update_error = '';

if ($hospital_id <= 0) {
    echo "<div class='alert alert-danger m-4 text-center'>Invalid hospital ID.</div>";
    include '../includes/footer.php';
    exit();
}

// Fetch data from datebase
$stmt_hospitals = $conn->prepare("SELECT h.id, h.hospital_name, h.registration_no, u.email, h.phone, h.address, h.status FROM hospitals h LEFT JOIN users u ON h.user_id = u.id WHERE h.id = ?");
$stmt_hospitals->bind_param("i", $hospital_id);
$stmt_hospitals->execute();
$result_hospitals = $stmt_hospitals->get_result();
$hospital = $result_hospitals->fetch_assoc();

if(!$hospital) {
    echo "<div class='alert alert-danger m-4 text-center'>Hospital not found.</div>";
    include '../includes/footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospital_name = trim($_POST['hospital_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // Update hospital details in the database
    $stmt_update = $conn->prepare("UPDATE hospitals SET hospital_name = ?, phone = ?, address = ?, status = ? WHERE id = ?");
    $stmt_update->bind_param("ssssi", $hospital_name, $phone, $address, $status, $hospital_id);
    if($stmt_update->execute()) {
        $update_success = true;
        // Refresh data
        $hospital['hospital_name'] = $hospital_name;
        $hospital['phone'] = $phone;
        $hospital['address'] = $address;
        $hospital['status'] = $status;
    } else {
        $update_error = "Error updating record: " . $conn->error;
    }
}

?>

<div class="main-content p-4">
    <!-- Breadcrumb & Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Update Hospital</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="hospital_list.php" class="text-decoration-none text-primary">Hospitals</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update Hospital</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="hospital_list.php" class="btn btn-outline-secondary shadow-sm rounded-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Success/Error Alert Placeholders -->
    <div id="alertPlaceholder">
    </div>

    <!-- Form Section -->
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h3 class="mb-0 m-3 fw-bold text-dark">
                        <i class="fas fa-edit me-2 text-primary"></i>Update Hospital Details
                    </h3>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form id="updateHospitalForm" class="needs-validation" novalidate method="POST">
                        <!-- Hidden Hospital ID Field -->
                        <input type="hidden" name="hospital_id" value="<?php echo $hospital['id']; ?>">
                        <div class="row g-4">
                            <!-- Hospital Name -->
                            <div class="col-md-6">
                                <label for="hospitalName" class="form-label fw-semibold">Hospital Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-hospital text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0" id="hospitalName" name="hospital_name" value="<?php echo htmlspecialchars($hospital['hospital_name']); ?>" placeholder="Enter Full Hospital Name" required>
                                </div>
                                <div class="invalid-feedback">Please provide a hospital name.</div>
                            </div>

                            <!-- Registration Number (READ ONLY) -->
                            <div class="col-md-6">
                                <label for="regNumber" class="form-label fw-semibold">Registration Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 text-muted" id="regNumber" name="reg_no" value="<?php echo htmlspecialchars($hospital['registration_no']); ?>" readonly title="Registration Number cannot be edited.">
                                </div>
                                <div class="form-text small text-muted">Registration Number cannot be modified.</div>
                            </div>

                            <!-- Email Address (READ ONLY) -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0" id="email" name="email" value="<?php echo htmlspecialchars($hospital['email']); ?>" placeholder="hospital@example.com" readonly title="Email cannot be edited.">
                                </div>
                                <div class="form-text small text-muted">Email cannot be modified.</div>
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="tel" class="form-control bg-light border-start-0" id="phone" name="phone" value="<?php echo htmlspecialchars($hospital['phone']); ?>" placeholder="+1 (XXX) XXX-XXXX" required>
                                </div>
                                <div class="invalid-feedback">Please provide a phone number.</div>
                            </div>

                            <!-- Status Dropdown -->
                            <div class="col-md-12">
                                <label for="status" class="form-label fw-semibold">Account Status</label>
                                <select class="form-select bg-light" id="status" name="status" required>
                                    <option value="approved" <?php echo $hospital['status'] == 'approved' ? 'selected' : ''; ?>>Accept</option>
                                    <option value="rejected" <?php echo $hospital['status'] == 'rejected' ? 'selected' : ''; ?>>Reject</option>
                                    <option value="pending" <?php echo $hospital['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                </select>
                                <div class="invalid-feedback">Please select an account status.</div>
                            </div>

                            <!-- Full Address -->
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Office Address</label>
                                <textarea class="form-control bg-light" id="address" name="address" rows="3" placeholder="Enter full physical address..." required><?php echo htmlspecialchars($hospital['address']); ?></textarea>
                                <div class="invalid-feedback">Please provide the address.</div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 mt-5">
                                <hr class="my-4 opacity-50">
                                <div class="d-flex justify-content-center flex-column flex-sm-row gap-3">
                                    <button type="submit" name="update_btn" class="btn btn-primary px-5 py-2 rounded-3 shadow-none fw-bold" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>Update Hospital
                                    </button>
                                    <a href="hospital_list.php" class="btn btn-light px-5 py-2 rounded-3 fw-bold border text-muted">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

// UI Interactions for Update Hospital Page

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateHospitalForm');
    const submitBtn = document.getElementById('submitBtn');
    const alertPlaceholder = document.getElementById('alertPlaceholder');

    // Helper to show alerts

    function showAlert(message, type) {
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
        alertPlaceholder.append(wrapper);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (wrapper.parentNode) {
                wrapper.remove();
            }
        }, 5000);
    }

    // Trigger alerts from PHP state
    <?php if ($update_success): ?>
        showAlert('Hospital details updated successfully!', 'success');
    <?php endif; ?>
    
    <?php if (!empty($update_error)): ?>
        showAlert(<?php echo json_encode($update_error); ?>, 'danger');
    <?php endif; ?>

    // Form Validation & Submission (Mockup)
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
            showAlert('Please ensure all required fields are correctly updated.', 'danger');
        } else {
            // Allow form to submit to PHP
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
        }
    }, false);
});

</script>

<?php include '../includes/footer.php'; ?>
