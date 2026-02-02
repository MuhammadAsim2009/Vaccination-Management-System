<!-- ============================================
     VMS Admin Panel - Sidebar Navigation
     Modern SaaS-style vertical sidebar component
     ============================================ -->

<aside id="adminSidebar" class="sidebar">
    <div class="sidebar-content">
        
        <!-- ============================================
             SIDEBAR HEADER (Optional - can be removed)
             ============================================ -->
        <div class="sidebar-header d-none d-md-block px-3 py-4 border-bottom border-secondary">
            <h5 class="text-white mb-0 fw-bold">
                <i class="fas fa-bars me-2"></i>
                Menu
            </h5>
        </div>

        <!-- ============================================
             NAVIGATION MENU
             ============================================ -->
        <nav class="sidebar-nav px-2 py-3">
            <ul class="nav flex-column gap-1">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link sidebar-link active" href="../dashboard.php" data-page="dashboard">
                        <i class="fas fa-home me-3"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Children -->
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="children/view_children.php" data-page="children">
                        <i class="fas fa-child me-3"></i>
                        <span>Children</span>
                    </a>
                </li>

                <!-- Vaccinations -->
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="vaccination/vaccination_report.php" data-page="vaccinations">
                        <i class="fas fa-syringe me-3"></i>
                        <span>Vaccinations</span>
                    </a>
                </li>

                <!-- Vaccines -->
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="vaccines/vaccine_list.php" data-page="vaccines">
                        <i class="fas fa-vial me-3"></i>
                        <span>Vaccines</span>
                    </a>
                </li>

                <!-- Hospitals -->
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="hospitals/hospital_list.php" data-page="hospitals">
                        <i class="fas fa-hospital me-3"></i>
                        <span>Hospitals</span>
                    </a>
                </li>

                <!-- Requests -->
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="requests/appointment_requests.php" data-page="requests">
                        <i class="fas fa-inbox me-3"></i>
                        <span>Requests</span>
                    </a>
                </li>

                <!-- Bookings -->
                <li class="nav-item">
                    <a class="nav-link sidebar-link" href="bookings/booking_details.php" data-page="bookings">
                        <i class="fas fa-calendar-check me-3"></i>
                        <span>Bookings</span>
                    </a>
                </li>

                <!-- Divider -->
                <li class="nav-item my-2">
                    <hr class="sidebar-divider">
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link sidebar-link sidebar-link-danger" href="../auth/logout.php">
                        <i class="fas fa-sign-out-alt me-3"></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>

    <!-- ============================================
         MOBILE OVERLAY (for mobile toggle)
         ============================================ -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
</aside>

<!-- ============================================
     SIDEBAR STYLES (Override/Additional styles)
     ============================================ -->
<style>
    /* Ensure sidebar is below navbar but above content */
    .sidebar {
        z-index: 999 !important;
    }

    /* Mobile Overlay */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 998;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .sidebar-overlay.show {
        display: block;
        opacity: 1;
    }

    /* Mobile Styles */
    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        body.sidebar-open {
            overflow: hidden;
        }
        
        /* Remove body padding on mobile */
        body {
            padding-left: 0 !important;
        }
    }

    /* Desktop: Add padding to body to account for fixed sidebar */
    @media (min-width: 992px) {
        /* Push body content to the right to make room for sidebar */
        body {
            padding-left: 260px !important;
        }
        
        /* Ensure main content area works properly */
        .main-content {
            margin-left: 0;
            width: 100%;
        }
    }
</style>

<!-- ============================================
     SIDEBAR JAVASCRIPT
     ============================================ -->
<script>
(function() {
    'use strict';
    
    // Sidebar Toggle Functionality (for mobile)
    function toggleSidebar() {
        try {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            if (sidebar && overlay) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                if (body) {
                    body.classList.toggle('sidebar-open');
                }
            }
        } catch (error) {
            console.warn('Sidebar toggle error:', error);
        }
    }

    // Initialize sidebar on page load
    function initSidebar() {
        try {
            // Close sidebar when clicking overlay
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.addEventListener('click', function() {
                    toggleSidebar();
                });
            }

            // Auto-detect active page based on current URL
            try {
                const currentPath = window.location.pathname.toLowerCase();
                const currentPage = currentPath.split('/').pop() || '';
                const sidebarLinks = document.querySelectorAll('.sidebar-link');
                
                if (sidebarLinks && sidebarLinks.length > 0) {
                    sidebarLinks.forEach(function(link) {
                        if (!link || !link.href) return;
                        
                        // Remove active class from all links first
                        link.classList.remove('active');
                        
                        // Get the link's href and data-page
                        const linkHref = (link.getAttribute('href') || '').toLowerCase();
                        const linkPage = (link.getAttribute('data-page') || '').toLowerCase();
                        
                        // Simple matching: check if current path includes the link path or data-page
                        const linkFileName = linkHref.split('/').pop().replace('.php', '');
                        const currentFileName = currentPage.replace('.php', '');
                        
                        // Match by filename or data-page attribute
                        if (linkFileName && currentFileName && currentFileName === linkFileName) {
                            link.classList.add('active');
                        } else if (linkPage && currentPath.includes(linkPage)) {
                            link.classList.add('active');
                        } else if (linkHref && currentPath.includes(linkHref)) {
                            link.classList.add('active');
                        }
                    });
                }
            } catch (e) {
                // Silently fail if active link detection fails
            }
        } catch (error) {
            console.warn('Sidebar initialization error:', error);
        }
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        initSidebar();
    }

    // Expose toggle function globally (can be called from header toggle button)
    window.toggleSidebar = toggleSidebar;
})();
</script>

<!-- ============================================
     USAGE NOTES:
     
     1. Include this sidebar in your admin pages:
        <?php include '../includes/sidebar.php'; ?>
     
     2. To toggle sidebar on mobile, add a button in your header:
        <button onclick="toggleSidebar()" class="btn">
            <i class="fas fa-bars"></i>
        </button>
     
     3. The sidebar automatically highlights the active page based on URL
     
     4. Main content should have proper padding/margin on desktop
        (automatically handled by CSS - body gets padding-left: 260px)
     ============================================ -->

