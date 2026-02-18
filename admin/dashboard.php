<?php

// Include DB
include '../config/db.php';

// Reusable Includes
include 'includes/auth_check.php';

// --- AJAX Handler for Chart Data ---
if (isset($_GET['action']) && $_GET['action'] === 'get_chart_data') {
    $range = isset($_GET['range']) ? intval($_GET['range']) : 6;
    if (!in_array($range, [3, 6, 12])) $range = 6;

    $months = [];
    $trend_counts = [];

    for ($i = $range - 1; $i >= 0; $i--) {
        $m_start = date('Y-m-01', strtotime("-$i months"));
        $m_end = date('Y-m-t', strtotime("-$i months"));
        $months[] = date('M', strtotime("-$i months"));
        
        $sql = "SELECT COUNT(*) as count FROM vaccination_schedule WHERE status = 'vaccinated' AND scheduled_date BETWEEN '$m_start' AND '$m_end'";
        $res = $conn->query($sql);
        $trend_counts[] = ($res && $row = $res->fetch_assoc()) ? intval($row['count']) : 0;
    }

    header('Content-Type: application/json');
    echo json_encode(['labels' => $months, 'data' => $trend_counts]);
    exit();
}

include 'includes/header.php';
include 'includes/sidebar.php';

// --- 1. Statistics ---

// Total Children
$total_children = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM children");
if($res && $row = $res->fetch_assoc()) $total_children = $row['count'];

// Total Vaccinations (Completed)
$total_vaccinations = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM vaccination_schedule WHERE status = 'vaccinated'");
if($res && $row = $res->fetch_assoc()) $total_vaccinations = $row['count'];

// Upcoming Appointments (Pending & Future)
$upcoming_appointments = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM vaccination_schedule WHERE status = 'pending' AND scheduled_date >= CURDATE()");
if($res && $row = $res->fetch_assoc()) $upcoming_appointments = $row['count'];

// Registered Hospitals
$total_hospitals = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM hospitals");
if($res && $row = $res->fetch_assoc()) $total_hospitals = $row['count'];

// --- 2. Charts Data ---

// Vaccination Trends (Last 6 Months)
$months = [];
$trend_counts = [];
for ($i = 5; $i >= 0; $i--) {
    $m_start = date('Y-m-01', strtotime("-$i months"));
    $m_end = date('Y-m-t', strtotime("-$i months"));
    $months[] = date('M', strtotime("-$i months"));
    
    $sql = "SELECT COUNT(*) as count FROM vaccination_schedule WHERE status = 'vaccinated' AND scheduled_date BETWEEN '$m_start' AND '$m_end'";
    $res = $conn->query($sql);
    $trend_counts[] = ($res && $row = $res->fetch_assoc()) ? $row['count'] : 0;
}

// Vaccination Status
$status_counts = ['vaccinated' => 0, 'pending' => 0, 'missed' => 0];
$sql = "SELECT status, COUNT(*) as count FROM vaccination_schedule GROUP BY status";
$res = $conn->query($sql);
if($res) {
    while($row = $res->fetch_assoc()) {
        $s = strtolower($row['status']);
        if(isset($status_counts[$s])) {
            $status_counts[$s] = $row['count'];
        }
    }
}
$total_status_count = array_sum($status_counts);
$status_percentages = [
    'vaccinated' => $total_status_count > 0 ? round(($status_counts['vaccinated'] / $total_status_count) * 100) : 0,
    'pending' => $total_status_count > 0 ? round(($status_counts['pending'] / $total_status_count) * 100) : 0,
    'missed' => $total_status_count > 0 ? round(($status_counts['missed'] / $total_status_count) * 100) : 0
];

// --- 3. Recent Activity ---
$recent_activity = [];
$sql = "SELECT vs.id, c.name as child_name, c.id as child_id, v.vaccine_name, vs.scheduled_date, h.hospital_name, vs.status 
        FROM vaccination_schedule vs
        JOIN children c ON vs.child_id = c.id
        JOIN vaccines v ON vs.vaccine_id = v.id
        LEFT JOIN hospitals h ON vs.hospital_id = h.id
        ORDER BY vs.created_at DESC LIMIT 5";
