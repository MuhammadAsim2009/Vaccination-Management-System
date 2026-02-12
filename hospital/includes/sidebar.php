<?php
$current_page = basename($_SERVER['PHP_SELF'], ".php");
?>

<!-- Sidebar Toggle Button (Mobile) -->
<button class="btn btn-primary d-lg-none sidebar-toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#hospitalSidebar" aria-controls="hospitalSidebar" aria-expanded="false" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050; border-radius: 50%; width: 50px; height: 50px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
    <i class="fas fa-bars"></i>
</button>

<!-- Responsive Sidebar -->
<aside id="hospitalSidebar" class="sidebar collapse d-lg-block">
    <div class="sidebar-card">

        <!-- Brand / Header -->
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <div>
                    <div class="brand-title">Hospital Panel</div>
                    <div class="brand-subtitle">VMS Hospital</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="sidebar-section-title">Management</div>
            <ul class="nav flex-column gap-2">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/hospital/dashboard.php" 
                        class="nav-link sidebar-link <?= ($current_page == 'dashboard') ? 'active' : '' ?>">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Appointments -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/hospital/vaccination/appointments.php"
                       class="nav-link sidebar-link <?= ($current_page == 'appointments' || $current_page == 'update_status') ? 'active' : '' ?>">
                        <i class="fas fa-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title mt-4">Vaccine Inventory</div>
            <ul class="nav flex-column gap-2">
                <!-- Vaccine List -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/hospital/vaccines/vaccine_list.php"
                       class="nav-link sidebar-link <?= ($current_page == 'vaccine_list' || $current_page == 'update_vaccine') ? 'active' : '' ?>">
                        <i class="fas fa-vials"></i>
                        <span>Vaccine List</span>
                    </a>
                </li>

                <!-- Add Vaccine -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/hospital/vaccines/add_vaccine.php"
                       class="nav-link sidebar-link <?= ($current_page == 'add_vaccine') ? 'active' : '' ?>">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Vaccine</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-section-title mt-4">Account</div>
            <ul class="nav flex-column gap-2">
                <!-- Profile -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/hospital/profile/update_profile.php"
                       class="nav-link sidebar-link <?= ($current_page == 'update_profile') ? 'active' : '' ?>">
                        <i class="fas fa-user-cog"></i>
                        <span>Profile</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Logout Button -->
        <div class="sidebar-footer">
            <a href="/vaccination_management_system/auth/logout.php" class="btn btn-danger w-100 rounded-pill">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>

    </div>
</aside>
