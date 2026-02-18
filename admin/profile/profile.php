<?php
// Required Includes
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Backend Logic


$user_id = $_SESSION['user_id'];
$alert_msg = '';
$alert_type = '';

// 1. Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile_btn'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // Update User Table
    $stmt_user = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt_user->bind_param("ssi", $name, $email, $user_id);
    
    if ($stmt_user->execute()) {
        $alert_msg = "Profile updated successfully!";
        $alert_type = "success";
        $_SESSION['name'] = $name;
    } else {
        $alert_msg = "Error updating profile: " . $conn->error;
        $alert_type = "danger";
    }
}

// 2. Handle Password Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password_btn'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $alert_msg = "New passwords do not match.";
        $alert_type = "danger";
    } else {
        $stmt_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_pass->bind_param("i", $user_id);
        $stmt_pass->execute();
        $res_pass = $stmt_pass->get_result();
        $user_data = $res_pass->fetch_assoc();

        if (password_verify($current_pass, $user_data['password'])) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt_update_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_update_pass->bind_param("si", $new_hash, $user_id);
            
            if ($stmt_update_pass->execute()) {
                $alert_msg = "Password changed successfully.";
                $alert_type = "success";
            } else {
                $alert_msg = "Database error.";
                $alert_type = "danger";
            }
        } else {
            $alert_msg = "Incorrect current password.";
            $alert_type = "danger";
        }
    }
}

// 3. Fetch Admin Data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Fallbacks for missing admin table data
$admin['role'] = $admin['role'] ?? 'Administrator';
$admin['status'] = $admin['status'] ?? 'Active';
$admin['permissions'] = ['Manage Users/Parents', 'Manage Hospitals', 'Manage Children', 'Manage Appointments', 'Manage Vaccination Schedule', 'View Dashboard Analytics'];

?>

<main class="main-content">
    <div class="container-fluid px-4 py-4">
        
        <!-- 1️⃣ Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold">Admin Profile</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button class="btn btn-primary shadow-sm" id="editProfileHeaderBtn">
                    <i class="fas fa-user-edit me-2"></i>Edit Profile
                </button>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Alert Section -->
            <?php if($alert_msg): ?>
            <div class="col-12">
                <div class="alert alert-<?= $alert_type ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fas fa-<?= $alert_type == 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                    <?= $alert_msg ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Left Column: Overview & Timeline -->
            <div class="col-lg-4">
                
                <!-- 2️⃣ Profile Overview Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 text-center overflow-hidden">
                    <div class="card-body p-4">
                        <div class="position-relative d-inline-block mb-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border border-4 border-white shadow-sm" style="width: 120px; height: 120px;">
                                <i class="fas fa-user fa-4x text-secondary"></i>
                            </div>
                            <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle" data-bs-toggle="tooltip" title="Status: Active">
                                <span class="visually-hidden">Active</span>
                            </span>
                        </div>
                        
                        <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($admin['name']) ?></h4>
                        <p class="text-muted mb-2"><?= htmlspecialchars($admin['email']) ?></p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">
                                <i class="fas fa-shield-alt me-1"></i><?= htmlspecialchars($admin['role']) ?>
                            </span>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                <i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($admin['status']) ?>
                            </span>
                        </div>

                        <div class="d-grid gap-2">
                            <div class="p-3 bg-light rounded-3 text-start">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Member Since</small>
                                <div class="fw-medium text-dark"><i class="fas fa-calendar me-2 text-secondary"></i><?= date('M d, Y', strtotime($admin['created_at'])) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Details & Settings -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                        <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                    <i class="fas fa-id-badge me-2"></i>Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab">
                                    <i class="fas fa-user-cog me-2"></i>Edit Profile
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                                    <i class="fas fa-key me-2"></i>Password
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content" id="profileTabsContent">
                            
                            <!-- 3️⃣ Overview Tab (Readonly) -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <h6 class="fw-bold text-secondary text-uppercase small mb-3">Profile Details</h6>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="small text-muted fw-bold text-uppercase">Full Name</label>
                                        <div class="fw-medium text-dark fs-6"><?= htmlspecialchars($admin['name']) ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted fw-bold text-uppercase">Username</label>
                                        <div class="fw-medium text-dark fs-6">@<?= htmlspecialchars($admin['email']) ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted fw-bold text-uppercase">Email Address</label>
                                        <div class="fw-medium text-dark fs-6"><?= htmlspecialchars($admin['email']) ?></div>
                                    </div>
                                </div>

                                <hr class="my-4 opacity-10">

                                <h6 class="fw-bold text-secondary text-uppercase small mb-3">Role & Permissions</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach($admin['permissions'] as $perm): ?>
                                        <span class="badge bg-light text-dark border py-2 px-3">
                                            <i class="fas fa-check text-success me-2"></i><?= $perm ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- 4️⃣ Edit Profile Tab -->
                            <div class="tab-pane fade" id="edit" role="tabpanel">
                                <form id="editProfileForm" method="POST">
                                    <input type="hidden" name="update_profile_btn" value="1">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Full Name</label>
                                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Username</label>
                                            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($admin['email']) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Email</label>
                                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                                        </div>
                                        <div class="col-12 mt-4 text-end">
                                            <button type="button" class="btn btn-light border me-2">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- 4️⃣ Change Password Tab -->
                            <div class="tab-pane fade" id="password" role="tabpanel">
                                <form id="passwordForm" method="POST">
                                    <input type="hidden" name="update_password_btn" value="1">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 d-flex align-items-center" role="alert">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <small>Ensure your password is at least 8 characters long and includes symbols.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Current Password</label>
                                            <input type="password" class="form-control" name="current_password" placeholder="Enter current password" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">New Password</label>
                                            <input type="password" class="form-control" name="new_password" placeholder="Enter new password" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Confirm Password</label>
                                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password" required>
                                        </div>
                                        <div class="col-12 mt-4 text-end">
                                            <button type="submit" class="btn btn-primary">Update Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- JavaScript for Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Header "Edit Profile" button interaction
    const editBtn = document.getElementById('editProfileHeaderBtn');
    if(editBtn) {
        editBtn.addEventListener('click', function() {
            const triggerEl = document.querySelector('#profileTabs button[data-bs-target="#edit"]');
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
            // Scroll to tabs
            document.getElementById('profileTabs').scrollIntoView({ behavior: 'smooth' });
        });
    }

    // Initialize Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>

<?php include '../includes/footer.php'; ?>
