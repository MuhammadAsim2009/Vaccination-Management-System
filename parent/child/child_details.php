<?php
// Include authentication and layout files
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// 1. Get Child ID and Validate
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo "<script>window.location.href='children_list.php';</script>";
    exit();
}
$child_id = (int)$_GET['id'];
$parent_id = $_SESSION['user_id'];

// 2. Fetch Child Details
$sql_child = "SELECT c.*, u.name as parent_name, u.email as parent_email, p.phone as parent_phone, p.address 
              FROM children c 
              JOIN users u ON c.parent_id = u.id 
              LEFT JOIN parents p ON u.id = p.user_id 
              WHERE c.id = ? AND c.parent_id = ?";

$stmt = $conn->prepare($sql_child);
$stmt->bind_param("ii", $child_id, $parent_id);
$stmt->execute();
$result = $stmt->get_result();
$child_data = $result->fetch_assoc();

if (!$child_data) {
    echo "<script>window.location.href='children_list.php?error=not_found';</script>";
    exit();
}

// Calculate Age
$dob = new DateTime($child_data['date_of_birth']);
$now = new DateTime();
$age_interval = $now->diff($dob);
$age_display = $age_interval->y > 0 ? $age_interval->y . ' Years' : ($age_interval->m > 0 ? $age_interval->m . ' Months' : $age_interval->d . ' Days');

// 3. Fetch Vaccination Stats
$sql_stats = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'vaccinated' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'missed' THEN 1 ELSE 0 END) as missed
              FROM vaccination_schedule 
              WHERE child_id = ?";
$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->bind_param("i", $child_id);
$stmt_stats->execute();
$stats_result = $stmt_stats->get_result()->fetch_assoc();

$vaccination_stats = [
    'total' => (int)$stats_result['total'],
    'completed' => (int)$stats_result['completed'],
    'pending' => (int)$stats_result['pending'],
    'missed' => (int)$stats_result['missed'],
    'percentage' => 0
];
if ($vaccination_stats['total'] > 0) {
    $vaccination_stats['percentage'] = round(($vaccination_stats['completed'] / $vaccination_stats['total']) * 100);
}

// 4. Fetch Vaccination History (Completed/Vaccinated/Missed)
$history_records = [];
$sql_history = "SELECT vs.*, v.vaccine_name, h.hospital_name 
                FROM vaccination_schedule vs 
                LEFT JOIN vaccines v ON vs.vaccine_id = v.id 
                LEFT JOIN hospitals h ON vs.hospital_id = h.id 
                WHERE vs.child_id = ? AND vs.status IN ('vaccinated', 'missed', 'completed') 
                ORDER BY vs.created_at DESC";

if ($stmt_hist = $conn->prepare($sql_history)) {
    $stmt_hist->bind_param("i", $child_id);
    $stmt_hist->execute();
    $res_hist = $stmt_hist->get_result();
    while ($row = $res_hist->fetch_assoc()) {
        $history_records[] = $row;
    }
}

// 5. Fetch Upcoming Schedule (Pending)
$upcoming_records = [];
$sql_upcoming = "SELECT vs.*, v.vaccine_name, h.hospital_name 
                 FROM vaccination_schedule vs 
                 LEFT JOIN vaccines v ON vs.vaccine_id = v.id 
                 LEFT JOIN hospitals h ON vs.hospital_id = h.id 
                 WHERE vs.child_id = ? AND vs.status = 'pending' 
                 ORDER BY vs.scheduled_date ASC";

if ($stmt_up = $conn->prepare($sql_upcoming)) {
    $stmt_up->bind_param("i", $child_id);
    $stmt_up->execute();
    $res_up = $stmt_up->get_result();
    while ($row = $res_up->fetch_assoc()) {
        $upcoming_records[] = $row;
    }
}

// Prepare display variables
$child_name = htmlspecialchars($child_data['name']);
$child_id_display = 'CH-' . $child_data['id'];
$child_dob = date('M d, Y', strtotime($child_data['date_of_birth']));
$child_gender = htmlspecialchars($child_data['gender']);
$child_blood = htmlspecialchars($child_data['blood_group']);
$parent_name = htmlspecialchars($child_data['parent_name']);
$parent_phone = htmlspecialchars($child_data['parent_phone'] ?? 'N/A');
$parent_email = htmlspecialchars($child_data['parent_email']);
$address = htmlspecialchars($child_data['address'] ?? 'Not Recorded');

$avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($child_name) . "&background=e3f2fd&color=1976d2&size=128&bold=true";
?>

<!-- Main Content Container -->
<div class="container-fluid px-4">

    <!-- 1️⃣ Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Child Profile</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="children_list.php" class="text-decoration-none">Children</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Child Profile</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="children_list.php" class="btn btn-outline-secondary me-2 shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="update_child.php?id=<?= $child_id ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-edit me-2"></i>Edit Child
            </a>
        </div>
    </div>

    <!-- 2️⃣ Child Profile Summary Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <!-- Avatar -->
                <div class="col-lg-auto text-center text-lg-start mb-3 mb-lg-0">
                    <div class="position-relative d-inline-block">
                        <img src="<?= $avatar_url ?>" alt="Child Avatar" class="rounded-circle border border-4 border-white shadow-sm" width="100" height="100">
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle" data-bs-toggle="tooltip" title="Active Status">
                            <span class="visually-hidden">Active</span>
                        </span>
                    </div>
                </div>
                <!-- Name & ID -->
                <div class="col-lg-5 text-center text-lg-start mb-3 mb-lg-0">
                    <h4 class="fw-bold text-dark mb-1"><?= $child_name ?></h4>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2 mb-2">
                        <span class="badge bg-light text-dark border"><i class="fas fa-id-card me-1 text-muted"></i> <?= $child_id_display ?></span>
                        <span class="badge bg-light text-dark border"><i class="fas fa-birthday-cake me-1 text-muted"></i> <?= $age_display ?></span>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-map-marker-alt me-1"></i> <?= $address ?>
                    </p>
                </div>
                <!-- Quick Stats -->
                <div class="col-lg-auto ms-lg-auto">
                    <div class="d-flex gap-3 justify-content-center">
                        <div class="text-center px-3 border-end">
                            <div class="small text-muted fw-bold text-uppercase">Gender</div>
                            <div class="fw-bold text-dark">
                                <?php if($child_gender == 'Male'): ?>
                                    <i class="fas fa-mars text-primary me-1"></i>
                                <?php else: ?>
                                    <i class="fas fa-venus text-danger me-1"></i>
                                <?php endif; ?>
                                <?= $child_gender ?>
                            </div>
                        </div>
                        <div class="text-center px-3 border-end">
                            <div class="small text-muted fw-bold text-uppercase">Blood</div>
                            <div class="fw-bold text-dark"><i class="fas fa-tint text-danger me-1"></i><?= $child_blood ?></div>
                        </div>
                        <div class="text-center px-3">
                            <div class="small text-muted fw-bold text-uppercase">Status</div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <!-- Left Column: Info & Medical -->
        <div class="col-lg-4">
            
            <!-- 3️⃣ Basic Information Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 h-auto">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between border-bottom-0 pb-1">
                            <span class="text-muted small">Date of Birth</span>
                            <span class="fw-medium text-dark"><?= $child_dob ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between border-bottom-0 py-1">
                            <span class="text-muted small">Parent Name</span>
                            <span class="fw-medium text-dark"><?= $parent_name ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between border-bottom-0 py-1">
                            <span class="text-muted small">Phone</span>
                            <span class="fw-medium text-dark"><?= $parent_phone ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between border-bottom-0 py-1">
                            <span class="text-muted small">Email</span>
                            <span class="fw-medium text-dark"><?= $parent_email ?></span>
                        </li>
                        <li class="list-group-item px-0 pt-2">
                            <span class="text-muted small d-block mb-1">Address</span>
                            <span class="fw-medium text-dark d-block small"><i class="fas fa-home me-1 text-muted"></i><?= $address ?></span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- Right Column: Stats, Charts, History -->
        <div class="col-lg-8">
            
            <!-- Stats & Chart Row -->
            <div class="row g-4 mb-4">
                
                <!-- 5️⃣ Vaccination Progress Overview -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-pie me-2 text-primary"></i>Vaccination Progress</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4 mt-2">
                                <h2 class="display-6 fw-bold text-primary mb-0"><?= $vaccination_stats['percentage'] ?>%</h2>
                                <span class="text-muted small">Overall Completion</span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Completed</span>
                                    <span class="fw-bold text-success"><?= $vaccination_stats['completed'] ?>/<?= $vaccination_stats['total'] ?></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($vaccination_stats['completed']/$vaccination_stats['total'])*100 ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Pending</span>
                                    <span class="fw-bold text-warning"><?= $vaccination_stats['pending'] ?>/<?= $vaccination_stats['total'] ?></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= ($vaccination_stats['pending']/$vaccination_stats['total'])*100 ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-0">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Missed</span>
                                    <span class="fw-bold text-danger"><?= $vaccination_stats['missed'] ?>/<?= $vaccination_stats['total'] ?></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= ($vaccination_stats['missed']/$vaccination_stats['total'])*100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 8️⃣ Vaccination Statistics Chart -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-bar me-2 text-info"></i>Statistics</h6>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center position-relative">
                            <div style="width: 100%; height: 220px;">
                                <canvas id="vaccineStatsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Vaccination History Column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-primary"></i>Vaccination History</h6>
                </div>
                <div class="card-body p-4">
                    <div class="timeline-wrapper">
                        <?php if(empty($history_records)): ?>
                            <div class="text-center py-4 text-muted">No vaccination history found.</div>
                        <?php else: ?>
                        <?php foreach($history_records as $record): ?>
                        <div class="timeline-item">
                            <!-- Marker Color Logic -->
                            <?php 
                                $status = ucfirst($record['status']); // Vaccinated, Missed, etc.
                                $markerClass = 'bg-secondary';
                                if($status == 'Vaccinated' || $status == 'Completed') $markerClass = 'bg-success';
                                elseif($status == 'Missed') $markerClass = 'bg-danger';
                                elseif($status == 'Pending') $markerClass = 'bg-warning';
                            ?>
                            <div class="timeline-marker <?= $markerClass ?>"></div>
                            
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">
                                        <?= htmlspecialchars($record['vaccine_name'] ?? 'Unknown Vaccine') ?> 
                                        <span class="badge bg-light text-dark border ms-2 fw-normal">Dose: <?= htmlspecialchars($record['dose_number'] ?? '1') ?></span>
                                    </h6>
                                    <p class="text-muted small mb-1"><i class="fas fa-hospital me-1"></i> <?= htmlspecialchars($record['hospital_name'] ?? 'N/A') ?></p>
                                </div>
                                <div class="text-end">
                                    <span class="d-block small fw-bold text-dark"><?= date('M d, Y', strtotime($record['scheduled_date'])) ?></span>
                                    <?php if($status == 'Vaccinated' || $status == 'Completed'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Completed</span>
                                    <?php elseif($status == 'Missed'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25">Missed</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Schedule Column -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt me-2 text-warning"></i>Upcoming Schedule</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Vaccine</th>
                                    <th>Dose</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($upcoming_records)): ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No upcoming vaccinations scheduled.</td></tr>
                                <?php else: ?>
                                <?php foreach($upcoming_records as $next): ?>
                                <tr>
                                    <td class="fw-semibold ps-3"><?= htmlspecialchars($next['vaccine_name'] ?? 'Unknown') ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($next['dose_number'] ?? '1') ?></span></td>
                                    <td class="text-primary fw-medium"><?= date('M d, Y', strtotime($next['scheduled_date'])) ?></td>
                                    <td><span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">Upcoming</span></td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-outline-primary" title="Book Appointment">
                                            <i class="fas fa-calendar-plus"></i>
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
    
    <!-- 9️⃣ Action Buttons Section -->
    <div class="d-flex gap-3 mt-4 justify-content-end">
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Print Profile
        </button>
    </div>
</div>

<!-- JavaScript for Charts and Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Chart.js Initialization
    const ctx = document.getElementById('vaccineStatsChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'Missed'],
            datasets: [{
                data: [<?= $vaccination_stats['completed'] ?>, <?= $vaccination_stats['pending'] ?>, <?= $vaccination_stats['missed'] ?>],
                backgroundColor: ['#50C878', '#FFC107', '#DC3545'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        usePointStyle: true, 
                        padding: 20,
                        font: { family: "'Inter', sans-serif", size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            cutout: '70%'
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>