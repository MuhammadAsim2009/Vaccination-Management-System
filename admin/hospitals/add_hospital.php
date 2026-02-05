<?php
/**
 * Vaccination Management System - Admin Module
 * add_hospital.php: Manually add a new hospital to the system.
 * 
 * Tech Stack: PHP, HTML5, CSS3, Bootstrap 5, JavaScript, Font Awesome.
 * Design: SaaS Admin UI, Medical/Healthcare Theme.
 */

// Reusable includes
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content p-4">
    <!-- Breadcrumb & Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Add Hospital</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="hospital_list.php" class="text-decoration-none text-primary">Hospitals</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Hospital</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="hospital_list.php" class="btn btn-outline-secondary shadow-sm rounded-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-center py-3 mb-4" role="alert">
        <i class="fas fa-info-circle fs-4 me-3 text-info"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Manual Entry Notice</h6>
            <p class="mb-0 small text-dark opacity-75">Hospitals usually self-register. Use this form only for manual or emergency entries.</p>
        </div>
    </div>

    <!-- Success/Error Alert Placeholders (Hidden by default) -->
    <div id="alertPlaceholder"></div>

    <!-- Form Section -->
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h3 class="mb-0 m-3 fw-bold text-dark">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>Add Hospital
                    </h3>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form id="addHospitalForm" class="needs-validation" novalidate>
                        <div class="row g-4">
                            <!-- Hospital Name -->
                            <div class="col-md-6">
                                <label for="hospitalName" class="form-label fw-semibold">Hospital Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-hospital text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0" id="hospitalName" name="name" placeholder="Enter Full Hospital Name" required>
                                </div>
                                <div class="invalid-feedback">Please provide a hospital name.</div>
                            </div>

                            <!-- Registration Number -->
                            <div class="col-md-6">
                                <label for="regNumber" class="form-label fw-semibold">Registration Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0" id="regNumber" name="reg_no" placeholder="REG-YYYY-XXXX" required>
                                </div>
                                <div class="invalid-feedback">Please provide a registration number.</div>
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0" id="email" name="email" placeholder="hospital@example.com" required>
                                </div>
                                <div class="invalid-feedback">Please provide a valid email address.</div>
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="tel" class="form-control bg-light border-start-0" id="phone" name="phone" placeholder="+1 (XXX) XXX-XXXX" required>
                                </div>
                                <div class="invalid-feedback">Please provide a phone number.</div>
                            </div>

                            <!-- Status Dropdown -->
                            <div class="col-md-12">
                                <label for="status" class="form-label fw-semibold">Account Status</label>
                                <select class="form-select bg-light" id="status" name="status" required>
                                    <option value="" selected disabled>Select status...</option>
                                    <option value="Accepted">Accepted</option>
                                    <option value="Rejected">Rejected</option>
                                    <option value="Pending">Pending</option>
                                </select>
                                <div class="invalid-feedback">Please select an account status.</div>
                            </div>

                            <!-- Full Address -->
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Office Address</label>
                                <textarea class="form-control bg-light" id="address" name="address" rows="3" placeholder="Enter full physical address..." required></textarea>
                                <div class="invalid-feedback">Please provide the address.</div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 mt-5">
                                <hr class="my-4 opacity-50">
                                <div class="d-flex justify-content-center flex-column flex-sm-row gap-3">
                                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-none fw-bold" id="submitBtn">
                                        <i class="fas fa-plus-circle me-2"></i>Add Hospital
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
                <i class="fas fa-lock me-1"></i> Data entry is secured and logged for audit purposes.
            </p>
        </div>
    </div>
</div>

<script>
/**
 * UI Interactions for Add Hospital Page
 */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addHospitalForm');
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
            showAlert('Please fill in all required fields correctly.', 'danger');
            return;
        }

        // Mocking record addition
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
        
        setTimeout(() => {
            showAlert('Hospital record added successfully! (UI Mockup)', 'success');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add Hospital';
            form.reset();
            form.classList.remove('was-validated');
            
            // Log for debug
            console.log('Record Submitted - Redirect logic would go here in backend.');
        }, 1500);
    }, false);

    // Initial check to disable/manage submit button if empty (Optional requirement)
    // Here we use Bootstrap's standard 'was-validated' class for visual feedback
});
</script>

<?php include '../includes/footer.php'; ?>
