<?php
/**
 * Update Hospital Profile Page
 * Hospital Panel - Vaccination Management System
 * 
 * This page allows hospital administrators to view and update their
 * facility's profile information, logo, and security settings.
 * All data is static for frontend demonstration purposes.
 * 
 * @path /hospital/profile/update_profile.php
 */

// Essential includes for authentication, header, sidebar, and footer
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// --- DUMMY DATA SIMULATION ---
// In a real application, this data would be fetched from the database
// based on the logged-in hospital's session ID.
$hospital_profile = [
    'name' => 'City General Hospital',
    'registration_no' => 'HSP-LHR-12345',
    'email' => 'contact@citygeneral.com',
    'phone' => '+92 300 1234567',
    'website' => 'https://citygeneral.com',
    'address' => '123 Health St, Medical District',
    'city' => 'Larkana',
    'country' => 'Pakistan',
    'username' => 'citygeneral_admin',
    'status' => 'Active',
    'logo' => 'https://ui-avatars.com/api/?name=City+Hospital&background=0D6EFD&color=fff&size=128&bold=true'
];
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
            <div class="col-12">
                <div id="successAlert" class="alert alert-success d-none alert-dismissible fade show shadow-sm rounded-4 border-0" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fs-4 me-3"></i>
                        <div><strong>Success!</strong> Your profile has been updated. (UI Mockup)</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <form id="updateProfileForm" class="needs-validation" novalidate>
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
                                    <input type="text" class="form-control" id="hospitalName" value="<?= htmlspecialchars($hospital_profile['name']) ?>" required>
                                    <div class="invalid-feedback">Hospital name is required.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="regNumber" class="form-label fw-semibold">Registration Number</label>
                                    <input type="text" class="form-control bg-light" id="regNumber" value="<?= htmlspecialchars($hospital_profile['registration_no']) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($hospital_profile['email']) ?>" required>
                                    <div class="invalid-feedback">Please enter a valid email.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" value="<?= htmlspecialchars($hospital_profile['phone']) ?>" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="website" class="form-label fw-semibold">Website URL</label>
                                    <input type="url" class="form-control" id="website" value="<?= htmlspecialchars($hospital_profile['website']) ?>">
                                </div>

                                <!-- Address Info -->
                                <div class="col-md-6">
                                    <label for="city" class="form-label fw-semibold">City</label>
                                    <input type="text" class="form-control" id="city" value="<?= htmlspecialchars($hospital_profile['city']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="country" class="form-label fw-semibold">Country</label>
                                    <select class="form-select" id="country">
                                        <option value="Pakistan" <?= $hospital_profile['country'] == 'Pakistan' ? 'selected' : '' ?>>Pakistan</option>
                                        <option value="India" <?= $hospital_profile['country'] == 'India' ? 'selected' : '' ?>>India</option>
                                        <option value="Bangladesh" <?= $hospital_profile['country'] == 'Bangladesh' ? 'selected' : '' ?>>Bangladesh</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">Full Address</label>
                                    <textarea class="form-control" id="address" rows="2"><?= htmlspecialchars($hospital_profile['address']) ?></textarea>
                                </div>

                                <!-- Account Info -->
                                <div class="col-md-6">
                                    <label for="username" class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control bg-light" id="username" value="<?= htmlspecialchars($hospital_profile['username']) ?>" readonly>
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
                                <button type="submit" class="btn btn-primary btn-md fw-bold px-4 ms-2">
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
                                        <input type="password" class="form-control border-start-0" id="currentPassword" placeholder="Enter your current password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="newPassword" class="form-label fw-semibold">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-key text-muted"></i></span>
                                        <input type="password" class="form-control border-start-0" id="newPassword" placeholder="Enter new password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirmPassword" class="form-label fw-semibold">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password">
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-danger rounded-pill px-4">
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
    const successAlert = document.getElementById('successAlert');

    /**
     * 1. FORM SUBMISSION LOGIC (UI MOCKUP)
     * Handles form validation and displays a success message.
     */
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        event.stopPropagation();

        // Add Bootstrap's validation class
        form.classList.add('was-validated');

        if (form.checkValidity()) {
            // If form is valid, simulate a successful update
            console.log('Form is valid. Simulating update...');

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';

            // Simulate API call delay
            setTimeout(() => {
                // Show success alert
                successAlert.classList.remove('d-none');
                window.scrollTo({ top: 0, behavior: 'smooth' });

                // Reset button to original state after a delay
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;

                // Remove validation classes after success
                form.classList.remove('was-validated');
            }, 1500);

        } else {
            // If form is invalid, Bootstrap's default feedback will show
            console.log('Form is invalid.');
        }
    });

    /**
     * 2. FORM RESET LOGIC
     * Resets validation state and logo preview.
     */
    form.addEventListener('reset', function() {
        form.classList.remove('was-validated');
        successAlert.classList.add('d-none');
    });

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