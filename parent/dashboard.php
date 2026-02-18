<?php
// Include DB connection
include '../config/db.php';

// Include authentication check
include 'includes/auth_check.php';

// Include header
include 'includes/header.php';

// Include sidebar
include 'includes/sidebar.php';

// --- Dynamic Data Fetching ---
$parent_id = $_SESSION['user_id'];

// 1. Total Children
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM children WHERE parent_id = ?");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$total_children = $stmt->get_result()->fetch_assoc()['count'];

// 2. Vaccination Stats (Global)
$stats = ['total' => 0, 'completed' => 0, 'pending' => 0, 'missed' => 0];
$stmt = $conn->prepare("SELECT vs.status, COUNT(*) as count 
    FROM vaccination_schedule vs
    JOIN children c ON vs.child_id = c.id
    WHERE c.parent_id = ?
    GROUP BY vs.status
");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()){
    if($row['status'] == 'vaccinated') $stats['completed'] += $row['count'];
    elseif($row['status'] == 'pending') $stats['pending'] += $row['count'];
    elseif($row['status'] == 'missed') $stats['missed'] += $row['count'];
    $stats['total'] += $row['count'];
}
$completion_rate = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0;

// 3. Upcoming Reminders (Next 3 pending)
$reminders = [];
$stmt = $conn->prepare("SELECT c.name as child_name, v.vaccine_name, vs.scheduled_date
    FROM vaccination_schedule vs
    JOIN children c ON vs.child_id = c.id
    JOIN vaccines v ON vs.vaccine_id = v.id
    WHERE c.parent_id = ? AND vs.status = 'pending' AND vs.scheduled_date >= CURDATE()
    ORDER BY vs.scheduled_date ASC
    LIMIT 3
");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $due_date = new DateTime($row['scheduled_date']);
    $today = new DateTime();
    $today->setTime(0,0,0); // Reset time for accurate day diff
    $due_date->setTime(0,0,0);
    $interval = $today->diff($due_date);
    $days_left = (int)$interval->format('%r%a');
    
    $row['days_left'] = $days_left;
    $reminders[] = $row;
}

// 4. Recent History (Last 5 records)
$history = [];
$stmt = $conn->prepare("SELECT c.name as child_name, v.vaccine_name, vs.scheduled_date, h.hospital_name, vs.status
    FROM vaccination_schedule vs
    JOIN children c ON vs.child_id = c.id
    JOIN vaccines v ON vs.vaccine_id = v.id
    LEFT JOIN hospitals h ON vs.hospital_id = h.id
    WHERE c.parent_id = ?
    ORDER BY vs.created_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $history[] = $row;
}

// 5. Chart Data (Per Child)
$chart_data = [];
$stmt = $conn->prepare("SELECT id, name FROM children WHERE parent_id = ?");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$res_c = $stmt->get_result();

$js_labels = [];
$js_completed = [];
$js_pending = [];
$js_missed = [];

while($child = $res_c->fetch_assoc()) {
    $js_labels[] = $child['name'];
    
    // Get stats for this child
    $stmt_s = $conn->prepare("SELECT status, COUNT(*) as count FROM vaccination_schedule WHERE child_id = ? GROUP BY status");
    $stmt_s->bind_param("i", $child['id']);
    $stmt_s->execute();
    $res_s = $stmt_s->get_result();
    
    $c_comp = 0; $c_pend = 0; $c_miss = 0;
    while($s = $res_s->fetch_assoc()) {
        if($s['status'] == 'vaccinated') $c_comp = $s['count'];
        elseif($s['status'] == 'pending') $c_pend = $s['count'];
        elseif($s['status'] == 'missed') $c_miss = $s['count'];
    }
    $js_completed[] = $c_comp;
    $js_pending[] = $c_pend;
    $js_missed[] = $c_miss;
}
?>

