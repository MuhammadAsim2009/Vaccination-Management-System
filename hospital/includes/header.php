<?php
// Retrieve current page filename to set the page title dynamically
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
$pageTitle = ucwords(str_replace("_", " ", $currentPage));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $pageTitle ? $pageTitle . " - VMS Hospital" : "VMS Hospital"; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/vaccination_management_system/assets/css/hospital.css">
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid px-4">
        
        <!-- Left: Logo / Brand Name -->
        <a class="navbar-brand d-flex align-items-center fw-bold text-primary" href="/vaccination_management_system/hospital/dashboard.php">
            <i class="fas fa-hospital-alt me-2"></i>
            VMS Hospital
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
            
            <!-- Center: Search Bar -->
            <form class="d-flex mx-auto my-3 my-lg-0 navbar-search" style="max-width: 400px; width: 100%;">
                <div class="input-group w-100">
                    <span class="input-group-text bg-light border-0 rounded-start-pill ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input class="form-control bg-light border-0 rounded-end-pill" type="search" placeholder="Search patients, appointments..." aria-label="Search">
                </div>
            </form>

            <!-- Right: Actions -->
            <ul class="navbar-nav ms-auto align-items-center">
                
                <!-- Notification Bell -->
                <li class="nav-item dropdown me-3">
                    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-lg text-secondary"></i>
                        <span class="position-absolute translate-middle badge rounded-pill bg-danger notification-badge" style="font-size: 0.65rem;">
                            3
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notificationDropdown" style="width: 300px;">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item d-flex align-items-start py-2" href="#">
                            <div class="me-2"><i class="fas fa-calendar-check text-success"></i></div>
                            <div>
                                <small class="fw-bold d-block">Appointment Confirmed</small>
                                <small class="text-muted">John Doe - 10:00 AM</small>
                            </div>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item d-flex align-items-start py-2" href="#">
                            <div class="me-2"><i class="fas fa-exclamation-circle text-warning"></i></div>
                            <div>
                                <small class="fw-bold d-block">Stock Alert</small>
                                <small class="text-muted">Polio vaccine running low</small>
                            </div>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center small text-primary fw-bold" href="/vaccination_management_system/hospital/notifications/notifications.php">View all notifications</a></li>
                    </ul>
                </li>

                <!-- Hospital Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=City+Hospital&background=0D6EFD&color=fff" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                        <div class="d-flex flex-column text-end d-none d-md-block">
                            <span class="fw-bold small text-dark">City General Hospital</span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="/vaccination_management_system/hospital/profile/update_profile.php"><i class="fas fa-user me-2 text-muted"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2 text-muted"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/vaccination_management_system/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>
