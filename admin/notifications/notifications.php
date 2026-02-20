<?php
include '../../config/db.php';
include '../../config/functions.php';
include '../includes/auth_check.php';

// --- Handle AJAX Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $user_type = 'admin';

    if ($action === 'mark_read' && $id) {
        $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE id = ? AND user_type = ?");
        $stmt->bind_param("is", $id, $user_type);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }
    
    if ($action === 'delete' && $id) {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ? AND user_type = ?");
        $stmt->bind_param("is", $id, $user_type);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }

    if ($action === 'mark_all_read') {
        $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE user_type = ? AND status = 'unread'");
        $stmt->bind_param("s", $user_type);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }

    if ($action === 'clear_all') {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE user_type = ?");
        $stmt->bind_param("s", $user_type);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }
    exit;
}

include '../includes/header.php';
include '../includes/sidebar.php';

// Fetch Notifications from Database
$notifications = [];
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_type = 'admin' AND (recipient_id = ? OR recipient_id IS NULL) ORDER BY created_at DESC");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Map DB types to UI styles
    $icon = 'fa-bell';
    $color = 'primary';
    
    switch($row['type']) {
        case 'appointment': $icon = 'fa-calendar-check'; $color = 'warning'; break;
        case 'vaccination': $icon = 'fa-syringe'; $color = 'success'; break;
        case 'system': $icon = 'fa-info-circle'; $color = 'info'; break;
        case 'message': $icon = 'fa-envelope'; $color = 'secondary'; break;
    }

    $notifications[] = [
        'id' => $row['id'],
        'category' => $row['type'], // appointment, vaccination, system
        'title' => $row['title'],
        'message' => $row['message'],
        'time' => time_elapsed_string($row['created_at']),
        'datetime' => date('M d, Y h:i A', strtotime($row['created_at'])),
        'status' => $row['status'],
        'icon' => $icon,
        'color_class' => $color
    ];
}

// Calculate Stats
$total = count($notifications);
$unread = count(array_filter($notifications, fn($n) => $n['status'] === 'unread'));
$system_msgs = count(array_filter($notifications, fn($n) => $n['category'] === 'system'));
$appt_alerts = count(array_filter($notifications, fn($n) => $n['category'] === 'appointment'));
?>

