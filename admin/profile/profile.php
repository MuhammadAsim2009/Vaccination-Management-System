<?php
/**
 * Admin Profile Page
 * 
 * Allows admin to view and manage their profile details, security settings,
 * and view recent activity logs.
 * 
 * Path: admin/profile/profile.php
 */

include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// --------------------------------------------------------------------------
// Dummy Data Generation
// --------------------------------------------------------------------------
$admin = [
    'name' => 'Super Admin',
    'username' => 'admin_master',
    'email' => 'admin@vms.com',
    'phone' => '+1 (555) 123-4567',
    'role' => 'Super Administrator',
    'status' => 'Active',
    'last_login' => 'Oct 25, 2023 09:15 AM',
    'dob' => '1985-08-15',
    'gender' => 'Male',
    'address' => '123 Admin Plaza, Tech City, CA 90210',
    'completion' => 85,
    'permissions' => ['Manage Users', 'Manage Hospitals', 'System Settings', 'View Reports', 'Audit Logs']
];
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
                        
                        <h4 class="fw-bold text-dark mb-1"><?= $admin['name'] ?></h4>
                        <p class="text-muted mb-2"><?= $admin['email'] ?></p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">
                                <i class="fas fa-shield-alt me-1"></i><?= $admin['role'] ?>
                            </span>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                <i class="fas fa-check-circle me-1"></i><?= $admin['status'] ?>
                            </span>
                        </div>

                        <div class="d-grid gap-2">
                            <div class="p-3 bg-light rounded-3 text-start">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Last Login</small>
                                <div class="fw-medium text-dark"><i class="fas fa-clock me-2 text-secondary"></i><?= $admin['last_login'] ?></div>
                            </div>
                            <div class="p-3 bg-light rounded-3 text-start">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Phone</small>
                                <div class="fw-medium text-dark"><i class="fas fa-phone me-2 text-secondary"></i><?= $admin['phone'] ?></div>
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
                                        <div class="fw-medium text-dark fs-6"><?= $admin['name'] ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted fw-bold text-uppercase">Username</label>
                                        <div class="fw-medium text-dark fs-6">@<?= $admin['username'] ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted fw-bold text-uppercase">Email Address</label>
                                        <div class="fw-medium text-dark fs-6"><?= $admin['email'] ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted fw-bold text-uppercase">Phone Number</label>
                                        <div class="fw-medium text-dark fs-6"><?= $admin['phone'] ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted fw-bold text-uppercase">Date of Birth</label>
                                        <div class="fw-medium text-dark fs-6"><?= date('F d, Y', strtotime($admin['dob'])) ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted fw-bold text-uppercase">Gender</label>
                                        <div class="fw-medium text-dark fs-6"><?= $admin['gender'] ?></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-muted fw-bold text-uppercase">Address</label>
                                        <div class="fw-medium text-dark fs-6"><?= $admin['address'] ?></div>
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
                                <form id="editProfileForm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Full Name</label>
                                            <input type="text" class="form-control" value="<?= $admin['name'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Username</label>
                                            <input type="text" class="form-control" value="<?= $admin['username'] ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Email</label>
                                            <input type="email" class="form-control" value="<?= $admin['email'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Phone</label>
                                            <input type="text" class="form-control" value="<?= $admin['phone'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Date of Birth</label>
                                            <input type="date" class="form-control" value="<?= $admin['dob'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Gender</label>
                                            <select class="form-select">
                                                <option value="Male" selected>Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Address</label>
                                            <textarea class="form-control" rows="3"><?= $admin['address'] ?></textarea>
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
                                <form id="passwordForm">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 d-flex align-items-center" role="alert">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <small>Ensure your password is at least 8 characters long and includes symbols.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Current Password</label>
                                            <input type="password" class="form-control" placeholder="Enter current password">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">New Password</label>
                                            <input type="password" class="form-control" placeholder="Enter new password">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Confirm Password</label>
                                            <input type="password" class="form-control" placeholder="Confirm new password">
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

    // Form Submission Simulation
    const forms = ['editProfileForm', 'passwordForm'];
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                
                // Loading State
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                
                setTimeout(() => {
                    // Success State
                    btn.innerHTML = '<i class="fas fa-check me-2"></i>Saved';
                    btn.classList.replace('btn-primary', 'btn-success');
                    
                    setTimeout(() => {
                        // Reset
                        btn.innerHTML = originalText;
                        btn.classList.replace('btn-success', 'btn-primary');
                        btn.disabled = false;
                        if(formId === 'passwordForm') form.reset();
                    }, 2000);
                }, 1000);
            });
        }
    });

    // Initialize Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>

<?php include '../includes/footer.php'; ?>
