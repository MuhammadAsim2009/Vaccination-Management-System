<?php
// Essential includes for authentication, header, sidebar, and database connection
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Get parent ID from session
$parent_id = $_SESSION['user_id'];

// Fetch parent profile data
$stmt_hospitals = $conn->prepare("SELECT p.id AS parent_id, u.name AS parent_name, u.email, p.phone, p.address, p.created_at AS join_date FROM parents p LEFT JOIN users u ON p.user_id = u.id WHERE u.id = ?");
$stmt_hospitals->bind_param("i", $parent_id);
$stmt_hospitals->execute();
$result_hospitals = $stmt_hospitals->get_result();
$row = $result_hospitals->fetch_assoc();


// Prepare parent profile data
$parent_profile = [
    'full_name' => $row['parent_name'],
    'email' => $row['email'],
    'phone' => $row['phone'],
    'address' => $row['address'],
    'join_date' => $row['join_date'],
];

$alert_msg = '';
$alert_type = '';

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Update users table (name)
    $stmt_u = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
    $stmt_u->bind_param("si", $full_name, $parent_id);
    $stmt_u->execute();

    // Update parents table (phone, address)
    $stmt_p = $conn->prepare("UPDATE parents SET phone = ?, address = ? WHERE user_id = ?");
    $stmt_p->bind_param("ssi", $phone, $address, $parent_id);
    
    if ($stmt_p->execute()) {
        $alert_msg = "Profile updated successfully.";
        $alert_type = "success";
        // Refresh data
        $parent_profile['full_name'] = $full_name;
        $parent_profile['phone'] = $phone;
        $parent_profile['address'] = $address;
    } else {
        $alert_msg = "Error updating profile: " . $conn->error;
        $alert_type = "danger";
    }
}

// Handle Password Update
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $alert_msg = "New passwords do not match.";
        $alert_type = "danger";
    } else {
        // Verify current password
        $stmt_check = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_check->bind_param("i", $parent_id);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();
        $user_data = $res_check->fetch_assoc();

        if ($user_data && password_verify($current_password, $user_data['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_pass->bind_param("si", $new_hash, $parent_id);
            
            if ($stmt_pass->execute()) {
                $alert_msg = "Password changed successfully.";
                $alert_type = "success";
            } else {
                $alert_msg = "Error updating password.";
                $alert_type = "danger";
            }
        } else {
            $alert_msg = "Incorrect current password.";
            $alert_type = "danger";
        }
    }
}

?>

<div class="container-fluid px-4">
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="mb-1 fw-bold text-dark">My Profile</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php if($alert_msg): ?>
    <div class="alert alert-<?= $alert_type ?> alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
        <i class="fas fa-<?= $alert_type == 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
        <?= $alert_msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Personal Details Form Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user-edit text-primary me-2"></i>Personal Details
                    </h5>
                    <button class="btn btn-outline-primary btn-sm" id="editProfileBtn">
                        <i class="fas fa-pencil-alt me-2"></i>Edit Profile
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <form id="profileForm" method="POST">
                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="full_name" value="<?= htmlspecialchars($parent_profile['full_name']) ?>" disabled required>
                        </div>
                        <!-- Email Address (Read-only) -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($parent_profile['email']) ?>" readonly>
                        </div>
                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($parent_profile['phone']) ?>" disabled required>
                        </div>
                        <!-- Address -->
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($parent_profile['address']) ?>" disabled required>
                        </div>
                    </div>
                    <!-- Action Buttons (Hidden by default) -->
                    <div class="mt-4 text-end d-none" id="formActions">
                        <button type="button" class="btn btn-light me-2" id="cancelBtn">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn" name="update_profile">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Settings Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-shield-alt text-primary me-2"></i>Security Settings
                </h5>
            </div>
            <div class="card-body p-4">
                <form id="passwordForm" method="POST">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" placeholder="Enter your current password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Enter new password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Confirm new password" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary" name="update_password">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editProfileBtn = document.getElementById('editProfileBtn');
    const profileForm = document.getElementById('profileForm');
    const formActions = document.getElementById('formActions');
    const cancelBtn = document.getElementById('cancelBtn');
    const formInputs = profileForm.querySelectorAll('input:not([readonly])');

    // Store original values to revert on cancel
    let originalValues = {};
    formInputs.forEach(input => {
        originalValues[input.id] = input.value;
    });

    function toggleEditMode(enable) {
        if (enable) {
            // Enable editing
            formInputs.forEach(input => input.disabled = false);
            formActions.classList.remove('d-none');
            editProfileBtn.classList.add('d-none');
        } else {
            // Disable editing and restore original values
            formInputs.forEach(input => {
                input.disabled = true;
                input.value = originalValues[input.id];
            });
            formActions.classList.add('d-none');
            editProfileBtn.classList.remove('d-none');
        }
    }

    // "Edit Profile" button click event
    editProfileBtn.addEventListener('click', () => toggleEditMode(true));

    // "Cancel" button click event
    cancelBtn.addEventListener('click', () => toggleEditMode(false));

    // Profile form submission handled by PHP

    // "Change Password" form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('New password and confirmation do not match.');
            return;
        }

        if (newPassword.length > 0 && newPassword.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long.');
            return;
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