<main class="main-content">
    <div class="container-fluid px-4 py-4">
        
        <!-- 1️⃣ Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="mb-1 fw-bold">Admin Notifications</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary shadow-sm" id="markAllReadBtn">
                    <i class="fas fa-check-double me-2"></i>Mark All as Read
                </button>
                <button class="btn btn-outline-danger shadow-sm" id="clearAllBtn">
                    <i class="fas fa-trash-alt me-2"></i>Clear All
                </button>
            </div>
        </div>

        <!-- 2️⃣ Stats Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Notifications -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Total Notifications</h6>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-bell fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0" id="statTotal"><?= $total ?></h2>
                        <small class="text-muted">All time history</small>
                    </div>
                </div>
            </div>

            <!-- Unread Notifications -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Unread Messages</h6>
                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-envelope-open-text fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0" id="statUnread"><?= $unread ?></h2>
                        <small class="text-danger fw-medium">Requires attention</small>
                    </div>
                </div>
            </div>

            <!-- Hospital Requests -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">System Alerts</h6>
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-server fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0"><?= $system_msgs ?></h2>
                        <small class="text-info fw-medium">Updates & Logs</small>
                    </div>
                </div>
            </div>

            <!-- Appointment Alerts -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Appointment Alerts</h6>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-calendar-day fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0"><?= $appt_alerts ?></h2>
                        <small class="text-warning fw-medium">Urgent updates</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3️⃣ Filters -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body p-2">
                <div class="d-flex flex-wrap gap-2" id="notificationFilters">
                    <button class="btn btn-sm btn-primary px-3 rounded-pill filter-btn active" data-filter="all">All</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="unread">Unread</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="appointment">Appointments</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="vaccination">Vaccinations</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="system">System</button>
                </div>
            </div>
        </div>

        <!-- 4️⃣ Notifications List -->
        <div class="row">
            <div class="col-12">
                <div class="list-group gap-3" id="notificationList">
                    <?php foreach ($notifications as $note): ?>
                        <div class="list-group-item border-0 shadow-sm rounded-4 p-3 notification-item card-hover <?= $note['status'] ?>" 
                             data-id="<?= $note['id'] ?>" 
                             data-category="<?= $note['category'] ?>" 
                             data-status="<?= $note['status'] ?>">
                            
                            <div class="d-flex align-items-start">
                                <!-- Icon -->
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-<?= $note['color_class'] ?> bg-opacity-10 text-<?= $note['color_class'] ?>" style="width: 48px; height: 48px;">
                                        <i class="fas <?= $note['icon'] ?> fa-lg"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1 min-width-0" style="cursor: pointer;" onclick="openModal(this)"
                                     data-title="<?= htmlspecialchars($note['title']) ?>"
                                     data-message="<?= htmlspecialchars($note['message']) ?>"
                                     data-time="<?= $note['time'] ?>"
                                     data-datetime="<?= $note['datetime'] ?>"
                                     data-icon="<?= $note['icon'] ?>"
                                     data-color="<?= $note['color_class'] ?>"
                                     data-category="<?= ucfirst($note['category']) ?>">
                                    
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate title-text"><?= $note['title'] ?></h6>
                                        <small class="text-muted ms-2 text-nowrap"><?= $note['time'] ?></small>
                                    </div>
                                    <p class="mb-1 text-muted small text-truncate message-text" style="max-width: 90%;"><?= $note['message'] ?></p>
                                    
                                    <div class="d-flex align-items-center mt-2">
                                        <?php if($note['status'] === 'unread'): ?>
                                            <span class="badge bg-primary rounded-pill me-2 status-badge">New</span>
                                        <?php endif; ?>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            <i class="far fa-clock me-1"></i> <?= $note['datetime'] ?>
                                        </small>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex-shrink-0 ms-3 d-flex flex-column gap-2">
                                    <button class="btn btn-sm btn-light text-primary mark-read-btn" title="Mark as Read" data-bs-toggle="tooltip">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger delete-btn" title="Delete" data-bs-toggle="tooltip">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 6️⃣ Empty State (Hidden by default) -->
                <div id="emptyState" class="text-center py-5 <?= empty($notifications) ? '' : 'd-none' ?>">
                    <div class="mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="far fa-bell-slash fa-3x text-muted"></i>
                        </div>
                    </div>
                    <h5 class="text-muted fw-bold">No notifications found</h5>
                    <p class="text-muted small">You're all caught up! Check back later for updates.</p>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- 5️⃣ Notification Detail Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Notification Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <!-- Header with Icon -->
                <div class="d-flex align-items-center mb-4">
                    <div id="modalIconBg" class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                        <i id="modalIcon" class="fas fa-lg"></i>
                    </div>
                    <div>
                        <h6 id="modalTitle" class="fw-bold mb-1 text-dark"></h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border" id="modalCategory"></span>
                            <small id="modalTime" class="text-muted"></small>
                        </div>
                    </div>
                </div>

                <!-- Full Message -->
                <div class="p-3 bg-light rounded-3 mb-3">
                    <p id="modalMessage" class="mb-0 text-secondary"></p>
                </div>
                
                <div class="text-end">
                    <small class="text-muted" id="modalDateTime"></small>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="modalMarkReadBtn">
                    <i class="fas fa-check me-2"></i>Mark as Read
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Filter Logic ---
        const filterBtns = document.querySelectorAll('.filter-btn');
        const items = document.querySelectorAll('.notification-item');
        const emptyState = document.getElementById('emptyState');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Toggle active class
                filterBtns.forEach(b => {
                    b.classList.remove('btn-primary', 'active');
                    b.classList.add('btn-light', 'text-dark');
                });
                btn.classList.remove('btn-light', 'text-dark');
                btn.classList.add('btn-primary', 'active');

                const filter = btn.getAttribute('data-filter');
                let visibleCount = 0;

                items.forEach(item => {
                    const category = item.getAttribute('data-category');
                    const status = item.getAttribute('data-status');

                    let show = false;
                    if (filter === 'all') show = true;
                    else if (filter === 'unread' && status === 'unread') show = true;
                    else if (filter === category) show = true;

                    if (show) {
                        item.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        item.classList.add('d-none');
                    }
                });

                // Toggle Empty State
                if (visibleCount === 0) {
                    emptyState.classList.remove('d-none');
                } else {
                    emptyState.classList.add('d-none');
                }
            });
        });

        // --- Mark as Read Logic ---
        function markAsRead(item) {
            if (item.getAttribute('data-status') === 'unread') {
                const id = item.getAttribute('data-id');
                
                // AJAX Call
                const formData = new FormData();
                formData.append('action', 'mark_read');
                formData.append('id', id);
                fetch('notifications.php', { method: 'POST', body: formData });

                // UI Update
                item.setAttribute('data-status', 'read');
                item.classList.remove('unread');
                item.classList.add('read');
                item.style.opacity = '0.8';

                // Remove badge
                const badge = item.querySelector('.status-badge');
                if(badge) badge.remove();

                // Update Counter
                const counter = document.getElementById('statUnread');
                let count = parseInt(counter.innerText);
                if(count > 0) counter.innerText = count - 1;
            }
        }

        document.querySelectorAll('.mark-read-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent modal opening
                const item = this.closest('.notification-item');
                markAsRead(item);
            });
        });

        // Mark All as Read
        document.getElementById('markAllReadBtn').addEventListener('click', () => {
            // AJAX Call
            const formData = new FormData();
            formData.append('action', 'mark_all_read');
            fetch('notifications.php', { method: 'POST', body: formData });
            
            items.forEach(item => markAsRead(item));
        });

        // --- Delete Logic ---
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent modal opening
                if(confirm('Are you sure you want to delete this notification?')) {
                    const item = this.closest('.notification-item');
                    const id = item.getAttribute('data-id');

                    // AJAX Call
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', id);
                    fetch('notifications.php', { method: 'POST', body: formData });
                    
                    // If deleting unread, update counter
                    if(item.getAttribute('data-status') === 'unread') {
                        const counter = document.getElementById('statUnread');
                        let count = parseInt(counter.innerText);
                        if(count > 0) counter.innerText = count - 1;
                    }

                    item.remove();
                    
                    // Update Total Counter
                    const totalCounter = document.getElementById('statTotal');
                    totalCounter.innerText = parseInt(totalCounter.innerText) - 1;

                    // Check if list is empty
                    const visibleItems = document.querySelectorAll('.notification-item:not(.d-none)');
                    if(visibleItems.length === 0) emptyState.classList.remove('d-none');
                }
            });
        });

        // Clear All
        document.getElementById('clearAllBtn').addEventListener('click', () => {
            if(confirm('Are you sure you want to clear all notifications?')) {
                // AJAX Call
                const formData = new FormData();
                formData.append('action', 'clear_all');
                fetch('notifications.php', { method: 'POST', body: formData });

                items.forEach(item => item.remove());
                document.getElementById('statTotal').innerText = '0';
                document.getElementById('statUnread').innerText = '0';
                emptyState.classList.remove('d-none');
            }
        });

        // --- Modal Logic ---
        window.openModal = function(element) {
            // Get data
            const title = element.getAttribute('data-title');
            const message = element.getAttribute('data-message');
            const time = element.getAttribute('data-time');
            const datetime = element.getAttribute('data-datetime');
            const icon = element.getAttribute('data-icon');
            const color = element.getAttribute('data-color');
            const category = element.getAttribute('data-category');

            // Populate Modal
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('modalTime').innerText = time;
            document.getElementById('modalDateTime').innerText = datetime;
            document.getElementById('modalCategory').innerText = category;

            // Icon styling
            const modalIcon = document.getElementById('modalIcon');
            modalIcon.className = 'fas ' + icon;
            
            const modalIconBg = document.getElementById('modalIconBg');
            modalIconBg.className = `rounded-circle d-flex align-items-center justify-content-center me-3 bg-${color} bg-opacity-10 text-${color}`;

            // Handle "Mark as Read" inside modal
            const item = element.closest('.notification-item');
            const modalMarkReadBtn = document.getElementById('modalMarkReadBtn');
            
            modalMarkReadBtn.onclick = function() {
                markAsRead(item);
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('notificationModal'));
                modalInstance.hide();
            };

            // Show Modal
            const modal = new bootstrap.Modal(document.getElementById('notificationModal'));
            modal.show();
        };

        // Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

<?php include '../includes/footer.php'; ?>
