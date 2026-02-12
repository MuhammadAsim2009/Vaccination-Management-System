<?php
/**
 * Update Vaccine Details Page
 * Hospital Panel - Vaccination Management System
 * 
 * Allows hospital staff to edit existing vaccine information.
 * Uses dummy data to simulate a database record fetch.
 * 
 * @path /hospital/vaccines/update_vaccine.php
 */

// Essential includes
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// ---------------------------------------------------------
// DUMMY DATA (Simulating a fetched record from DB)
// ---------------------------------------------------------
$vaccine = [
    'id' => 'VAC-001',
    'name' => 'BCG Vaccine',
    'code' => 'VAC-001',
    'manufacturer' => 'Serum Institute of India',
    'age_group' => '0-1 Year',
    'doses' => 1,
    'description' => 'Primary vaccine for Tuberculosis protection in newborns. Administered intradermally.',
    'status' => 'Available',
    'stock' => 150,
    'expiry' => '2026-12-31'
];
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

        <!-- UI Alerts Placeholder -->
        <div class="row">
            <div class="col-12">
                <!-- Success Alert -->
                <div id="successAlert" class="alert alert-success d-none alert-dismissible fade show shadow-sm rounded-4 border-0" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-25 text-success rounded-circle p-2 me-3">
                            <i class="fas fa-check fs-5"></i>
                        </div>
                        <div>
                            <strong>Update Successful!</strong> The vaccine details have been saved.
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <form id="updateVaccineForm" class="needs-validation" novalidate>
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
                                <div class="col-md-6">
                                    <label for="vaccineName" class="form-label fw-semibold">Vaccine Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-syringe text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="vaccineName" value="<?= $vaccine['name'] ?>" required>
                                        <div class="invalid-feedback">Please enter vaccine name.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="vaccineCode" class="form-label fw-semibold">Vaccine Code</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0 bg-light" id="vaccineCode" value="<?= $vaccine['code'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="manufacturer" class="form-label fw-semibold">Manufacturer <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-industry text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="manufacturer" value="<?= $vaccine['manufacturer'] ?>" required>
                                        <div class="invalid-feedback">Manufacturer is required.</div>
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
                                        <select class="form-select border-start-0" id="ageGroup" required>
                                            <option value="">Select Age Group</option>
                                            <option value="0-1 Year" <?= $vaccine['age_group'] == '0-1 Year' ? 'selected' : '' ?>>0–1 Year</option>
                                            <option value="1-5 Years" <?= $vaccine['age_group'] == '1-5 Years' ? 'selected' : '' ?>>1–5 Years</option>
                                            <option value="5-12 Years" <?= $vaccine['age_group'] == '5-12 Years' ? 'selected' : '' ?>>5–12 Years</option>
                                            <option value="Adult" <?= $vaccine['age_group'] == 'Adult' ? 'selected' : '' ?>>Adult</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="doses" class="form-label fw-semibold">Doses Required <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-prescription-bottle text-muted"></i></span>
                                        <input type="number" class="form-control border-start-0" id="doses" min="1" value="<?= $vaccine['doses'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control" id="description" rows="3" placeholder="Enter vaccine details..."><?= $vaccine['description'] ?></textarea>
                                </div>
                            </div>

                            <!-- Inventory Section -->
                            <h6 class="text-uppercase text-muted small fw-bold mb-3 pt-2 border-top">Inventory & Status</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="stockQty" class="form-label fw-semibold">Stock Quantity</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-boxes text-muted"></i></span>
                                        <input type="number" class="form-control border-start-0" id="stockQty" value="<?= $vaccine['stock'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="expiryDate" class="form-label fw-semibold">Expiry Date</label>
                                    <input type="date" class="form-control" id="expiryDate" value="<?= $vaccine['expiry'] ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="status" class="form-label fw-semibold">Availability</label>
                                    <select class="form-select" id="status">
                                        <option value="Available" <?= $vaccine['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                                        <option value="Unavailable" <?= $vaccine['status'] == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mt-5 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm">
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
                                    <small class="text-white-50" id="previewCode"><?= $vaccine['code'] ?></small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-white-50 d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Manufacturer</small>
                                <span class="fw-medium" id="previewManufacturer"><?= $vaccine['manufacturer'] ?></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <small class="text-white-50 d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Current Stock</small>
                                    <h3 class="fw-bold mb-0" id="previewStock"><?= $vaccine['stock'] ?></h3>
                                </div>
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
                                <li><i class="fas fa-check text-success me-2"></i>Ensure stock count matches physical inventory.</li>
                                <li><i class="fas fa-check text-success me-2"></i>Update expiry dates promptly to trigger alerts.</li>
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
    const successAlert = document.getElementById('successAlert');

    // Live Preview Elements
    const inputs = {
        name: document.getElementById('vaccineName'),
        manufacturer: document.getElementById('manufacturer'),
        stock: document.getElementById('stockQty'),
        status: document.getElementById('status')
    };

    const previews = {
        name: document.getElementById('previewName'),
        manufacturer: document.getElementById('previewManufacturer'),
        stock: document.getElementById('previewStock'),
        status: document.getElementById('previewStatus')
    };

    // Live Update Logic
    Object.keys(inputs).forEach(key => {
        inputs[key].addEventListener('input', () => {
            previews[key].textContent = inputs[key].value || '-';
        });
    });

    // Handle Reset to update Live Preview
    form.addEventListener('reset', function() {
        setTimeout(() => {
            Object.keys(inputs).forEach(key => {
                previews[key].textContent = inputs[key].value || '-';
            });
        }, 0);
    });

    // Form Submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        event.stopPropagation();

        if (form.checkValidity()) {
            // Simulate API call
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

            setTimeout(() => {
                successAlert.classList.remove('d-none');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Updated';
                btn.classList.replace('btn-primary', 'btn-success');
                
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    btn.classList.replace('btn-success', 'btn-primary');
                }, 2000);
            }, 1000);
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php include '../includes/footer.php'; ?>