<!-- Page Content -->
<div class="container-fluid px-4 ">
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="mb-1 fw-bold text-dark">Parent Dashboard</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Overview</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <h5 class="mb-0 text-muted">
                        <i class="fas fa-user-circle me-2"></i>Welcome, <span class="text-primary"><?= $_SESSION['name'] ?></span>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Section -->
    <div class="row g-4 mb-4">
        
        <!-- Total Children Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Children</p>
                            <h3 class="mb-0 fw-bold text-dark"><?= $total_children ?></h3>
                        </div>
                        <div class="stats-icon bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-child text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i>All registered
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Vaccinations Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Upcoming Vaccinations</p>
                            <h3 class="mb-0 fw-bold text-dark"><?= $stats['pending'] ?></h3>
                        </div>
                        <div class="stats-icon bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar-check text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-warning">
                            <i class="fas fa-clock me-1"></i>Next in 3 days
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Vaccinations Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Completed Vaccinations</p>
                            <h3 class="mb-0 fw-bold text-dark"><?= $stats['completed'] ?></h3>
                        </div>
                        <div class="stats-icon bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i><?= $completion_rate ?>% completion rate
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Missed Vaccinations Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Missed Vaccinations</p>
                            <h3 class="mb-0 fw-bold text-dark"><?= $stats['missed'] ?></h3>
                        </div>
                        <div class="stats-icon bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-danger">
                            <i class="fas fa-exclamation-circle me-1"></i>Requires attention
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Second Row: Reminder & Chart -->
    <div class="row g-4 mb-4">
        
        <!-- Upcoming Vaccination Reminder -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-bell text-warning me-2"></i>Upcoming Reminder
                    </h5>
                </div>
                <div class="card-body">
                    
                    <?php if(empty($reminders)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle mb-2 fs-4"></i><br>No upcoming reminders
                        </div>
                    <?php else: ?>
                    <?php foreach($reminders as $rem): 
                        $badge_cls = 'bg-success text-white';
                        if($rem['days_left'] <= 3) $badge_cls = 'bg-warning text-dark';
                        elseif($rem['days_left'] <= 7) $badge_cls = 'bg-info text-dark';
                        
                        $days_text = $rem['days_left'] == 0 ? 'Today' : $rem['days_left'] . ' days';
                    ?>
                    <div class="reminder-item p-3 mb-3 rounded-3" style="background: linear-gradient(135deg, rgba(74, 144, 226, 0.05) 0%, rgba(80, 200, 120, 0.05) 100%);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($rem['child_name']) ?></h6>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-syringe me-1"></i><?= htmlspecialchars($rem['vaccine_name']) ?>
                                </p>
                            </div>
                            <span class="badge <?= $badge_cls ?>"><?= $days_text ?></span>
                        </div>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-calendar me-1"></i>Due: <?= date('M d, Y', strtotime($rem['scheduled_date'])) ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                </div>
                <div class="card-footer bg-white border-0 pb-4">
                    <a href="vaccination/vaccination_dates.php" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-calendar-alt me-2"></i>View Full Schedule
                    </a>
                </div>
            </div>
        </div>

        <!-- Vaccination Progress Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Vaccination Progress
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="vaccinationChart" height="100"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Vaccination History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-history text-primary me-2"></i>Recent Vaccination History
                        </h5>
                        <a href="vaccination/vaccination_report.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-medical me-1"></i>View Full Report
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Child Name</th>
                                    <th>Vaccine Name</th>
                                    <th>Date</th>
                                    <th>Hospital</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($history)): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No recent activity found.</td></tr>
                                <?php else: ?>
                                <?php foreach($history as $rec): 
                                    $status_display = ucfirst($rec['status']);
                                    $badge_class = 'bg-secondary';
                                    if($rec['status'] == 'vaccinated') { $status_display = 'Completed'; $badge_class = 'bg-success'; }
                                    elseif($rec['status'] == 'pending') { $status_display = 'Pending'; $badge_class = 'bg-warning text-dark'; }
                                    elseif($rec['status'] == 'missed') { $status_display = 'Missed'; $badge_class = 'bg-danger'; }
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-child text-primary"></i>
                                            </div>
                                            <span class="fw-semibold"><?= htmlspecialchars($rec['child_name']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($rec['vaccine_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($rec['scheduled_date'])) ?></td>
                                    <td><?= htmlspecialchars($rec['hospital_name'] ?? 'Not Assigned') ?></td>
                                    <td><span class="badge <?= $badge_class ?>"><?= $status_display ?></span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewHistoryModal"
                                            data-child="<?= htmlspecialchars($rec['child_name']) ?>"
                                            data-vaccine="<?= htmlspecialchars($rec['vaccine_name']) ?>"
                                            data-date="<?= date('M d, Y', strtotime($rec['scheduled_date'])) ?>"
                                            data-hospital="<?= htmlspecialchars($rec['hospital_name'] ?? 'Not Assigned') ?>"
                                            data-status="<?= $status_display ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- History Details Modal -->
<div class="modal fade" id="viewHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Vaccination Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="text-center mb-4">
                    <div class="avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="fas fa-syringe fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-1" id="modalVaccine"></h5>
                    <span class="badge" id="modalStatusBadge"></span>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Child Name</label>
                        <div class="fw-medium text-dark" id="modalChild"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Date</label>
                        <div class="fw-medium text-dark" id="modalDate"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Hospital</label>
                        <div class="fw-medium text-dark" id="modalHospital"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Status</label>
                        <div class="fw-medium text-dark" id="modalStatusText"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Vaccination Progress Chart
    const ctx = document.getElementById('vaccinationChart').getContext('2d');
    const vaccinationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($js_labels) ?>,
            datasets: [
                {
                    label: 'Completed',
                    data: <?= json_encode($js_completed) ?>,
                    backgroundColor: 'rgba(80, 200, 120, 0.8)',
                    borderColor: 'rgba(80, 200, 120, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                },
                {
                    label: 'Pending',
                    data: <?= json_encode($js_pending) ?>,
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                },
                {
                    label: 'Missed',
                    data: <?= json_encode($js_missed) ?>,
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            family: "'Inter', 'Segoe UI', sans-serif"
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        stepSize: 2,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Modal Logic
    const historyModal = document.getElementById('viewHistoryModal');
    if (historyModal) {
        historyModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const child = button.getAttribute('data-child');
            const vaccine = button.getAttribute('data-vaccine');
            const date = button.getAttribute('data-date');
            const hospital = button.getAttribute('data-hospital');
            const status = button.getAttribute('data-status');

            historyModal.querySelector('#modalChild').textContent = child;
            historyModal.querySelector('#modalVaccine').textContent = vaccine;
            historyModal.querySelector('#modalDate').textContent = date;
            historyModal.querySelector('#modalHospital').textContent = hospital;
            historyModal.querySelector('#modalStatusText').textContent = status;
            
            const statusBadge = historyModal.querySelector('#modalStatusBadge');
            statusBadge.textContent = status;
            
            // Reset classes
            statusBadge.className = 'badge rounded-pill px-3';
            if(status === 'Completed') {
                statusBadge.classList.add('bg-success-subtle', 'text-success', 'border', 'border-success', 'border-opacity-25');
            } else if(status === 'Pending') {
                statusBadge.classList.add('bg-warning-subtle', 'text-warning', 'border', 'border-warning', 'border-opacity-25');
            } else {
                statusBadge.classList.add('bg-danger-subtle', 'text-danger', 'border', 'border-danger', 'border-opacity-25');
            }
        });
    }
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
