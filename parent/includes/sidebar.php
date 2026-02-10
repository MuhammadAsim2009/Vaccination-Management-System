<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Toggle Button (Mobile) -->
<button class="btn btn-soft-primary d-lg-none sidebar-toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#parentSidebar" aria-controls="parentSidebar" aria-expanded="false">
    <i class="fas fa-bars"></i>
    <span class="ms-2">Menu</span>
</button>

<!-- Responsive Sidebar -->
<aside id="parentSidebar" class="parent-sidebar collapse d-lg-block">
    <div class="sidebar-card">

        <!-- Brand / Header -->
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div>
                    <div class="brand-title">Parent Panel</div>
                    <div class="brand-subtitle">Vaccination Care</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="sidebar-section-title">Main</div>
            <ul class="nav flex-column gap-2">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/parent/dashboard.php" 
                        class="nav-link sidebar-link <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- My Children -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/parent/child/children_list.php"
                       class="nav-link sidebar-link <?= in_array($current_page, ['children_list.php', 'child_details.php', 'add_child.php', 'update_child.php']) ? 'active' : '' ?>">
                        <i class="fas fa-child"></i>
                        <span>My Children</span>
                    </a>
                </li>

                <!-- Vaccination Schedule -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/parent/vaccination/vaccination_dates.php"
                       class="nav-link sidebar-link <?= ($current_page === 'vaccination_dates.php') ? 'active' : '' ?>">
                        <i class="fas fa-syringe"></i>
                        <span>Vaccination Schedule</span>
                    </a>
                </li>

                <!-- Vaccination Report -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/parent/vaccination/vaccination_report.php"
                       class="nav-link sidebar-link <?= ($current_page === 'vaccination_report.php') ? 'active' : '' ?>">
                        <i class="fas fa-file-medical"></i>
                        <span>Vaccination Report</span>
                    </a>
                </li>

                <!-- Book Vaccination -->
                <li class="nav-item">
                    <a href="/vaccination_management_system/parent/booking/search_hospital.php"
                       class="nav-link sidebar-link <?= in_array($current_page, ['book_vaccination.php', 'search_hospital.php']) ? 'active' : '' ?>">

                        <i class="fas fa-calendar-check"></i>
                        <span>Book Vaccination</span>
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
    </div>
</aside>
