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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $pageTitle ? $pageTitle . " - VMS Parent" : "VMS Parent"; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/vaccination_management_system/assets/css/parent.css">
</head>
<body>

<!-- Sticky Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid px-3 px-lg-4">
        
        <!-- Left: Logo/Brand -->
        <a class="navbar-brand d-flex align-items-center" href="/vaccination_management_system/parent/dashboard.php">
            <i class="fas fa-shield-virus text-primary me-2 fs-4"></i>
            <span class="fw-bold text-primary">VMS Parent</span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            
            <!-- Center: Search Bar (Optional) -->
            <div class="mx-auto my-3 my-lg-0" style="max-width: 400px; width: 100%;">
                <div class="input-group rounded-pill overflow-hidden shadow-sm">
                    <span class="input-group-text bg-white border-0 ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-0 shadow-none" placeholder="Search vaccinations, appointments..." aria-label="Search">
                </div>
            </div>

            <!-- Right: Notifications & Profile -->
            <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-3">
                
                <!-- Notification Bell -->
                <li class="nav-item dropdown position-relative">
                    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fs-5 text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            3
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </a>
                    
                    <!-- Notification Dropdown -->
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" style="min-width: 320px;" aria-labelledby="notificationDropdown">
                        <li class="px-3 py-2 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark">Notifications</h6>
                        </li>
                        
                        <!-- Notification Items -->
                        <li>
                            <a class="dropdown-item py-3 border-bottom" href="#">
                                <div class="d-flex align-items-start">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-syringe text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 small fw-semibold">Vaccination Due Soon</p>
                                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">Your child's MMR vaccine is due in 3 days</p>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        
                        <li>
                            <a class="dropdown-item py-3 border-bottom" href="#">
                                <div class="d-flex align-items-start">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 small fw-semibold">Appointment Confirmed</p>
                                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">Your booking for Feb 15 has been confirmed</p>
                                        <small class="text-muted">5 hours ago</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        
                        <li>
                            <a class="dropdown-item py-3" href="#">
                                <div class="d-flex align-items-start">
                                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-info-circle text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 small fw-semibold">New Vaccine Available</p>
                                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">COVID-19 booster now available for children</p>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        
                        <li class="text-center py-2 border-top">
                            <a href="#" class="text-decoration-none small fw-semibold text-primary">View All Notifications</a>
                        </li>
                    </ul>
                </li>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center gap-2 pe-0" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-none d-md-block text-end">
                            <p class="mb-0 small fw-semibold text-dark">John Doe</p>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Parent</p>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                    </a>
                    
                    <!-- Profile Dropdown Menu -->
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item rounded-2 py-2" href="profile.php">
                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-2 py-2" href="settings.php">
                                <i class="fas fa-cog me-2 text-secondary"></i>
                                Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-2"></li>
                        <li>
                            <a class="dropdown-item rounded-2 py-2 text-danger" href="../auth/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>