<?php
/**
 * Notifications Page
 * 
 * Displays system notifications for the hospital user including appointments,
 * stock alerts, and system messages.
 */

include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// --------------------------------------------------------------------------
// Dummy Data Generation
// --------------------------------------------------------------------------
$notifications = [
    [
        'id' => 1,
        'type' => 'appointment',
        'category' => 'appointments',
        'title' => 'New Appointment Request',
        'message' => 'Sarah Connor requested a vaccination appointment for Child: John.',
        'time' => '10 mins ago',
        'datetime' => '2023-10-25 09:30',
        'status' => 'unread',
        'icon' => 'fa-calendar-check',
        'bg' => 'bg-soft-primary',
        'text' => 'text-primary'
    ],
    [
        'id' => 2,
        'type' => 'alert',
        'category' => 'alerts',
        'title' => 'Low Stock Warning',
        'message' => 'Polio Vaccine stock is running low (less than 50 units). Please restock immediately.',
        'time' => '1 hour ago',
        'datetime' => '2023-10-25 08:45',
        'status' => 'unread',
        'icon' => 'fa-exclamation-triangle',
        'bg' => 'bg-soft-warning',
        'text' => 'text-warning'
    ],
    [
        'id' => 3,
        'type' => 'vaccine',
        'category' => 'vaccinations',
        'title' => 'Vaccination Completed',
        'message' => 'System verified vaccination record for Patient #4592 (Emma Watson).',
        'time' => '2 hours ago',
        'datetime' => '2023-10-25 07:15',
        'status' => 'read',
        'icon' => 'fa-syringe',
        'bg' => 'bg-soft-success',
        'text' => 'text-success'
    ],
    [
        'id' => 4,
        'type' => 'system',
        'category' => 'system',
        'title' => 'System Maintenance',
        'message' => 'Scheduled maintenance on Saturday, 12:00 AM - 02:00 AM. The system will be offline.',
        'time' => '1 day ago',
        'datetime' => '2023-10-24 14:00',
        'status' => 'read',
        'icon' => 'fa-server',
        'bg' => 'bg-soft-info',
        'text' => 'text-info'
    ],
    [
        'id' => 5,
        'type' => 'alert',
        'category' => 'alerts',
        'title' => 'Temperature Alert',
        'message' => 'Freezer B temperature deviation detected. Check sensor logs.',
        'time' => '2 days ago',
        'datetime' => '2023-10-23 11:20',
        'status' => 'read',
        'icon' => 'fa-temperature-high',
        'bg' => 'bg-soft-danger',
        'text' => 'text-danger'
    ]
];

// Generate additional dummy items to reach ~12 items
for ($i = 6; $i <= 12; $i++) {
    $types = ['appointment', 'vaccine', 'system'];
    $type = $types[array_rand($types)];
    $status = ($i % 3 == 0) ? 'unread' : 'read';
    
    $item = [
        'id' => $i,
        'type' => $type,
        'time' => $i . ' days ago',
        'datetime' => '2023-10-' . (25-$i) . ' 10:00',
        'status' => $status
    ];

    if ($type == 'appointment') {
        $item['category'] = 'appointments';
        $item['title'] = 'Appointment Update';
        $item['message'] = 'Patient updated their schedule preference for upcoming visit.';
        $item['icon'] = 'fa-calendar-alt';
        $item['bg'] = 'bg-soft-primary';
        $item['text'] = 'text-primary';
    } elseif ($type == 'vaccine') {
        $item['category'] = 'vaccinations';
        $item['title'] = 'Batch Expiry Warning';
        $item['message'] = 'Vaccine Batch #3922 expires in 30 days. Prioritize usage.';
        $item['icon'] = 'fa-vials';
        $item['bg'] = 'bg-soft-success';
        $item['text'] = 'text-success';
    } else {
        $item['category'] = 'system';
        $item['title'] = 'Policy Update';
        $item['message'] = 'New vaccination guidelines have been published by the admin.';
        $item['icon'] = 'fa-file-alt';
        $item['bg'] = 'bg-light';
        $item['text'] = 'text-secondary';
    }
    $notifications[] = $item;
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
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="appointments">Appointments</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="vaccinations">Vaccinations</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="alerts">Alerts</button>
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
                <div id="emptyState" class="text-center py-5 d-none">
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