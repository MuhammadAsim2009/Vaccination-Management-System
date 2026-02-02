<!-- ============================================
     VMS Admin Panel - Top Navigation Bar
     Modern SaaS-style header component
     ============================================ -->

<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm border-bottom">
    <div class="container-fluid px-4">
        
        <!-- ============================================
             LEFT SIDE: Logo / Brand
             ============================================ -->
        <a class="navbar-brand d-flex align-items-center fw-bold text-primary" href="../admin/dashboard.php">
            <i class="fas fa-syringe me-2 fs-4"></i>
            <span class="d-none d-sm-inline">VMS Admin</span>
        </a>

        <!-- Mobile menu toggle button -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- ============================================
             COLLAPSIBLE CONTENT
             ============================================ -->
        <div class="collapse navbar-collapse" id="navbarContent">
            
            <!-- ============================================
                 CENTER: Search Bar
                 ============================================ -->
            <div class="d-flex flex-grow-1 justify-content-center mx-3 my-2 my-lg-0">
                <div class="input-group" style="max-width: 500px;">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="search" class="form-control border-start-0 ps-0" 
                           placeholder="Search patients, hospitals, vaccines..." 
                           aria-label="Search">
                </div>
            </div>

            <!-- ============================================
                 RIGHT SIDE: Notifications & Profile
                 ============================================ -->
            <div class="d-flex align-items-center gap-3">
                
                <!-- Notification Bell with Badge -->
                <div class="position-relative">
                    <button class="btn btn-link text-dark p-2 position-relative" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            title="Notifications">
                        <i class="fas fa-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="font-size: 0.65rem;">
                            3
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </button>
                    <!-- Notification Dropdown (optional - can be enhanced with actual notifications) -->
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-start py-2" href="#">
                                <i class="fas fa-circle text-primary me-2 mt-1" style="font-size: 0.5rem;"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small">New appointment request</div>
                                    <div class="text-muted small">2 minutes ago</div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-start py-2" href="#">
                                <i class="fas fa-circle text-success me-2 mt-1" style="font-size: 0.5rem;"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small">Vaccination completed</div>
                                    <div class="text-muted small">1 hour ago</div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-start py-2" href="#">
                                <i class="fas fa-circle text-warning me-2 mt-1" style="font-size: 0.5rem;"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small">Upcoming vaccination due</div>
                                    <div class="text-muted small">3 hours ago</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center text-primary small fw-semibold" href="#">
                                View all notifications
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Admin Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark text-decoration-none d-flex align-items-center gap-2 p-0" 
                            type="button" 
                            id="adminProfileDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                             style="width: 38px; height: 38px; font-size: 0.9rem; font-weight: 600;">
                            A
                        </div>
                        <span class="d-none d-md-inline fw-medium">Admin</span>
                        <i class="fas fa-chevron-down small d-none d-md-inline"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="adminProfileDropdown">
                        <li>
                            <a class="dropdown-item py-2" href="#">
                                <i class="fas fa-user me-2 text-muted"></i>
                                Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="#">
                                <i class="fas fa-cog me-2 text-muted"></i>
                                Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="../auth/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</nav>

<!-- ============================================
     REQUIRED CDN LINKS (Add to your main layout if not already included)
     ============================================
     
     Bootstrap 5 CSS:
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     
     Font Awesome:
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     
     Bootstrap 5 JS (for dropdowns and collapse):
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     ============================================ -->

