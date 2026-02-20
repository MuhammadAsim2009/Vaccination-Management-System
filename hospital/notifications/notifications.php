<?php
include '../../config/db.php';
include '../../config/functions.php';
include '../includes/auth_check.php';

// --- Handle AJAX Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $user_type = 'hospital';
    $user_id = $_SESSION['user_id'];

    if ($action === 'mark_read' && $id) {
        $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE id = ? AND user_type = ? AND (recipient_id = ? OR recipient_id IS NULL)");
        $stmt->bind_param("isi", $id, $user_type, $user_id);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }
    
    if ($action === 'delete' && $id) {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ? AND user_type = ? AND (recipient_id = ? OR recipient_id IS NULL)");
        $stmt->bind_param("isi", $id, $user_type, $user_id);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }

    if ($action === 'mark_all_read') {
        $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE user_type = ? AND (recipient_id = ? OR recipient_id IS NULL) AND status = 'unread'");
        $stmt->bind_param("si", $user_type, $user_id);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }

    if ($action === 'clear_all') {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE user_type = ? AND (recipient_id = ? OR recipient_id IS NULL)");
        $stmt->bind_param("si", $user_type, $user_id);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }
    exit;
}

include '../includes/header.php';
include '../includes/sidebar.php';

// Fetch Notifications from Database
$notifications = [];
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_type = 'hospital' AND (recipient_id = ? OR recipient_id IS NULL) ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Map DB types to UI styles
    $icon = 'fa-bell';
    $bg = 'bg-soft-primary';
    $text = 'text-primary';
    
    switch($row['type']) {
        case 'appointment': $icon = 'fa-calendar-check'; $bg = 'bg-soft-primary'; $text = 'text-primary'; break;
        case 'vaccination': $icon = 'fa-syringe'; $bg = 'bg-soft-success'; $text = 'text-success'; break;
        case 'system': $icon = 'fa-server'; $bg = 'bg-soft-info'; $text = 'text-info'; break;
        case 'message': $icon = 'fa-envelope'; $bg = 'bg-soft-warning'; $text = 'text-warning'; break;
    }

    $notifications[] = [
        'id' => $row['id'],
        'category' => $row['type'], // appointment, vaccination, system, message
        'title' => $row['title'],
        'message' => $row['message'],
        'time' => time_elapsed_string($row['created_at']),
        'datetime' => date('M d, Y h:i A', strtotime($row['created_at'])),
        'status' => $row['status'],
        'icon' => $icon,
        'bg' => $bg,
        'text' => $text
    ];
}
?>

