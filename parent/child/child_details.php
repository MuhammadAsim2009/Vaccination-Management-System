<?php
/**
 * Child Detail / Profile Page
 * Shows full child profile details, health info, and vaccination history.
 * 
 * Path: parent/child/child_details.php
 */

// Include authentication and layout files
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Dummy Data for UI Simulation (Simulating Database Fetch)
$child = [
    'name' => 'Sarah Ahmed',
    'id' => 'CH-2026-001',
    'dob' => '2021-01-15',
    'age' => '5 Years',
    'gender' => 'Female',
    'blood_group' => 'A+',
    'status' => 'Active',
    'place_of_birth' => 'City General Hospital, Larkana',
    'parent_name' => 'John Doe',
    'parent_phone' => '+1 (555) 123-4567',
    'parent_email' => 'john.doe@example.com',
    'address' => '123 Health Ave, Medical District, NY',
    'birth_weight' => '3.2 kg',
    'allergies' => ['Peanuts', 'Dust'],
    'chronic_diseases' => [],
    'notes' => 'Child is generally healthy. Reacts mildly to flu shots.',
    'avatar_url' => 'https://ui-avatars.com/api/?name=Sarah+Ahmed&background=e3f2fd&color=1976d2&size=128&bold=true'
];

// Dummy Stats
$vaccination_stats = [
    'total' => 20,
    'completed' => 12,
    'pending' => 6,
    'missed' => 2,
    'percentage' => 60
];

// Dummy History Data
$history = [
    ['vaccine' => 'BCG', 'dose' => '1st Dose', 'date' => '2021-01-16', 'hospital' => 'City General Hospital', 'status' => 'Completed'],
    ['vaccine' => 'OPV-0', 'dose' => '1st Dose', 'date' => '2021-01-16', 'hospital' => 'City General Hospital', 'status' => 'Completed'],
    ['vaccine' => 'Pentavalent-1', 'dose' => '1st Dose', 'date' => '2021-02-25', 'hospital' => 'City General Hospital', 'status' => 'Completed'],
    ['vaccine' => 'Pneumococcal-1', 'dose' => '1st Dose', 'date' => '2021-02-25', 'hospital' => 'City General Hospital', 'status' => 'Completed'],
    ['vaccine' => 'Rotavirus-1', 'dose' => '1st Dose', 'date' => '2021-03-10', 'hospital' => 'Metro Clinic', 'status' => 'Missed'],
    ['vaccine' => 'Measles-1', 'dose' => '1st Dose', 'date' => '2021-10-15', 'hospital' => 'City General Hospital', 'status' => 'Completed'],
];

