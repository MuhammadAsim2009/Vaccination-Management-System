<?php
/**
 * Vaccination Management System - Admin Module
 * update_hospital.php: Update details for an existing hospital.
 * 
 * Tech Stack: PHP, HTML5, CSS3, Bootstrap 5, JavaScript, Font Awesome.
 * Design: SaaS Admin UI, Medical/Healthcare Theme.
 */

// Reusable includes
include '../includes/header.php';
include '../includes/sidebar.php';

// Dummy data for the hospital being edited (Simulating a database fetch)
$hospital = [
    'id' => 'HSP-101',
    'name' => 'City General Hospital',
    'reg_no' => 'REG-2023-001',
    'email' => 'contact@citygeneral.com',
    'phone' => '+1 (555) 123-4567',
    'address' => '123 Health Ave, Medical District',
    'status' => 'Accepted'
];
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
    <div id="alertPlaceholder"></div>

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
                    <form id="updateHospitalForm" class="needs-validation" novalidate>
                        <div class="row g-4">
                            <!-- Hospital Name -->
                            <div class="col-md-6">
                                <label for="hospitalName" class="form-label fw-semibold">Hospital Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-hospital text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0" id="hospitalName" name="name" value="<?php echo htmlspecialchars($hospital['name']); ?>" placeholder="Enter Full Hospital Name" required>
                                </div>
                                <div class="invalid-feedback">Please provide a hospital name.</div>
                            </div>

                            <!-- Registration Number (READ ONLY) -->
                            <div class="col-md-6">
                                <label for="regNumber" class="form-label fw-semibold">Registration Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 text-muted" id="regNumber" name="reg_no" value="<?php echo htmlspecialchars($hospital['reg_no']); ?>" readonly title="Registration Number cannot be edited.">
                                </div>
                                <div class="form-text small text-muted">Registration Number cannot be modified.</div>
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0" id="email" name="email" value="<?php echo htmlspecialchars($hospital['email']); ?>" placeholder="hospital@example.com" required>
                                </div>
                                <div class="invalid-feedback">Please provide a valid email address.</div>
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
                                    <option value="Accepted" <?php echo $hospital['status'] == 'Accepted' ? 'selected' : ''; ?>>Accepted</option>
                                    <option value="Rejected" <?php echo $hospital['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    <option value="Pending" <?php echo $hospital['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
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
                                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-none fw-bold" id="submitBtn">
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
            
            <p class="text-center mt-4 text-muted small">
                <i class="fas fa-info-circle me-1 text-primary"></i> Last updated: <span class="fw-bold">Feb 10, 2026</span>
            </p>
        </div>
    </div>
</div>

<script>
/**
 * UI Interactions for Update Hospital Page
 */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateHospitalForm');
    const submitBtn = document.getElementById('submitBtn');
    const alertPlaceholder = document.getElementById('alertPlaceholder');

    /**
     * Helper to show alerts
     */
    function showAlert(message, type) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = [
            `<div class="alert alert-${type} alert-dismissible fade show border-0 shadow-sm rounded-3 py-3" role="alert">`,
            `   <div class="d-flex align-items-center">`,
            `       <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} fs-4 me-3"></i>`,
            `       <div>${message}</div>`,
            `   </div>`,
            '   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="this.parentElement.remove()"></button>',
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

    /**
     * Form Validation & Submission (Mockup)
     */
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (!form.checkValidity()) {
            event.stopPropagation();
            form.classList.add('was-validated');
            showAlert('Please ensure all required fields are correctly updated.', 'danger');
            return;
        }

        // Mocking record update
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
        
        setTimeout(() => {
            showAlert('Hospital details updated successfully! (UI Mockup)', 'success');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Hospital';
            form.classList.remove('was-validated');
            
            // Log for debug
            console.log('Record Updated - Data serialized:', new FormData(form));
        }, 1500);
    }, false);
});
</script>

<?php include '../includes/footer.php'; ?>
