<?php
// Current Page logic for Title
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
$pageTitle = ucwords(str_replace("_", " ", $currentPage));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ? $pageTitle . " - VMS Admin" : "VMS Admin"; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/vaccination_management_system/assets/css/admin.css">
</head>
<body>

<!-- ============================================
     VMS Admin Panel - Top Navigation Bar
     ============================================ -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm border-bottom" style="z-index: 1060;">
    <div class="container-fluid px-4">
        
        <!-- ============================================
             LEFT SIDE: Logo / Brand
             ============================================ -->
        <a class="navbar-brand d-flex align-items-center fw-bold text-primary" href="/vaccination_management_system/admin/dashboard.php">
            <i class="fas fa-syringe me-2 fs-4"></i>
            <span class="d-none d-sm-inline">VMS Admin</span>
        </a>

        <!-- Mobile menu toggle button -->
        <button class="navbar-toggler border-0" type="button" onclick="toggleSidebar()">
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
                
                <!-- Notification Bell -->
                <div class="position-relative">
                    <button class="btn-link text-dark p-2 position-relative border-0 bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                        <i class="fas fa-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            3
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </button>
                    <!-- Notification Dropdown -->
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
                            <?php 
                                $adminName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
                                echo strtoupper(substr($adminName, 0, 1));
                            ?>
                        </div>
                        <span class="d-none d-md-inline fw-medium">
                            <?php echo htmlspecialchars($adminName); ?>
                        </span>
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
                            <a class="dropdown-item py-2 text-danger" href="/vaccination_management_system/auth/logout.php">
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
