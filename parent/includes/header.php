<?php
// Current Page logic for Title
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
$pageTitle = ucwords(str_replace("_", " ", $currentPage));

// Fetch Notifications
$parent_id = $_SESSION['user_id'] ?? 0;
$notifs = [];
$unread_count = 0;

if (isset($conn) && $parent_id) {
    // Count unread
    $stmt_unread = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_type = 'parent' AND recipient_id = ? AND status = 'unread'");
    if ($stmt_unread) {
        $stmt_unread->bind_param("i", $parent_id);
        $stmt_unread->execute();
        $res_unread = $stmt_unread->get_result();
        if ($row = $res_unread->fetch_assoc()) {
            $unread_count = $row['count'];
        }
        $stmt_unread->close();
    }

    // Fetch latest 5 notifications
    $stmt_notif = $conn->prepare("SELECT * FROM notifications WHERE user_type = 'parent' AND recipient_id = ? ORDER BY created_at DESC LIMIT 3");
    if ($stmt_notif) {
        $stmt_notif->bind_param("i", $parent_id);
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
                                    
                                    if ($notif['type'] == 'vaccination') {
                                        $icon = 'fa-syringe';
                                        $colorClass = 'text-danger';
                                        $bgClass = 'bg-danger';
                                    } elseif ($notif['type'] == 'appointment') {
                                        $icon = 'fa-calendar-check';
                                        $colorClass = 'text-success';
                                        $bgClass = 'bg-success';
                                    } elseif ($notif['type'] == 'system') {
                                        $icon = 'fa-cog';
                                        $colorClass = 'text-secondary';
                                        $bgClass = 'bg-secondary';
                                    }
                                    
                                    // Time formatting
                                    $time_ago = function_exists('time_elapsed_string') 
                                        ? time_elapsed_string($notif['created_at']) 
                                        : date('M d, H:i', strtotime($notif['created_at']));
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
                                                <small class="text-muted"><?= $time_ago ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="text-center py-4">
                                <p class="text-muted small mb-0">No notifications found</p>
                            </li>
                        <?php endif; ?>
                        
                        <li class="text-center py-2 border-top">
                            <a href="/vaccination_management_system/parent/notifications/notifications.php" class="text-decoration-none small fw-semibold text-primary">View All Notifications</a>
                        </li>
                    </ul>
                </li>

                <!-- Profile Link -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 pe-0" href="/vaccination_management_system/parent/profile/profile.php">
                        <div class="d-none d-md-block text-end">
                            <p class="mb-0 small fw-semibold text-dark"><?= $_SESSION['name'] ?></p>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Parent</p>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>