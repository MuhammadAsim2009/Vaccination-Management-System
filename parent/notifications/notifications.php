<?php
/**
 * Parent Notifications Page
 * 
 * Displays notifications for the parent user including vaccination reminders,
 * appointment updates, missed vaccination alerts, and system messages.
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
        'type' => 'vaccination',
        'category' => 'vaccination',
        'title' => 'Upcoming Vaccination: Polio',
        'child_name' => 'Ali Khan',
        'vaccine_name' => 'Polio (OPV)',
        'due_date' => '2023-11-05',
        'message' => 'Reminder: Ali is due for the Polio vaccination on Nov 5th. Please schedule an appointment.',
        'time' => '2 hours ago',
        'datetime' => '2023-10-25 09:30',
        'status' => 'unread',
        'icon' => 'fa-syringe',
        'bg' => 'bg-soft-warning',
        'text' => 'text-warning'
    ],
    [
        'id' => 2,
        'type' => 'appointment',
        'category' => 'appointments',
        'title' => 'Appointment Confirmed',
        'child_name' => 'Sarah Connor',
        'vaccine_name' => 'Measles',
        'due_date' => '2023-10-28',
        'message' => 'Your appointment for Sarah (Measles) has been confirmed for Oct 28th at 10:00 AM at City Hospital.',
        'time' => '5 hours ago',
        'datetime' => '2023-10-25 06:15',
        'status' => 'unread',
        'icon' => 'fa-calendar-check',
        'bg' => 'bg-soft-success',
        'text' => 'text-success'
    ],
    [
        'id' => 3,
        'type' => 'missed',
        'category' => 'vaccination',
        'title' => 'Missed Vaccination Alert',
        'child_name' => 'John Doe',
        'vaccine_name' => 'BCG',
        'due_date' => '2023-10-20',
        'message' => 'URGENT: John missed the BCG vaccination scheduled for Oct 20th. Please reschedule immediately.',
        'time' => '1 day ago',
        'datetime' => '2023-10-24 14:00',
        'status' => 'unread',
        'icon' => 'fa-exclamation-circle',
        'bg' => 'bg-soft-danger',
        'text' => 'text-danger'
    ],
    [
        'id' => 4,
        'type' => 'system',
        'category' => 'system',
        'title' => 'System Maintenance',
        'child_name' => 'N/A',
        'vaccine_name' => 'N/A',
        'due_date' => 'N/A',
        'message' => 'The system will be undergoing maintenance on Saturday from 12 AM to 2 AM. Services will be unavailable.',
        'time' => '2 days ago',
        'datetime' => '2023-10-23 10:00',
        'status' => 'read',
        'icon' => 'fa-info-circle',
        'bg' => 'bg-soft-info',
        'text' => 'text-info'
    ],
    [
        'id' => 5,
        'type' => 'vaccination',
        'category' => 'vaccination',
        'title' => 'Vaccination Completed',
        'child_name' => 'Emma Watson',
        'vaccine_name' => 'Hepatitis B',
        'due_date' => '2023-10-22',
        'message' => 'Vaccination record updated: Emma has successfully received the Hepatitis B vaccine.',
        'time' => '3 days ago',
        'datetime' => '2023-10-22 11:30',
        'status' => 'read',
        'icon' => 'fa-check-circle',
        'bg' => 'bg-soft-success',
        'text' => 'text-success'
    ]
];

// Generate more dummy items
for ($i = 6; $i <= 10; $i++) {
    $types = ['vaccination', 'appointment', 'system'];
    $type = $types[array_rand($types)];
    $status = ($i % 2 == 0) ? 'read' : 'unread';
    
    $item = [
        'id' => $i,
        'type' => $type,
        'time' => $i . ' days ago',
        'datetime' => '2023-10-' . (25-$i) . ' 09:00',
        'status' => $status
    ];

    if ($type == 'vaccination') {
        $item['category'] = 'vaccination';
        $item['title'] = 'Upcoming Vaccination Reminder';
        $item['child_name'] = 'Ali Khan';
        $item['vaccine_name'] = 'DTP Booster';
        $item['due_date'] = '2023-11-15';
        $item['message'] = 'Reminder: DTP Booster is due soon for Ali. Please check the schedule.';
        $item['icon'] = 'fa-syringe';
        $item['bg'] = 'bg-soft-warning';
        $item['text'] = 'text-warning';
    } elseif ($type == 'appointment') {
        $item['category'] = 'appointments';
        $item['title'] = 'Appointment Rescheduled';
        $item['child_name'] = 'Sarah Connor';
        $item['vaccine_name'] = 'General Checkup';
        $item['due_date'] = '2023-11-01';
        $item['message'] = 'Your appointment has been successfully rescheduled to Nov 1st.';
        $item['icon'] = 'fa-calendar-alt';
        $item['bg'] = 'bg-soft-primary';
        $item['text'] = 'text-primary';
    } else {
        $item['category'] = 'system';
        $item['title'] = 'New Health Guidelines';
        $item['child_name'] = 'N/A';
        $item['vaccine_name'] = 'N/A';
        $item['due_date'] = 'N/A';
        $item['message'] = 'Ministry of Health has released new guidelines for child nutrition.';
        $item['icon'] = 'fa-file-medical-alt';
        $item['bg'] = 'bg-light';
        $item['text'] = 'text-secondary';
    }
    $notifications[] = $item;
}

// Calculate Stats
$total_notifs = count($notifications);
$unread_notifs = count(array_filter($notifications, fn($n) => $n['status'] === 'unread'));
$appointment_alerts = count(array_filter($notifications, fn($n) => $n['category'] === 'appointments'));
$system_alerts = count(array_filter($notifications, fn($n) => $n['category'] === 'system'));
?>

    <div class="container-fluid px-4">
        
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h3 class="mb-1 fw-bold text-dark">Notifications</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary btn-sm me-2" id="markAllReadBtn">
                            <i class="fas fa-check-double me-2"></i>Mark All as Read
                        </button>
                        <button class="btn btn-outline-danger btn-sm" id="clearAllBtn">
                            <i class="fas fa-trash-alt me-2"></i>Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Notifications -->
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Notifications</p>
                                <h3 class="mb-0 fw-bold text-dark" id="statTotal"><?= $total_notifs ?></h3>
                            </div>
                            <div class="stats-icon bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-bell text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-history me-1"></i>All time
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Unread Notifications -->
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Unread Messages</p>
                                <h3 class="mb-0 fw-bold text-dark" id="statUnread"><?= $unread_notifs ?></h3>
                            </div>
                            <div class="stats-icon bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-envelope-open-text text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-info">
                                <i class="fas fa-circle me-1"></i>Requires attention
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Alerts -->
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Appointment Alerts</p>
                                <h3 class="mb-0 fw-bold text-dark"><?= $appointment_alerts ?></h3>
                            </div>
                            <div class="stats-icon bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-check text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-success">
                                <i class="fas fa-clock me-1"></i>Check schedule
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Messages -->
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">System Messages</p>
                                <h3 class="mb-0 fw-bold text-dark"><?= $system_alerts ?></h3>
                            </div>
                            <div class="stats-icon bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-info-circle text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-primary">
                                <i class="fas fa-shield-alt me-1"></i>Updates & Info
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body p-2">
                <div class="d-flex flex-wrap gap-2" id="notificationFilters">
                    <button class="btn btn-sm btn-primary px-3 rounded-pill filter-btn active" data-filter="all">All</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="unread">Unread</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="vaccination">Vaccinations</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="appointments">Appointments</button>
                    <button class="btn btn-sm btn-light text-dark px-3 rounded-pill filter-btn" data-filter="system">System Messages</button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
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
                                    <div class="avatar-md rounded-circle d-flex align-items-center justify-content-center <?= $note['bg'] ?> <?= $note['text'] ?>" style="width: 48px; height: 48px;">
                                        <i class="fas <?= $note['icon'] ?> fa-lg"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1 min-width-0" style="cursor: pointer;" onclick="openModal(this)"
                                     data-title="<?= htmlspecialchars($note['title']) ?>"
                                     data-message="<?= htmlspecialchars($note['message']) ?>"
                                     data-time="<?= $note['time'] ?>"
                                     data-child="<?= htmlspecialchars($note['child_name']) ?>"
                                     data-vaccine="<?= htmlspecialchars($note['vaccine_name']) ?>"
                                     data-due="<?= $note['due_date'] ?>"
                                     data-icon="<?= $note['icon'] ?>"
                                     data-bg="<?= $note['bg'] ?>"
                                     data-category="<?= $note['category'] ?>">
                                    
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
                                    <button class="btn btn-icon btn-sm btn-light text-primary mark-read-btn" title="Mark as Read" data-bs-toggle="tooltip">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-icon btn-sm btn-light text-danger delete-btn" title="Delete" data-bs-toggle="tooltip">
                                        <i class="fas fa-trash-alt"></i>
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
                    <h5 class="text-muted fw-bold">No notifications found</h5>
                    <p class="text-muted small">You're all caught up! Check back later for updates.</p>
                </div>
            </div>
        </div>

    </div>

<!-- Notification Detail Modal -->
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
                        <small id="modalTime" class="text-muted"></small>
                    </div>
                </div>

                <!-- Details Table -->
                <div class="bg-light rounded-3 p-3 mb-3">
                    <div class="row g-2 mb-2">
                        <div class="col-4 text-muted small fw-bold">Child Name:</div>
                        <div class="col-8 text-dark small" id="modalChild"></div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-4 text-muted small fw-bold">Vaccine:</div>
                        <div class="col-8 text-dark small" id="modalVaccine"></div>
                    </div>
                    <div class="row g-2">
                        <div class="col-4 text-muted small fw-bold">Due Date:</div>
                        <div class="col-8 text-dark small" id="modalDue"></div>
                    </div>
                </div>

                <!-- Full Message -->
                <div class="p-3 border rounded-3">
                    <p id="modalMessage" class="mb-0 text-secondary"></p>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <a href="../vaccination/appointments.php" class="btn btn-primary rounded-pill px-4" id="modalActionBtn">
                    <i class="fas fa-calendar-check me-2"></i>Go to Booking
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Filter Logic
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

        // Mark as Read Logic
        function markAsRead(item) {
            if (item.getAttribute('data-status') === 'unread') {
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
            items.forEach(item => markAsRead(item));
        });

        // Delete Logic
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent modal opening
                if(confirm('Are you sure you want to delete this notification?')) {
                    const item = this.closest('.notification-item');
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
                items.forEach(item => item.remove());
                document.getElementById('statTotal').innerText = '0';
                document.getElementById('statUnread').innerText = '0';
                emptyState.classList.remove('d-none');
            }
        });

        // Modal Logic
        window.openModal = function(element) {
            // Get data
            const title = element.getAttribute('data-title');
            const message = element.getAttribute('data-message');
            const time = element.getAttribute('data-time');
            const child = element.getAttribute('data-child');
            const vaccine = element.getAttribute('data-vaccine');
            const due = element.getAttribute('data-due');
            const icon = element.getAttribute('data-icon');
            const bg = element.getAttribute('data-bg');
            const category = element.getAttribute('data-category');

            // Populate Modal
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('modalTime').innerText = time;
            document.getElementById('modalChild').innerText = child;
            document.getElementById('modalVaccine').innerText = vaccine;
            document.getElementById('modalDue').innerText = due;

            // Icon styling
            const modalIcon = document.getElementById('modalIcon');
            modalIcon.className = 'fas ' + icon;
            
            const modalIconBg = document.getElementById('modalIconBg');
            // Extract just the bg-soft class and text color
            modalIconBg.className = 'rounded-circle d-flex align-items-center justify-content-center me-3 ' + bg.replace('bg-soft-', 'bg-soft-').replace('text-', 'text-');
            // Add specific classes manually to ensure they apply if string manipulation fails
            modalIconBg.classList.add(bg.split(' ')[0]);
            if(bg.includes('text-')) modalIconBg.classList.add(bg.match(/text-[a-z]+/)[0]);

            // Action Button Logic
            const actionBtn = document.getElementById('modalActionBtn');
            if(category === 'system') {
                actionBtn.style.display = 'none';
            } else {
                actionBtn.style.display = 'inline-block';
            }

            // Mark as read when opening
            const item = element.closest('.notification-item');
            markAsRead(item);

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
