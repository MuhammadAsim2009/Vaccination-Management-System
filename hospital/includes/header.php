<?php
include_once __DIR__ . '/../../config/db.php';
include_once __DIR__ . '/../../config/functions.php';

// Retrieve current page filename to set the page title dynamically
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
$pageTitle = ucwords(str_replace("_", " ", $currentPage));

// Fetch Notifications for Hospital
$hospital_id = $_SESSION['user_id'] ?? 0;
$notifs = [];
$unread_count = 0;

if (isset($conn) && $hospital_id) {
    // Count unread notifications
    $stmt_unread = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_type = 'hospital' AND recipient_id = ? AND status = 'unread'");
    if ($stmt_unread) {
        $stmt_unread->bind_param("i", $hospital_id);
        $stmt_unread->execute();
        $res_unread = $stmt_unread->get_result();
        if ($row = $res_unread->fetch_assoc()) {
            $unread_count = $row['count'];
        }
        $stmt_unread->close();
    }

    // Fetch latest 5 notifications
    $stmt_notif = $conn->prepare("SELECT * FROM notifications WHERE user_type = 'hospital' AND recipient_id = ? ORDER BY created_at DESC LIMIT 3");
    if ($stmt_notif) {
        $stmt_notif->bind_param("i", $hospital_id);
        $stmt_notif->execute();
        $result_notif = $stmt_notif->get_result();
        while ($row = $result_notif->fetch_assoc()) {
            $notifs[] = $row;
        }
        $stmt_notif->close();
    }
}
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
                <li class="nav-item dropdown position-relative">
                    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fs-5 text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            <?= $unread_count ?>
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </a>
                    
                    <!-- Notification Dropdown -->
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" style="min-width: 320px;" aria-labelledby="notificationDropdown">
                        <li class="px-3 py-2 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark">Notifications</h6>
                        </li>
                        
                        <!-- Notification Items -->
                        <?php if (count($notifs) > 0): ?>
                            <?php foreach ($notifs as $notif): ?>
                                <?php
                                    // Determine icon and color based on type
                                    $icon = 'fa-info-circle';
                                    $colorClass = 'text-primary';
                                    $bgClass = 'bg-primary';
                                    
                                    if ($notif['type'] == 'appointment') {
                                        $icon = 'fa-calendar-check';
                                        $colorClass = 'text-success';
                                        $bgClass = 'bg-success';
                                    } elseif ($notif['type'] == 'inventory') {
                                        $icon = 'fa-exclamation-circle';
                                        $colorClass = 'text-warning';
                                        $bgClass = 'bg-warning';
                                    }
                                ?>
                                <li>
                                    <a class="dropdown-item py-3 border-bottom" href="#">
                                        <div class="d-flex align-items-start">
                                            <div class="<?= $bgClass ?> bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="fas <?= $icon ?> <?= $colorClass ?>"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 small fw-semibold"><?= htmlspecialchars($notif['title']) ?></p>
                                                <p class="mb-0 text-muted" style="font-size: 0.8rem; white-space: normal;"><?= htmlspecialchars($notif['message']) ?></p>
                                                <small class="text-muted"><?= time_elapsed_string($notif['created_at']) ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="text-center py-4"><p class="text-muted small mb-0">No new notifications</p></li>
                        <?php endif; ?>
                        
                        <li class="text-center py-2 border-top">
                            <a href="/vaccination_management_system/hospital/notifications/notifications.php" class="text-decoration-none small fw-semibold text-primary">View All Notifications</a>
                        </li>
                    </ul>
                </li>

                <!-- Hospital Profile Link -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="/vaccination_management_system/hospital/profile/update_profile.php">
                        <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['name'] ?>&background=0D6EFD&color=fff" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                        <div class="d-flex flex-column text-end d-none d-md-block">
                            <span class="fw-bold small text-dark"><?= $_SESSION['name'] ?></span>
                        </div>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>