<main class="mt-5 pt-3">
    <div class="container-fluid">
        
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                            </ol>
                        </nav>
                        <h2 class="fw-bold text-primary">Notifications</h2>
                        <p class="text-muted">Manage your system alerts and messages</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-danger btn-sm" id="clearAllBtn">
                            <i class="fas fa-trash-alt me-2"></i>Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-2">
                <div class="d-flex flex-wrap gap-2" id="notificationFilters">
                    <button class="btn btn-sm btn-primary px-3 rounded-pill filter-btn active" data-filter="all">All</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="unread">Unread</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="appointment">Appointments</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="vaccination">Vaccinations</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="message">Messages</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="system">System</button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="row">
            <div class="col-12">
                <div class="list-group gap-3" id="notificationList">
                    <?php foreach ($notifications as $note): ?>
                        <div class="list-group-item border-0 shadow-sm rounded-3 p-3 notification-item card-hover <?= $note['status'] ?>" 
                             data-id="<?= $note['id'] ?>" 
                             data-category="<?= $note['category'] ?>" 
                             data-status="<?= $note['status'] ?>">
                            
                            <div class="d-flex align-items-start">
                                <!-- Icon -->
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-md rounded-circle d-flex align-items-center justify-content-center <?= $note['bg'] ?> <?= $note['text'] ?>">
                                        <i class="fas <?= $note['icon'] ?> fa-lg"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1 min-width-0" data-bs-toggle="modal" data-bs-target="#notificationModal">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate title-text"><?= $note['title'] ?></h6>
                                        <small class="text-muted ms-2 text-nowrap"><?= $note['time'] ?></small>
                                    </div>
                                    <p class="mb-1 text-muted small text-truncate message-text" style="max-width: 90%;"><?= $note['message'] ?></p>
                                    
                                    <div class="d-flex align-items-center mt-2">
                                        <?php if($note['status'] === 'unread'): ?>
                                            <span class="badge bg-primary rounded-pill me-2 status-badge">New</span>
                                        <?php endif; ?>
                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="far fa-clock me-1"></i> <?= $note['datetime'] ?></small>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex-shrink-0 ms-3 d-flex flex-column gap-2">
                                    <button class="btn btn-icon btn-sm btn-light text-primary mark-read-btn" title="Mark as Read" data-bs-toggle="tooltip">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-icon btn-sm btn-light text-danger delete-btn" title="Delete" data-bs-toggle="tooltip">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Empty State (Hidden by default) -->
                <div id="emptyState" class="text-center py-5 <?= empty($notifications) ? '' : 'd-none' ?>">
                    <div class="mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="far fa-bell-slash fa-3x text-muted"></i>
                        </div>
                    </div>
                    <h5 class="text-muted">No notifications found</h5>
                    <p class="text-muted small">You're all caught up!</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Notification Detail Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Notification Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="d-flex align-items-center mb-4">
                        <div id="modalIconBg" class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                            <i id="modalIcon" class="fas fa-lg"></i>
                        </div>
                        <div>
                            <h6 id="modalTitle" class="fw-bold mb-1 text-dark"></h6>
                            <small id="modalTime" class="text-muted"></small>
                        </div>
                    </div>
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <p id="modalMessage" class="mb-0 text-secondary"></p>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modalMarkRead">Mark as Read</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts for Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // Filters Logic
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

            // Mark as Read Functionality
            function markAsRead(item) {
                const id = item.getAttribute('data-id');
                
                // AJAX Call
                const formData = new FormData();
                formData.append('action', 'mark_read');
                formData.append('id', id);
                fetch('notifications.php', { method: 'POST', body: formData });

                item.setAttribute('data-status', 'read');
                item.classList.remove('unread');
                item.classList.add('read');
                const badge = item.querySelector('.status-badge');
                if(badge) badge.remove();
                item.style.opacity = '0.8';
            }

            document.querySelectorAll('.mark-read-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const item = this.closest('.notification-item');
                    markAsRead(item);
                });
            });

            // Delete Functionality
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if(confirm('Delete this notification?')) {
                        const item = this.closest('.notification-item');
                        const id = item.getAttribute('data-id');

                        // AJAX Call
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('id', id);
                        fetch('notifications.php', { method: 'POST', body: formData });

                        item.remove();
                        // Check if list is empty after delete
                        const visibleItems = document.querySelectorAll('.notification-item:not(.d-none)');
                        if(visibleItems.length === 0) emptyState.classList.remove('d-none');
                    }
                });
            });

            // Clear All Functionality
            document.getElementById('clearAllBtn').addEventListener('click', () => {
                if(confirm('Are you sure you want to clear all notifications?')) {
                    // AJAX Call
                    const formData = new FormData();
                    formData.append('action', 'clear_all');
                    fetch('notifications.php', { method: 'POST', body: formData });

                    items.forEach(item => item.remove());
                    emptyState.classList.remove('d-none');
                }
            });

            // Modal Population
            const modal = document.getElementById('notificationModal');
            modal.addEventListener('show.bs.modal', function (event) {
                // Trigger can be the card itself or a button inside
                let trigger = event.relatedTarget;
                if (!trigger.classList.contains('notification-item')) {
                    trigger = trigger.closest('.notification-item');
                }
                
                const title = trigger.querySelector('.title-text').textContent;
                const message = trigger.querySelector('.message-text').textContent;
                const time = trigger.querySelector('.text-muted.ms-2').textContent;
                const iconClass = trigger.querySelector('.avatar-md i').className;
                const bgClass = trigger.querySelector('.avatar-md').className;

                document.getElementById('modalTitle').textContent = title;
                document.getElementById('modalMessage').textContent = message;
                document.getElementById('modalTime').textContent = time;
                
                const modalIcon = document.getElementById('modalIcon');
                modalIcon.className = iconClass;
                
                const modalIconBg = document.getElementById('modalIconBg');
                // Extract just the bg-soft class and text color
                modalIconBg.className = 'rounded-circle d-flex align-items-center justify-content-center me-3 ' + bgClass.replace('avatar-md', '').replace('rounded-circle', '');
                
                // Handle "Mark as Read" inside modal
                const markReadBtn = document.getElementById('modalMarkRead');
                markReadBtn.onclick = function() {
                    markAsRead(trigger);
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    modalInstance.hide();
                };
            });
            
            // Initialize Bootstrap Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

<?php include '../includes/footer.php'; ?>