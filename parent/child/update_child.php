<?php
// Include authentication and layout files
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../../config/functions.php';

// Initialize variables
$child = null;
$alert_msg = '';
$alert_type = '';
$parent_id = $_SESSION['user_id'];

// 1. Get Child ID from URL and validate
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    // Redirect if ID is missing or invalid
    header("Location: children_list.php?error=invalid_id");
    exit();
}
$child_id = (int)$_GET['id'];

// 2. Handle Form Submission (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $posted_child_id = filter_input(INPUT_POST, 'child_id', FILTER_VALIDATE_INT);
    $full_name = trim($_POST['full_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];

    // Security check: Ensure the submitted child ID matches the one in the URL and belongs to the parent
    if ($posted_child_id && $posted_child_id === $child_id) {
        $stmt_update = $conn->prepare("UPDATE children SET name = ?, date_of_birth = ?, gender = ?, blood_group = ? WHERE id = ? AND parent_id = ?");
        $stmt_update->bind_param("ssssii", $full_name, $dob, $gender, $blood_group, $child_id, $parent_id);

        if ($stmt_update->execute()) {
            // Trigger Notification to Admin
            $parent_name = $_SESSION['name'];
            send_notification($conn, 'admin', null, $parent_id, 'system', 'Child Profile Updated', "Parent '$parent_name' updated details for child '$full_name'.");

            $alert_msg = "Child details have been updated successfully!";
            $alert_type = "success";
        } else {
            $alert_msg = "Error updating details: " . $conn->error;
            $alert_type = "danger";
        }
        $stmt_update->close();
    } else {
        $alert_msg = "Invalid request. Please try again.";
        $alert_type = "danger";
    }
}

// 3. Fetch Child and Parent Data for display (GET request or after POST)
$stmt_fetch = $conn->prepare("SELECT c.*, u.name as parent_name, u.email as parent_email, p.phone as parent_phone FROM children c JOIN users u ON c.parent_id = u.id LEFT JOIN parents p ON u.id = p.user_id WHERE c.id = ? AND c.parent_id = ?");
$stmt_fetch->bind_param("ii", $child_id, $parent_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
$child = $result->fetch_assoc();
$stmt_fetch->close();

// If child not found or doesn't belong to parent, redirect
if (!$child) {
    header("Location: children_list.php?error=not_found");
    exit();
}

// Blood group options for the dropdown
$blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
?>

<!-- Main Content Container -->
<div class="container-fluid px-4">

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
                    <i class="fas fa-user-edit me-2"></i>Edit Profile: <?= htmlspecialchars($child['name']) ?>
                </h5>
                <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25">
                    ID: CH-<?= htmlspecialchars($child['id']) ?>
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            
            <!-- Success/Error Alert -->
            <?php if (!empty($alert_msg)): ?>
            <div id="alertMessage" class="alert alert-<?= $alert_type ?> alert-dismissible fade show" role="alert">
                <?= $alert_msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <form id="updateChildForm" class="needs-validation" method="POST" novalidate>
                <input type="hidden" name="child_id" value="<?= htmlspecialchars($child['id']) ?>">
                
                <!-- Section 1: Child Basic Information -->
                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2">
                    <i class="fas fa-id-card me-2"></i>Basic Information
                </h6>
                
                <div class="row g-3 mb-4">
                    <!-- Child ID (Readonly) -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted small">Child ID</label>
                        <input type="text" class="form-control bg-light" value="CH-<?= htmlspecialchars($child['id']) ?>" readonly>
                    </div>

                    <!-- Full Name -->
                    <div class="col-md-5">
                        <label for="childName" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" class="form-control" id="childName" name="full_name" value="<?= htmlspecialchars($child['name']) ?>" required>
                            <div class="invalid-feedback">Please enter the child's full name.</div>
                        </div>
                    </div>

                    <!-- Blood Group -->
                    <div class="col-md-5">
                        <label for="bloodGroup" class="form-label fw-semibold">Blood Group <span class="text-danger">*</span></label>
                        <select class="form-select" id="bloodGroup" name="blood_group" required>
                            <option value="" disabled>Select Blood Group</option>
                            <?php foreach ($blood_groups as $group): ?>
                                <option value="<?= $group ?>" <?= ($child['blood_group'] == $group) ? 'selected' : '' ?>><?= $group ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a blood group.</div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="col-md-4">
                        <label for="dob" class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dob" name="dob" value="<?= htmlspecialchars($child['date_of_birth']) ?>" required>
                        <div class="invalid-feedback">Please select date of birth.</div>
                    </div>

                    <!-- Age (Auto-calculated) -->
                    <div class="col-md-4">
                        <label for="age" class="form-label fw-semibold">Current Age</label>
                        <input type="text" class="form-control bg-light" id="age" placeholder="Auto-calculated" readonly>
                    </div>
                    
                    <!-- Gender -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold d-block">Gender <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male" <?= ($child['gender'] == 'Male') ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="genderMale"><i class="fas fa-mars text-primary me-1"></i>Male</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female" <?= ($child['gender'] == 'Female') ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="genderFemale"><i class="fas fa-venus text-danger me-1"></i>Female</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderOther" value="Other" <?= ($child['gender'] == 'Other') ? 'checked' : '' ?> required>
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
                            <input type="text" class="form-control bg-light border-start-0" value="<?= htmlspecialchars($child['parent_name']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">Contact Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" value="<?= htmlspecialchars($child['parent_phone']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" value="<?= htmlspecialchars($child['parent_email']) ?>" readonly>
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

    // 3. Form Validation
    const form = document.getElementById('updateChildForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    // 4. Redirect on successful update
    <?php if ($alert_type === 'success'): ?>
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Updated!';
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-success');
        
        setTimeout(() => {
            window.location.href = 'children_list.php';
        }, 2000);
    <?php endif; ?>
});
</script>

<?php include '../includes/footer.php'; ?>