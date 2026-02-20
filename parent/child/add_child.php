<?php

// Include authentication and layout files
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../../config/functions.php';

// Fetch parent information
$stmt_parent = $conn->prepare("SELECT u.name, u.email, p.phone FROM parents p JOIN users u ON p.user_id = u.id WHERE p.user_id = ?");
$stmt_parent->bind_param("i", $_SESSION['user_id']);
$stmt_parent->execute();
$result_parent = $stmt_parent->get_result();
$parent = $result_parent->fetch_assoc();

// Parent Data
$parent_name = $parent['name'];
$parent_email = $parent['email'];
$parent_number = $parent['phone'];


// Handle form submission
if(isset($_POST['add_child'])) {

    // Get form data
    $parent_id = $_SESSION['user_id'];
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];

    // Insert child data into database
    $stmt = $conn->prepare("INSERT INTO children (parent_id, name, date_of_birth, gender, blood_group) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $parent_id, $full_name, $dob, $gender, $blood_group);

    if($stmt->execute()) {
        // Trigger Notification to Admin
        $child_id = $conn->insert_id;
        $notif_title = "New Child Added";
        $notif_message = "Parent " . htmlspecialchars($parent_name) . " added a new child: " . htmlspecialchars($full_name) . ".";
        send_notification($conn, 'admin', null, $parent_id, 'system', $notif_title, $notif_message);

        // Show success alert and redirect to children list after a short delay
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('successAlert').classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });

            setTimeout(function(){
                window.location.href = 'children_list.php';
            }, 5000);
        });
        </script>";
    } else {
        echo "<script>alert('DB Error: ".$stmt->error."');</script>";
    }

    $stmt->close();
}

?>

<!-- Main Content Container -->
<div class="container-fluid px-4">

    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Add Child</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="children_list.php" class="text-decoration-none">Children</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Child</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="children_list.php" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Registration Form Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-baby-carriage me-2"></i>New Child Registration
            </h5>
        </div>
        <div class="card-body p-4">
            
            <!-- Success Alert (Hidden by default) -->
            <div id="successAlert" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fs-4 me-3"></i>
                    <div>
                        <strong>Success!</strong> Child profile has been created successfully.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <form id="addChildForm" class="needs-validation" method="post">
                
                <!-- Section 1: Child Basic Information -->
                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2">
                    <i class="fas fa-id-card me-2"></i>Basic Information
                </h6>
                
                <div class="row g-3 mb-4">
                    <!-- Full Name -->
                    <div class="col-md-6">
                        <label for="childName" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" class="form-control" name="full_name" id="childName" placeholder="e.g. Sarah Ahmed" required>
                            <div class="invalid-feedback">Please enter the child's full name.</div>
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="col-md-3">
                        <label for="dob" class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="dob" class="form-control" id="dob" required>
                        <div class="invalid-feedback">Please select date of birth.</div>
                    </div>

                    <!-- Age (Auto-calculated) -->
                    <div class="col-md-3">
                        <label for="age" class="form-label fw-semibold">Age</label>
                        <input type="text" class="form-control bg-light" id="age" placeholder="Auto-calculated" readonly>
                    </div>

                    <!-- Gender -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold d-block">Gender <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male" required>
                                <label class="form-check-label" for="genderMale"><i class="fas fa-mars text-primary me-1"></i>Male</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female" required>
                                <label class="form-check-label" for="genderFemale"><i class="fas fa-venus text-danger me-1"></i>Female</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderOther" value="Other" required>
                                <label class="form-check-label" for="genderOther">Other</label>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block" id="genderError" style="display:none !important">Please select a gender.</div>
                    </div>

                    <!-- Blood Group -->
                    <div class="col-md-6">
                        <label for="bloodGroup" class="form-label fw-semibold">Blood Group <span class="text-danger">*</span></label>
                        <select class="form-select" name="blood_group" id="bloodGroup" required>
                            <option value="" selected disabled>Select Blood Group</option>
                            <option value="A+">A+</option>
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
                            <input type="text" class="form-control bg-light border-start-0" value="<?php echo htmlspecialchars($parent_name); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">Contact Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" value="<?php echo htmlspecialchars($parent_number); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" value="<?php echo htmlspecialchars($parent_email); ?>" readonly>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Vaccination Plan -->
                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2 mt-4">
                    <i class="fas fa-syringe me-2"></i>Vaccination Plan
                </h6>

                <div class="bg-light p-3 rounded-3 mb-4">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="" id="defaultSchedule" checked disabled>
                        <label class="form-check-label fw-bold text-dark" for="defaultSchedule">
                            National Immunization Schedule (Mandatory)
                        </label>
                        <div class="form-text small">Includes BCG, OPV, Pentavalent, Pneumococcal, Measles, etc.</div>
                    </div>
                    
                    <hr class="my-2">
                    
                    <label class="form-label fw-semibold small text-muted mt-2">Optional Vaccines:</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="optFlu">
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
                                <input class="form-check-input" type="checkbox" value="" id="optHepA">
                                <label class="form-check-label" for="optHepA">
                                    Hepatitis A
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Submit Actions -->
                <div class="d-flex gap-2 mt-5">
                    <button type="button" class="btn btn-light border flex-fill" onclick="window.history.back()">Cancel</button>
                    <button type="reset" class="btn btn-outline-secondary flex-fill">Reset Form</button>
                    <button type="submit" name="add_child" class="btn btn-primary flex-fill">
                        <i class="fas fa-save me-2"></i>Save Child
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

    // 2. Age Auto-Calculation
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');

    dobInput.addEventListener('change', function() {
        if(this.value) {
            const dob = new Date(this.value);
            const today = new Date();
            
            let years = today.getFullYear() - dob.getFullYear();
            let months = today.getMonth() - dob.getMonth();
            
            // Adjust if birth month hasn't happened yet this year
            if (months < 0 || (months === 0 && today.getDate() < dob.getDate())) {
                years--;
                months += 12;
            }
            
            // Adjust days for accurate month calculation
            if (today.getDate() < dob.getDate()) {
                months--;
                if (months < 0) {
                    months += 12;
                    // If years was adjusted down previously, we might need logic check here, 
                    // but for simple UI display:
                }
            }

            let ageString = '';
            if (years > 0) {
                ageString += years + (years === 1 ? ' year' : ' years');
            }
            
            if (years < 5) { // Show months for children under 5
                if (years > 0 && months > 0) ageString += ', ';
                if (months > 0 || years === 0) {
                    ageString += months + (months === 1 ? ' month' : ' months');
                }
            }
            
            if (years === 0 && months === 0) {
                ageString = 'Newborn';
            }

            ageInput.value = ageString;
        } else {
            ageInput.value = '';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
