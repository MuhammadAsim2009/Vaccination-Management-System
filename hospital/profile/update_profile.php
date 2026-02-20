<?php
// Essential includes for authentication, header, sidebar, and database connection
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include_once '../../config/functions.php';

// Get hospital ID from session
$id = $_SESSION['user_id'];

// Fetch hospital profile data
$stmt_hospitals = $conn->prepare("SELECT h.id AS hospital_id, h.hospital_name, h.registration_no, u.email, h.phone, h.address, h.status FROM hospitals h LEFT JOIN users u ON h.user_id = u.id WHERE u.id = ?");
$stmt_hospitals->bind_param("i", $id);
$stmt_hospitals->execute();
$result_hospitals = $stmt_hospitals->get_result();
$row = $result_hospitals->fetch_assoc();

// Prepare hospital profile data for easy access in the form
$hospital_profile = [
    'hospital_id' => $row['hospital_id'],
    'name' => $row['hospital_name'],
    'registration_no' => $row['registration_no'],
    'email' => $row['email'],
    'phone' => $row['phone'],
    'address' => $row['address'],
    'username' => $row['email'],
    'status' => $row['status'],
    'user_status' => $row['status']
];

$alert_msg = '';
$alert_type = '';

// Handle form submission for profile update
if(isset($_POST['update_btn'])) {
    // Get form data
    $hospital_id = $_POST['hospital_id'];
    $hospital_name = $_POST['hospital_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Update hospital profile in the database
    $stmt_update = $conn->prepare("UPDATE hospitals SET hospital_name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt_update->bind_param("sssi", $hospital_name, $phone, $address, $hospital_id);
    if($stmt_update->execute()) {
        // Trigger Notification to Admin
        $notif_title = "Hospital Profile Updated";
        $notif_message = "Hospital '" . htmlspecialchars($hospital_name) . "' updated their profile details.";
        send_notification($conn, 'admin', null, $id, 'system', $notif_title, $notif_message);

        $alert_msg = "Profile updated successfully.";
        $alert_type = "success";
        // Update local array to reflect changes immediately
        $hospital_profile['name'] = $hospital_name;
        $hospital_profile['phone'] = $phone;
        $hospital_profile['address'] = $address;
    } else {
        $alert_msg = "Error updating profile: " . $conn->error;
        $alert_type = "danger";
    }
}

// Update password logic
if(isset($_POST['update_password_btn'])) {
    // Get form data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate new password and confirmation
    if($new_password !== $confirm_password) {
        $alert_msg = "Passwords do not match.";
        $alert_type = "danger";
    } else {
        // Fetch current password hash from database
        $check_password = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $check_password->bind_param("i", $id);
        $check_password->execute();
        $result_password = $check_password->get_result();
        $row = $result_password->fetch_assoc();

        // Verify current password
        if(password_verify($current_password, $row['password'])) {
            // Hash new password and update in database
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_password->bind_param("si", $new_password_hash, $id);
            if($update_password->execute()) {
                // Trigger Notification to Admin
                $hospital_name = $_SESSION['name'];
                $notif_title = "Hospital Password Changed";
                $notif_message = "Hospital '$hospital_name' has changed their password.";
                send_notification($conn, 'admin', null, $id, 'system', $notif_title, $notif_message);

                $alert_msg = "Password updated successfully.";
                $alert_type = "success";
            } else {
                $alert_msg = "Error updating password: " . $conn->error;
                $alert_type = "danger";
            }
        } else {
            $alert_msg = "Current password is incorrect.";
            $alert_type = "danger";
        }

    }
}


?>

<!-- Main Content -->
<main class="mt-5 pt-3">
    <div class="container-fluid">

        <!-- 1. Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item">Profile</li>
                        <li class="breadcrumb-item active" aria-current="page">Update Profile</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <h2 class="fw-bold text-primary mb-0">Hospital Profile</h2>
                            <p class="text-muted mb-0">Manage your facility's information and settings.</p>
                        </div>
                    </div>
                    <a href="../dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- UI Alerts Placeholder -->
        <div class="row">
            <div class="col-12" id="alertPlaceholder"></div>
        </div>

        <form id="updateProfileForm" class="needs-validation" method="POST" novalidate>
            <input type="hidden" name="hospital_id" value="<?= htmlspecialchars($hospital_profile['hospital_id']) ?>">
            <div class="row g-4">

                <!-- Left Column: Profile & Security Forms -->
                <div class="col-12">
                    <!-- 2. Profile Information Form -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2 text-primary"></i>Profile Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <!-- Basic Info -->
                                <div class="col-md-6">
                                    <label for="hospitalName" class="form-label fw-semibold">Hospital Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="hospitalName" name="hospital_name" value="<?= htmlspecialchars($hospital_profile['name']) ?>" required>
                                    <div class="invalid-feedback">Hospital name is required.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="regNumber" class="form-label fw-semibold">Registration Number</label>
                                    <input type="text" class="form-control bg-light" name="reg_no" id="regNumber" value="<?= htmlspecialchars($hospital_profile['registration_no']) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control bg-light" id="email" value="<?= htmlspecialchars($hospital_profile['email']) ?>" readonly>
                                    <div class="invalid-feedback">Please enter a valid email.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($hospital_profile['phone']) ?>" required>
                                </div>

                                <!-- Address Info -->
                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">Full Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($hospital_profile['address']) ?></textarea>
                                </div>

                                <!-- Account Info -->
                                <div class="col-md-6">
                                    <label for="username" class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control bg-light" name="username" id="username" value="<?= htmlspecialchars($hospital_profile['username']) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Account Status</label>
                                    <div>
                                        <span class="badge bg-soft-success text-success rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($hospital_profile['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 text-end d-flex justify-content-end gap-3">
                                <button type="reset" class="btn btn-outline-secondary btn-md px-4">
                                    <i class="fas fa-undo me-2"></i>Reset Changes
                                </button>
                                <button type="submit" name="update_btn" class="btn btn-primary btn-md fw-bold px-4 ms-2">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Security Section -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-shield-alt me-2 text-danger"></i>Security</h5>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="text-muted fw-bold mb-3">Change Password</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="currentPassword" class="form-label fw-semibold">Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" class="form-control border-start-0" name="current_password" id="currentPassword" placeholder="Enter your current password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="newPassword" class="form-label fw-semibold">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-key text-muted"></i></span>
                                        <input type="password" class="form-control border-start-0" name="new_password" id="newPassword" placeholder="Enter new password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirmPassword" class="form-label fw-semibold">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" id="confirmPassword" placeholder="Confirm new password">
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="update_password_btn" class="btn btn-outline-danger rounded-pill px-4">
                                        <i class="fas fa-key me-2"></i>Update Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- CACHE DOM ELEMENTS ---
    const form = document.getElementById('updateProfileForm');

    // Form Submission Logic
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            showAlert("Error! Please fill out all required fields correctly.", "danger");
        }
        form.classList.add('was-validated');
    });

    // Alert Logic
    const alertMsg = <?= json_encode($alert_msg) ?>;
    const alertType = <?= json_encode($alert_type) ?>;
    
    if(alertMsg) {
        showAlert(alertMsg, alertType);
    }

    function showAlert(message, type) {
        const placeholder = document.getElementById('alertPlaceholder');
        if(!placeholder) return;
        
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
        
        placeholder.append(wrapper);
    }

    /**
     * 4. PASSWORD VISIBILITY TOGGLE (EXAMPLE)
     * This is an example of how you could add password visibility.
     * To implement fully, you would add a toggle button next to each password field.
     * 
     * Example HTML for a password field:
     * <div class="input-group">
     *   <input type="password" class="form-control" id="newPassword">
     *   <button class="btn btn-outline-secondary" type="button" id="togglePassword">
     *     <i class="fas fa-eye"></i>
     *   </button>
     * </div>
     */
    // const togglePassword = document.querySelector('#togglePassword');
    // const password = document.querySelector('#newPassword');
    // if (togglePassword && password) {
    //     togglePassword.addEventListener('click', function () {
    //         const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    //         password.setAttribute('type', type);
    //         this.querySelector('i').classList.toggle('fa-eye');
    //         this.querySelector('i').classList.toggle('fa-eye-slash');
    //     });
    // }

});
</script>

<?php include '../includes/footer.php'; ?>