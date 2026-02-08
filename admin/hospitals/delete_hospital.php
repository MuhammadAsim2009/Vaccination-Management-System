<?php
/**
 * Vaccination Management System - Admin Module
 * delete_hospital.php: Permanently remove a hospital from the system.
 * 
 * Tech Stack: PHP, HTML5, CSS3, Bootstrap 5, JavaScript, Font Awesome.
 * Design: SaaS Admin UI, Danger Styling.
 */

// Reusable includes
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Dummy data for the hospital to be deleted (Simulating a fetch from DB)
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
            <h3 class="fw-bold text-dark mb-1">Delete Hospital</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="hospital_list.php" class="text-decoration-none text-primary">Hospitals</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Delete Hospital</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="hospital_list.php" class="btn btn-outline-secondary shadow-sm rounded-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Alert Placeholders -->
    <div id="alertPlaceholder"></div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            
            <!-- IRREVERSIBLE ACTION WARNING -->
            <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center py-4 mb-4" role="alert">
                <i class="fas fa-exclamation-triangle fs-2 me-4 text-danger animate-pulse"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Critical Action Warning</h5>
                    <p class="mb-0 text-dark opacity-75">This action is <strong>irreversible</strong> and will permanently remove all data associated with this hospital. Please confirm before proceeding.</p>
                </div>
            </div>

            <!-- HOSPITAL DETAILS CARD (READONLY) -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center">
                    <i class="fas fa-hospital-alt me-2 text-danger fs-4"></i>
                    <h3 class="mb-0 fw-bold text-dark">Hospital Information</h3>
                    <span class="badge bg-danger ms-auto fw-semibold px-3 py-2 text-white">RECORD FOR DELETION</span>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Hospital Name</label>
                            <p class="fw-semibold text-dark mb-0 fs-5"><?php echo htmlspecialchars($hospital['name']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Registration Number</label>
                            <p class="fw-semibold text-dark mb-0 fs-5 text-primary"><?php echo htmlspecialchars($hospital['reg_no']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Email Address</label>
                            <p class="text-dark mb-0"><?php echo htmlspecialchars($hospital['email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Phone Number</label>
                            <p class="text-dark mb-0"><?php echo htmlspecialchars($hospital['phone']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Current Status</label>
                            <div>
                                <span class="badge <?php echo $hospital['status'] === 'Accepted' ? 'bg-success' : ($hospital['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark'); ?> rounded-pill px-3 py-2">
                                    <?php echo htmlspecialchars($hospital['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Address</label>
                            <p class="text-muted mb-0 small"><?php echo htmlspecialchars($hospital['address']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONFIRMATION & ACTION SECTION -->
            <div class="card border-0 shadow-sm rounded-3 bg-light border-start border-danger border-4">
                <div class="card-body p-4">
                    <form id="deleteHospitalForm">
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                            <label class="form-check-label text-dark fw-bold" for="confirmDelete" style="cursor: pointer;">
                                I understand that this action cannot be undone and I want to proceed.
                            </label>
                        </div>
                        
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            <button type="submit" class="btn btn-danger px-5 py-2 rounded-3 fw-bold shadow-none" id="deleteBtn" disabled>
                                <i class="fas fa-trash-alt me-2"></i>Delete Hospital
                            </button>
                            <a href="hospital_list.php" class="btn btn-secondary px-5 py-2 rounded-3 fw-bold border-0">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center mt-5 text-muted small">
                <i class="fas fa-shield-alt me-1"></i> Administratively restricted action.
            </p>
        </div>
    </div>
</div>

<style>
.animate-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<script>
/**
 * UI Interactions for Delete Hospital Page
 */
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirmDelete');
    const deleteBtn = document.getElementById('deleteBtn');
    const form = document.getElementById('deleteHospitalForm');
    const alertPlaceholder = document.getElementById('alertPlaceholder');

    /**
     * Enable/Disable delete button based on checkbox
     */
    confirmCheckbox.addEventListener('change', function() {
        deleteBtn.disabled = !this.checked;
    });

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
    }

    /**
     * Form Submission (Mockup)
     */
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (!confirmCheckbox.checked) return;

        // Mocking deletion progress
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
        
        setTimeout(() => {
            showAlert('Hospital record has been permanently deleted. (UI Mockup)', 'success');
            
            // Re-style UI to show empty state or redirect
            setTimeout(() => {
                window.location.href = 'hospital_list.php?msg=deleted';
            }, 2000);
        }, 1500);
    });
});
</script>

<?php include '../includes/footer.php'; ?>
