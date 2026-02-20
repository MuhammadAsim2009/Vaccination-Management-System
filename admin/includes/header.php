<?php
include_once __DIR__ . '/../../config/db.php';
include_once __DIR__ . '/../../config/functions.php';

// Current Page logic for Title
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
$pageTitle = ucwords(str_replace("_", " ", $currentPage));

// Fetch Notifications
$admin_id = $_SESSION['user_id'] ?? 0;
$notifs = [];
$unread_count = 0;

if (isset($conn) && $admin_id) {
    // Count unread
    $stmt_unread = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_type = 'admin' AND recipient_id = ? AND status = 'unread'");
    if ($stmt_unread) {
        $stmt_unread->bind_param("i", $admin_id);
        $stmt_unread->execute();
        $res_unread = $stmt_unread->get_result();
        if ($row = $res_unread->fetch_assoc()) {
            $unread_count = $row['count'];
        }
        $stmt_unread->close();
    }

    // Fetch latest 5 notifications
    $stmt_notif = $conn->prepare("SELECT * FROM notifications WHERE user_type = 'admin' AND recipient_id = ? ORDER BY created_at DESC LIMIT 3");
    if ($stmt_notif) {
        $stmt_notif->bind_param("i", $admin_id);
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
    <title><?= $pageTitle ? $pageTitle . " - VMS Admin" : "VMS Admin"; ?></title>
    
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
                            <?= $unread_count ?>
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </button>
                    <!-- Notification Dropdown -->
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <?php if (count($notifs) > 0): ?>
                            <?php foreach ($notifs as $notif): ?>
                                <?php
                                    $icon = 'fa-info-circle';
                                    $textClass = 'text-primary';
                                    if ($notif['type'] == 'alert') {
                                        $icon = 'fa-exclamation-circle';
                                        $textClass = 'text-danger';
                                    } elseif ($notif['type'] == 'success') {
                                        $icon = 'fa-check-circle';
                                        $textClass = 'text-success';
                                    }
                                ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-start py-2" href="#">
                                        <i class="fas <?= $icon ?> <?= $textClass ?> me-2 mt-1" style="font-size: 0.8rem;"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small"><?= htmlspecialchars($notif['title']) ?></div>
                                            <div class="text-muted small"><?= time_elapsed_string($notif['created_at']) ?></div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><div class="dropdown-item text-center small text-muted">No notifications</div></li>
                        <?php endif; ?>

                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center text-primary small fw-semibold" href="/vaccination_management_system/admin/notifications/notifications.php">
                                View all notifications
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Admin Profile Link -->
                <a href="/vaccination_management_system/admin/profile/profile.php" class="text-decoration-none d-flex align-items-center gap-2 text-dark">
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
                </a>

            </div>
        </div>
    </div>
</nav>
