<?php
/**
 * Update Child Details Page
 * Allows parents to update/edit their child’s registered information.
 * 
 * Path: parent/child/update_child.php
 */

// Include authentication and layout files
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<!-- Main Content Container -->
<div class="container-fluid px-4 py-4">

    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Update Child Details</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="children_list.php" class="text-decoration-none">Children</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update Child</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="children_list.php" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Update Form Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-user-edit me-2"></i>Edit Profile: Sarah Ahmed
                </h5>
                <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25">
                    ID: CH-2026-001
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            
            <!-- Success Alert (Hidden by default) -->
            <div id="successAlert" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fs-4 me-3"></i>
                    <div>
                        <strong>Update Successful!</strong> Child details have been updated.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <form id="updateChildForm" class="needs-validation" novalidate>
                
                <!-- Section 1: Child Basic Information -->
                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2">
                    <i class="fas fa-id-card me-2"></i>Basic Information
                </h6>
                
                <div class="row g-3 mb-4">
                    <!-- Child ID (Readonly) -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted small">Child ID</label>
                        <input type="text" class="form-control bg-light" value="CH-001" readonly>
                    </div>

                    <!-- Full Name -->
                    <div class="col-md-5">
                        <label for="childName" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" class="form-control" id="childName" value="Sarah Ahmed" required>
                            <div class="invalid-feedback">Please enter the child's full name.</div>
                        </div>
                    </div>

                    <!-- Blood Group -->
                    <div class="col-md-5">
                        <label for="bloodGroup" class="form-label fw-semibold">Blood Group <span class="text-danger">*</span></label>
                        <select class="form-select" id="bloodGroup" required>
                            <option value="">Select Blood Group</option>
                            <option value="A+" selected>A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                        <div class="invalid-feedback">Please select a blood group.</div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="col-md-4">
                        <label for="dob" class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dob" value="2021-01-15" required>
                        <div class="invalid-feedback">Please select date of birth.</div>
                    </div>

                    <!-- Age (Auto-calculated) -->
                    <div class="col-md-4">
                        <label for="age" class="form-label fw-semibold">Current Age</label>
                        <input type="text" class="form-control bg-light" id="age" readonly>
                    </div>

                    <!-- Gender -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold d-block">Gender <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male">
                                <label class="form-check-label" for="genderMale"><i class="fas fa-mars text-primary me-1"></i>Male</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female" checked>
                                <label class="form-check-label" for="genderFemale"><i class="fas fa-venus text-danger me-1"></i>Female</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderOther" value="Other">
                                <label class="form-check-label" for="genderOther">Other</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Parent Information (Readonly) -->
                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2 mt-4">
                    <i class="fas fa-user-shield me-2"></i>Parent Information
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">Parent Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-circle text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" value="John Doe" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">Contact Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" value="+1 (555) 123-4567" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" value="john.doe@example.com" readonly>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Vaccination Plan -->
                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2 mt-4">
                    <i class="fas fa-syringe me-2"></i>Vaccination Plan
                </h6>

                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="" id="defaultSchedule" checked disabled>
                        <label class="form-check-label fw-bold text-dark" for="defaultSchedule">
                            National Immunization Schedule (Mandatory)
                        </label>
                        <div class="form-text small">Includes BCG, OPV, Pentavalent, Pneumococcal, Measles, etc.</div>
                    </div>
                    
                    <hr class="my-2">
                    
                    <label class="form-label fw-semibold small text-muted mt-2">Optional Vaccines (Update):</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="optFlu" checked>
                                <label class="form-check-label" for="optFlu">
                                    Influenza (Flu Shot)
                                    <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Recommended annually"></i>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="optChickenpox">
                                <label class="form-check-label" for="optChickenpox">
                                    Varicella (Chickenpox)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="optHepA" checked>
                                <label class="form-check-label" for="optHepA">
                                    Hepatitis A
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Action Buttons -->
                <div class="d-flex gap-2 mt-5">
                    <a href="children_list.php" class="btn btn-light border flex-fill">Cancel</a>
                    <button type="reset" class="btn btn-outline-secondary flex-fill">Reset Changes</button>
                    <button type="submit" class="btn btn-primary flex-fill shadow-sm">
                        <i class="fas fa-save me-2"></i>Update Child Details
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Initialize Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // 2. Age Auto-Calculation Logic
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');

    function calculateAge() {
        if(dobInput.value) {
            const dob = new Date(dobInput.value);
            const today = new Date();
            let years = today.getFullYear() - dob.getFullYear();
            let months = today.getMonth() - dob.getMonth();
            if (months < 0 || (months === 0 && today.getDate() < dob.getDate())) {
                years--;
                months += 12;
            }
            if (today.getDate() < dob.getDate()) {
                months--;
                if (months < 0) months += 12;
            }
            let ageString = years > 0 ? years + (years === 1 ? ' year' : ' years') : '';
            if (years < 5) {
                if (years > 0 && months > 0) ageString += ', ';
                if (months > 0 || years === 0) ageString += months + (months === 1 ? ' month' : ' months');
            }
            if (years === 0 && months === 0) ageString = 'Newborn';
            ageInput.value = ageString;
        } else {
            ageInput.value = '';
        }
    }

    // Calculate on load and on change
    calculateAge();
    dobInput.addEventListener('change', calculateAge);

    // 3. Form Validation & Submission Mockup
    const form = document.getElementById('updateChildForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        event.stopPropagation();

        if (form.checkValidity()) {
            // Simulate API call / Processing
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

            setTimeout(function() {
                // Show Success Alert
                document.getElementById('successAlert').classList.remove('d-none');
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Reset button state
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Updated!';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');

                // Redirect simulation
                setTimeout(() => {
                    window.location.href = 'children_list.php';
                }, 2000);
            }, 1500);
        }

        form.classList.add('was-validated');
    }, false);
});
</script>

<?php include '../includes/footer.php'; ?>