<?php
/**
 * Update Vaccine Availability Status
 * 
 * Purpose: Update availability of an existing vaccine only.
 * Fields other than status are readonly.
 */

// Reusable Includes
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Get vaccine details from URL (Simulated data transfer between pages)
$vaccineId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 'VAC000';
$vaccineName = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Unknown Vaccine';
$ageGroup = isset($_GET['age']) ? htmlspecialchars($_GET['age']) : 'N/A';
$doseCount = isset($_GET['doses']) ? htmlspecialchars($_GET['doses']) : '0';
$currentStatus = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : 'Available';
?>

<!-- ============================================
     Main Content
     ============================================ -->
<main class="main-content">
    <div class="container-fluid px-4">
        
        <!-- Page Header & Breadcrumb -->
        <div class="d-flex align-items-center justify-content-between mb-4 mt-4">
            <div>
                <h3 class="fw-bold mb-1">Update Vaccine Availability</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="vaccine_list.php" class="text-decoration-none">Vaccines</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Update Status</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="vaccine_list.php" class="btn btn-light border d-flex align-items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to List</span>
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                
                <!-- Status Update Form Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-primary py-3">
                        <h5 class="card-title mb-0 text-white fw-bold d-flex align-items-center">
                            <i class="fas fa-vial me-2"></i>
                            <span>Update Status: <?= $vaccineId ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- UI-Only Success Alert -->
                        <div id="successAlert" class="alert alert-success alert-dismissible fade show d-none border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex">
                                <i class="fas fa-check-circle fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">Update Successful!</h6>
                                    <p class="mb-0 small">The availability status for <strong><?= $vaccineName ?></strong> has been updated. Redirecting...</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <form id="updateStatusForm" onsubmit="handleFormSubmission(event)">
                            <div class="row g-4">
                                
                                <!-- Readonly: Vaccine Name -->
                                <div class="col-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Vaccine Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-file-medical text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-start-0 fw-semibold" value="<?= $vaccineName ?>" readonly>
                                    </div>
                                </div>

                                <!-- Readonly: Details Row -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Target Age Group</label>
                                    <input type="text" class="form-control bg-light" value="<?= $ageGroup ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Required Doses</label>
                                    <input type="text" class="form-control bg-light" value="<?= $doseCount ?>" readonly>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2 opacity-10">
                                </div>

                                <!-- Dropdown: Availability Status -->
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Current Availability Status</label>
                                    <div class="position-relative">
                                        <select class="form-select form-select-lg border-2 border-primary border-opacity-10 fw-medium" 
                                                id="statusDropdown" 
                                                name="availability_status" 
                                                required>
                                            <option value="Available" <?= $currentStatus == 'Available' ? 'selected' : '' ?>>
                                                🟢 Available (Recommended)
                                            </option>
                                            <option value="Unavailable" <?= $currentStatus == 'Unavailable' ? 'selected' : '' ?>>
                                                🔴 Unavailable (Stop new bookings)
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-text mt-2 text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Hospitals will see the updated status in real-time.
                                    </div>
                                </div>

                                <!-- Optional Remarks -->
                                <div class="col-12 mt-3">
                                    <label class="form-label fw-bold text-dark">Reason for Change (Optional)</label>
                                    <textarea class="form-control border-opacity-25" 
                                              rows="3" 
                                              placeholder="e.g., Stock out, Batch shipment delayed, Low demand..."></textarea>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-12 mt-4">
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-3">
                                        <i class="fas fa-save me-2"></i>
                                        <span>Update Vaccine Status</span>
                                    </button>
                                    <a href="vaccine_list.php" class="btn btn-link w-100 text-decoration-none text-muted">
                                        Discard Changes and Return
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- Guidance Alert -->
                <div class="alert bg-white border shadow-sm rounded-4 p-4 d-flex align-items-start gap-4">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="fas fa-shield-alt fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Stock Management Note</h6>
                        <p class="text-muted small mb-0">
                            Setting a vaccine to 'Unavailable' automatically halts all pending appointment requests for that specific vaccine until stock is restored.
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>

<script>
/**
 * Handle form submission with UI feedback
 */
function handleFormSubmission(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalContent = submitBtn.innerHTML;
    
    // Disable interaction
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        <span>Processing Update...</span>
    `;

    // Simulate Network Latency
    setTimeout(() => {
        // Show Success UI
        const successAlert = document.getElementById('successAlert');
        successAlert.classList.remove('d-none');
        
        // Reset Button
        submitBtn.innerHTML = `<i class="fas fa-check me-2"></i> Status Updated!`;
        submitBtn.classList.replace('btn-primary', 'btn-success');

        // Redirect after delay
        setTimeout(() => {
            window.location.href = 'vaccine_list.php';
        }, 1800);
        
    }, 1200);
}
</script>

<?php include '../includes/footer.php'; ?>