// Dummy Upcoming Data
$upcoming = [
    ['vaccine' => 'MMR-2', 'dose' => '2nd Dose', 'due_date' => '2026-03-15', 'hospital' => 'City General Hospital'],
    ['vaccine' => 'DPT Booster', 'dose' => 'Booster', 'due_date' => '2026-04-20', 'hospital' => 'City General Hospital'],
    ['vaccine' => 'Polio Booster', 'dose' => 'Booster', 'due_date' => '2026-05-10', 'hospital' => 'Metro Clinic'],
];
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
            <a href="update_child.php?id=1" class="btn btn-primary shadow-sm">
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
                        <img src="<?= $child['avatar_url'] ?>" alt="Child Avatar" class="rounded-circle border border-4 border-white shadow-sm" width="100" height="100">
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle" data-bs-toggle="tooltip" title="Active Status">
                            <span class="visually-hidden">Active</span>
                        </span>
                    </div>
                </div>
                <!-- Name & ID -->
                <div class="col-lg-5 text-center text-lg-start mb-3 mb-lg-0">
                    <h4 class="fw-bold text-dark mb-1"><?= $child['name'] ?></h4>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2 mb-2">
                        <span class="badge bg-light text-dark border"><i class="fas fa-id-card me-1 text-muted"></i> <?= $child['id'] ?></span>
                        <span class="badge bg-light text-dark border"><i class="fas fa-birthday-cake me-1 text-muted"></i> <?= $child['age'] ?></span>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-map-marker-alt me-1"></i> <?= $child['place_of_birth'] ?>
                    </p>
                </div>
                <!-- Quick Stats -->
                <div class="col-lg-auto ms-lg-auto">
                    <div class="d-flex gap-3 justify-content-center">
                        <div class="text-center px-3 border-end">
                            <div class="small text-muted fw-bold text-uppercase">Gender</div>
                            <div class="fw-bold text-dark"><i class="fas fa-venus text-danger me-1"></i><?= $child['gender'] ?></div>
                        </div>
                        <div class="text-center px-3 border-end">
                            <div class="small text-muted fw-bold text-uppercase">Blood</div>
                            <div class="fw-bold text-dark"><i class="fas fa-tint text-danger me-1"></i><?= $child['blood_group'] ?></div>
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

    <div class="row g-4">
        
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
                            <span class="fw-medium text-dark"><?= date('M d, Y', strtotime($child['dob'])) ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between border-bottom-0 py-1">
                            <span class="text-muted small">Parent Name</span>
                            <span class="fw-medium text-dark"><?= $child['parent_name'] ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between border-bottom-0 py-1">
                            <span class="text-muted small">Phone</span>
                            <span class="fw-medium text-dark"><?= $child['parent_phone'] ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between border-bottom-0 py-1">
                            <span class="text-muted small">Email</span>
                            <span class="fw-medium text-dark"><?= $child['parent_email'] ?></span>
                        </li>
                        <li class="list-group-item px-0 pt-2">
                            <span class="text-muted small d-block mb-1">Address</span>
                            <span class="fw-medium text-dark d-block small"><i class="fas fa-home me-1 text-muted"></i><?= $child['address'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 4️⃣ Medical Information Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-heartbeat me-2"></i>Medical Profile</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded-3 text-center">
                                <div class="small text-muted fw-bold text-uppercase mb-1">Birth Weight</div>
                                <div class="fw-bold text-dark"><i class="fas fa-weight text-secondary me-1"></i><?= $child['birth_weight'] ?></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded-3 text-center">
                                <div class="small text-muted fw-bold text-uppercase mb-1">Diseases</div>
                                <?php if(empty($child['chronic_diseases'])): ?>
                                    <div class="fw-bold text-success small">None</div>
                                <?php else: ?>
                                    <div class="fw-bold text-danger small">Yes</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="small text-muted fw-bold text-uppercase d-block mb-2">Allergies</label>
                        <div>
                            <?php foreach($child['allergies'] as $allergy): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 me-1 mb-1"><?= $allergy ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="small text-muted fw-bold text-uppercase d-block mb-2">Special Notes</label>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 small text-dark border border-warning border-opacity-25">
                            <i class="fas fa-sticky-note me-1 text-warning"></i> "<?= $child['notes'] ?>"
                        </div>
                    </div>
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

            <!-- Tabs: History & Schedule -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom px-4 pt-4 pb-0">
                    <ul class="nav nav-tabs card-header-tabs" id="vaccineTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                <i class="fas fa-history me-2"></i>Vaccination History
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
                                <i class="fas fa-calendar-alt me-2"></i>Upcoming Schedule
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="vaccineTabsContent">
                        
                        <!-- 6️⃣ Vaccination History Timeline -->
                        <div class="tab-pane fade show active" id="history" role="tabpanel">
                            <div class="timeline-wrapper">
                                <?php foreach($history as $record): ?>
                                <div class="timeline-item">
                                    <!-- Marker Color Logic -->
                                    <?php 
                                        $markerClass = 'bg-secondary';
                                        if($record['status'] == 'Completed') $markerClass = 'bg-success';
                                        elseif($record['status'] == 'Missed') $markerClass = 'bg-danger';
                                        elseif($record['status'] == 'Pending') $markerClass = 'bg-warning';
                                    ?>
                                    <div class="timeline-marker <?= $markerClass ?>"></div>
                                    
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">
                                                <?= $record['vaccine'] ?> 
                                                <span class="badge bg-light text-dark border ms-2 fw-normal"><?= $record['dose'] ?></span>
                                            </h6>
                                            <p class="text-muted small mb-1"><i class="fas fa-hospital me-1"></i> <?= $record['hospital'] ?></p>
                                        </div>
                                        <div class="text-end">
                                            <span class="d-block small fw-bold text-dark"><?= date('M d, Y', strtotime($record['date'])) ?></span>
                                            <?php if($record['status'] == 'Completed'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Completed</span>
                                            <?php elseif($record['status'] == 'Missed'): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25">Missed</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- 7️⃣ Upcoming Vaccination Schedule Table -->
                        <div class="tab-pane fade" id="upcoming" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Vaccine</th>
                                            <th>Dose</th>
                                            <th>Due Date</th>
                                            <th>Hospital</th>
                                            <th>Status</th>
                                            <th class="text-end pe-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($upcoming as $next): ?>
                                        <tr>
                                            <td class="fw-semibold ps-3"><?= $next['vaccine'] ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= $next['dose'] ?></span></td>
                                            <td class="text-primary fw-medium"><?= date('M d, Y', strtotime($next['due_date'])) ?></td>
                                            <td class="small text-muted"><?= $next['hospital'] ?></td>
                                            <td><span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">Upcoming</span></td>
                                            <td class="text-end pe-3">
                                                <button class="btn btn-sm btn-outline-primary" title="Book Appointment">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
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