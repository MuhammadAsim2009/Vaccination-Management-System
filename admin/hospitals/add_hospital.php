<?php
// Reusable includes
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../../config/functions.php';

$alert_msg = '';
$alert_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospital_name = $_POST['hospital_name'];
    $reg_no = $_POST['reg_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = 'hospital';
    $password = $_POST['password'];
    $status = $_POST['status'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if registration number already exists
    $stmt_check_reg = $conn->prepare("SELECT id FROM hospitals WHERE registration_no = ?");
    $stmt_check_reg->bind_param("s", $reg_no);
    $stmt_check_reg->execute();
    if ($stmt_check_reg->get_result()->num_rows > 0) {
        $alert_msg = "Registration Number already exists.";
        $alert_type = "danger";
    }
    $stmt_check_reg->close();

    // Check if email already exists
    if (empty($alert_msg)) {
        $stmt_check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check_email->bind_param("s", $email);
        $stmt_check_email->execute();
        if ($stmt_check_email->get_result()->num_rows > 0) {
            $alert_msg = "Email address is already in use.";
            $alert_type = "danger";
        }
        $stmt_check_email->close();
    }

    if (empty($alert_msg)) {
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Insert into users table
            $stmt_user = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES(?,?,?,?)");
            $stmt_user->bind_param("ssss", $hospital_name, $email, $hashed_password, $role);
            $stmt_user->execute();
            $user_id = $conn->insert_id;
            $stmt_user->close();

            // Insert into hospital table
            $stmt_hospital = $conn->prepare("INSERT INTO hospitals (user_id, hospital_name, registration_no, phone, address, status) VALUES(?,?,?,?,?,?)");
            $stmt_hospital->bind_param("isssss", $user_id, $hospital_name, $reg_no, $phone, $address, $status);
            $stmt_hospital->execute();
            $stmt_hospital->close();

            // Trigger Notification (Log Admin Action)
            $admin_id = $_SESSION['user_id'];
            $admin_name = $_SESSION['name'];
            $notif_title = "New Hospital Added";
            $notif_message = "Admin '$admin_name' manually added a new hospital: '" . htmlspecialchars($hospital_name) . "'.";
            send_notification($conn, 'admin', null, $admin_id, 'system', $notif_title, $notif_message);

            $conn->commit();
            $alert_msg = "Hospital registered successfully! Redirecting to list...";
            $alert_type = "success";
        } catch (Exception $e) {
            $conn->rollback();
            $alert_msg = "Registration Failed: " . $e->getMessage();
            $alert_type = "danger";
        }
    }
}


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
                    <form id="addHospitalForm" method="POST" class="needs-validation" novalidate>
                        <div class="row g-4">
                            <!-- Hospital Name -->
                            <div class="col-md-6">
                                <label for="hospitalName" class="form-label fw-semibold">Hospital Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-hospital text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0" id="hospitalName" name="hospital_name" placeholder="Enter Full Hospital Name" required>
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

                            <!-- Password Field -->
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 border-end-0" id="password" name="password" placeholder="Create password" required>
                                    <button class="btn btn-light border border-start-0" type="button" id="togglePassword">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Please provide a password.</div>
                            </div>

                            <!-- Status Dropdown -->
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Account Status</label>
                                <select class="form-select bg-light" id="status" name="status" required>
                                    <option value="" selected disabled>Select status...</option>
                                    <option value="approved">Accepted</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="pending">Pending</option>
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
                                <div class="d-flex justify-content-center flex-column flex-sm-row gap-3" >
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

    // Password Toggle Logic
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }

    // Trigger alerts from PHP state after form submission
    <?php if (!empty($alert_msg)): ?>
        showAlert(<?php echo json_encode($alert_msg); ?>, '<?php echo $alert_type; ?>');
        <?php if ($alert_type === 'success'): ?>
            // Redirect on success after a delay
            setTimeout(() => {
                window.location.href = 'hospital_list.php?msg=added';
            }, 2500);
        <?php endif; ?>
    <?php endif; ?>

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

    /**
     * Form Validation & Submission
     */
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            showAlert('Please fill in all required fields correctly.', 'danger');
        } else {
            // If form is valid, let it submit. The button will be disabled and show a spinner.
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
        }
        form.classList.add('was-validated');
    }, false);

    // Initial check to disable/manage submit button if empty (Optional requirement)
    // Here we use Bootstrap's standard 'was-validated' class for visual feedback
});
</script>

<?php include '../includes/footer.php'; ?>
