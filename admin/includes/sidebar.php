<?php
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>

<!-- ============================================
     VMS Admin Panel - Sidebar Navigation
     ============================================ -->
<aside class="sidebar-wrapper" id="adminSidebar">
    
    <div class="sidebar-header">
        <h5 class="d-flex align-items-center">
            <i class="fas fa-bars me-3"></i>
            Menu
        </h5>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link sidebar-link <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>" href="/vaccination_management_system/admin/dashboard.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Children -->
            <li class="nav-item">
                <a class="nav-link sidebar-link <?= in_array($current_page, ['view_children.php', 'child_profile.php']) ? 'active' : '' ?>" href="/vaccination_management_system/admin/children/view_children.php">
                    <i class="fas fa-child"></i>
                    <span>Children</span>
                </a>
            </li>

            <!-- Vaccinations -->
            <li class="nav-item">
                <a class="nav-link sidebar-link <?= in_array($current_page, ['vaccination_report.php', 'upcoming_dates.php']) ? 'active' : '' ?>" href="/vaccination_management_system/admin/vaccination/vaccination_report.php">
                    <i class="fas fa-syringe"></i>
                    <span>Vaccinations</span>
                </a>
            </li>

            <!-- Vaccines -->
            <li class="nav-item">
                <a class="nav-link sidebar-link <?= in_array($current_page, ['vaccine_list.php', 'update_vaccine_status.php']) ? 'active' : '' ?>" href="/vaccination_management_system/admin/vaccines/vaccine_list.php">
                    <i class="fas fa-vial"></i>
                    <span>Vaccines</span>
                </a>
            </li>

            <!-- Hospitals -->
            <li class="nav-item">
                <a class="nav-link sidebar-link <?= in_array($current_page, ['hospital_list.php', 'add_hospital.php', 'update_hospital.php']) ? 'active' : '' ?>" href="/vaccination_management_system/admin/hospitals/hospital_list.php">
                    <i class="fas fa-hospital"></i>
                    <span>Hospitals</span>
                </a>
            </li>

            <!-- Requests -->
            <li class="nav-item">
                <a class="nav-link sidebar-link <?= ($current_page === 'appointment_requests.php') ? 'active' : '' ?>" href="/vaccination_management_system/admin/requests/appointment_requests.php">
                    <i class="fas fa-inbox"></i>
                    <span>Requests</span>
                </a>
            </li>

            <!-- Bookings -->
            <li class="nav-item">
                <a class="nav-link sidebar-link <?= ($current_page === 'booking_details.php') ? 'active' : '' ?>" href="/vaccination_management_system/admin/bookings/booking_details.php">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>

        </ul>
        
        <!-- Logout pushed to bottom -->
        <div class="mt-5 pt-5">
            <a class="nav-link sidebar-link sidebar-link-danger" href="/vaccination_management_system/auth/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
</aside>