$res = $conn->query($sql);
if($res) {
    while($row = $res->fetch_assoc()) {
        $recent_activity[] = $row;
    }
}
?>

    <!-- ============================================
         MAIN CONTENT AREA
         ============================================ -->
    <main class="main-content">
        <div class="container-fluid px-4 py-4">
            
            <!-- ============================================
                 PAGE HEADER
                 ============================================ -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1 fw-bold">Admin Dashboard</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Overview</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="export/export_dashboard.php" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>
                        Export Report
                    </a>
                </div>
            </div>

            <!-- ============================================
                 SUMMARY CARDS SECTION
                 ============================================ -->
            <div class="row g-4 mb-4">
                
                <!-- Total Children Card -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Total Children</h6>
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fas fa-child fs-4"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-2"><?= number_format($total_children) ?></h2>
                            <div class="d-flex align-items-center text-success small fw-medium">
                                <i class="fas fa-check-circle me-1"></i>
                                <span>Registered Children</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Vaccinations Card -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Total Vaccinations</h6>
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fas fa-syringe fs-4"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-2"><?= number_format($total_vaccinations) ?></h2>
                            <div class="d-flex align-items-center text-success small fw-medium">
                                <i class="fas fa-check-circle me-1"></i>
                                <span>Completed Doses</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments Card -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Upcoming Appointments</h6>
                                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fas fa-calendar-check fs-4"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-2"><?= number_format($upcoming_appointments) ?></h2>
                            <div class="d-flex align-items-center text-danger small fw-medium">
                                <i class="fas fa-clock me-1"></i>
                                <span>Pending & Future</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registered Hospitals Card -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Registered Hospitals</h6>
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fas fa-hospital fs-4"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-2"><?= number_format($total_hospitals) ?></h2>
                            <div class="d-flex align-items-center text-info small fw-medium">
                                <i class="fas fa-check-circle me-1"></i>
                                <span>Active Partners</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ============================================
                 CHARTS SECTION
                 ============================================ -->
            <div class="row g-4 mb-4">
                
                <!-- Vaccination Trends Line Chart -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center rounded-top-4">
                            <h5 class="mb-0 fw-bold">Vaccination Trends</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="trendRangeBtn" data-bs-toggle="dropdown">
                                    Last 6 Months
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item trend-range-item" href="#" data-range="3">Last 3 Months</a></li>
                                    <li><a class="dropdown-item trend-range-item" href="#" data-range="6">Last 6 Months</a></li>
                                    <li><a class="dropdown-item trend-range-item" href="#" data-range="12">Last Year</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div style="height: 300px; position: relative;">
                                <canvas id="vaccinationTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vaccination Status Pie Chart -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 rounded-top-4">
                            <h5 class="mb-0 fw-bold">Vaccination Status</h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div style="height: 300px; position: relative;">
                                <canvas id="vaccinationStatusChart"></canvas>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success me-2" style="width: 12px; height: 12px;"></div>
                                        <span class="small">Completed</span>
                                    </div>
                                    <span class="fw-semibold"><?= $status_percentages['vaccinated'] ?>%</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-warning me-2" style="width: 12px; height: 12px;"></div>
                                        <span class="small">Pending</span>
                                    </div>
                                    <span class="fw-semibold"><?= $status_percentages['pending'] ?>%</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-danger me-2" style="width: 12px; height: 12px;"></div>
                                        <span class="small">Missed</span>
                                    </div>
                                    <span class="fw-semibold"><?= $status_percentages['missed'] ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ============================================
                 RECENT ACTIVITY TABLE
                 ============================================ -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center rounded-top-4">
                            <h5 class="mb-0 fw-bold">Recent Vaccination Activity</h5>
                            <a href="bookings/booking_details.php" class="btn btn-sm btn-outline-primary">
                                View All
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4 py-3 text-muted text-uppercase small fw-bold border-0">Child Name</th>
                                            <th class="px-4 py-3 text-muted text-uppercase small fw-bold border-0">Vaccine</th>
                                            <th class="px-4 py-3 text-muted text-uppercase small fw-bold border-0">Date</th>
                                            <th class="px-4 py-3 text-muted text-uppercase small fw-bold border-0">Hospital</th>
                                            <th class="px-4 py-3 text-muted text-uppercase small fw-bold border-0">Status</th>
                                            <th class="px-4 py-3 text-muted text-uppercase small fw-bold border-0">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($recent_activity)): ?>
                                            <tr><td colspan="6" class="text-center py-4 text-muted">No recent activity found.</td></tr>
                                        <?php else: ?>
                                        <?php foreach($recent_activity as $row): 
                                            // Status Badge Logic
                                            $status_badge = 'bg-secondary bg-opacity-10 text-secondary';
                                            $status_text = ucfirst($row['status']);
                                            $status_icon = 'fa-circle';

                                            if(strtolower($row['status']) == 'vaccinated') {
                                                $status_badge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                                $status_text = 'Completed';
                                                $status_icon = 'fa-check-circle';
                                            } elseif(strtolower($row['status']) == 'pending') {
                                                $status_badge = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                                                $status_text = 'Pending';
                                                $status_icon = 'fa-clock';
                                            } elseif(strtolower($row['status']) == 'missed') {
                                                $status_badge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                                $status_text = 'Missed';
                                                $status_icon = 'fa-times-circle';
                                            }

                                            // Initials
                                            $initials = strtoupper(substr($row['child_name'], 0, 2));
                                            $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                            $color = $colors[array_rand($colors)];
                                        ?>
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-<?= $color ?> text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 0.875rem; font-weight: 600;">
                                                        <?= $initials ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold"><?= htmlspecialchars($row['child_name']) ?></div>
                                                        <div class="text-muted small">ID: CH-<?= $row['child_id'] ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="fw-semibold"><?= htmlspecialchars($row['vaccine_name']) ?></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div><?= date('M d, Y', strtotime($row['scheduled_date'])) ?></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="fw-semibold"><?= htmlspecialchars($row['hospital_name'] ?? 'Not Assigned') ?></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="badge <?= $status_badge ?> rounded-pill px-3 py-2">
                                                    <i class="fas <?= $status_icon ?> me-1"></i>
                                                    <?= $status_text ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <a href="children/child_profile.php?id=<?= $row['child_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </a>
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
    </main>



    <!-- ============================================
         CHART INITIALIZATION
         ============================================ -->
    <script>
        // Wait for DOM and Chart.js to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Vaccination Trends Line Chart
            const trendsCtx = document.getElementById('vaccinationTrendsChart');
            let trendsChart;

            if (trendsCtx) {
                trendsChart = new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($months) ?>,
                        datasets: [{
                            label: 'Vaccinations',
                            data: <?= json_encode($trend_counts) ?>,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Handle Trend Range Change
            document.querySelectorAll('.trend-range-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const range = this.getAttribute('data-range');
                    const text = this.textContent;

                    // Update Button Text
                    document.getElementById('trendRangeBtn').textContent = text;

                    // Fetch Data
                    fetch(`dashboard.php?action=get_chart_data&range=${range}`)
                        .then(response => response.json())
                        .then(data => {
                            if (trendsChart) {
                                trendsChart.data.labels = data.labels;
                                trendsChart.data.datasets[0].data = data.data;
                                trendsChart.update();
                            }
                        })
                        .catch(error => console.error('Error fetching chart data:', error));
                });
            });

            // Vaccination Status Doughnut Chart
            const statusCtx = document.getElementById('vaccinationStatusChart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'Pending', 'Missed'],
                        datasets: [{
                            data: [<?= $status_percentages['vaccinated'] ?>, <?= $status_percentages['pending'] ?>, <?= $status_percentages['missed'] ?>],
                            backgroundColor: [
                                'rgb(34, 197, 94)',
                                'rgb(245, 158, 11)',
                                'rgb(239, 68, 68)'
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed + '%';
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>
