<?php
/**
 * Parent Profile Page
 * 
 * Allows the parent to view and edit their personal and security details.
 * Path: parent/profile.php
 */

include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Dummy data for the parent's profile
$parent_profile = [
    'full_name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone' => '+1 (123) 456-7890',
    'address' => '123 Health St, Wellness Town',
    'city' => 'Metropolis',
    'state' => 'California',
    'postal_code' => '90210',
    'join_date' => '2023-01-15',
    'profile_pic' => 'https://i.pravatar.cc/150?u=johndoe' // Using a placeholder image service
];

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
                <form id="profileForm">
                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" value="<?= htmlspecialchars($parent_profile['full_name']) ?>" disabled>
                        </div>
                        <!-- Email Address (Read-only) -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($parent_profile['email']) ?>" readonly>
                        </div>
                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" value="<?= htmlspecialchars($parent_profile['phone']) ?>" disabled>
                        </div>
                        <!-- Address -->
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" value="<?= htmlspecialchars($parent_profile['address']) ?>" disabled>
                        </div>
                    </div>
                    <!-- Action Buttons (Hidden by default) -->
                    <div class="mt-4 text-end d-none" id="formActions">
                        <button type="button" class="btn btn-light me-2" id="cancelBtn">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>
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
                <form id="passwordForm">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" placeholder="Enter your current password">
                        </div>
                        <div class="col-md-6">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
                        </div>
                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
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

    // "Save Changes" form submission
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // In a real application, you would send data to the server via AJAX.
        
        // For this demo, we'll just show an alert and disable the form.
        alert('Profile updated successfully!');
        
        // Update original values to the new saved values
        formInputs.forEach(input => {
            originalValues[input.id] = input.value;
        });

        toggleEditMode(false);
    });

    // "Change Password" form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            alert('New password and confirmation do not match.');
            return;
        }

        if (newPassword.length > 0 && newPassword.length < 8) {
            alert('Password must be at least 8 characters long.');
            return;
        }

        // For this demo, we'll just show an alert.
        alert('Password changed successfully!');
        this.reset(); // Clear the password form fields
    });
});
</script>

<?php include '../includes/footer.php'; ?>
