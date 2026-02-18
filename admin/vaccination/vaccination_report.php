<?php
// Reusable Includes
include '../../config/db.php';
include '../includes/auth_check.php';

// --- AJAX Handler for Dynamic Data ---
if (isset($_GET['action']) && $_GET['action'] === 'fetch_report') {
    header('Content-Type: application/json');
    
    $start_date = $_GET['start_date'] ?? date('Y-m-d');
    $end_date = $_GET['end_date'] ?? date('Y-m-d');

    // 1. Summary Counts
    $summary = ['vaccinated' => 0, 'pending' => 0, 'missed' => 0];
    $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM vaccination_schedule WHERE DATE(scheduled_date) BETWEEN ? AND ? GROUP BY status");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()) {
        $status = strtolower($row['status']);
        if(isset($summary[$status])) {
            $summary[$status] = $row['count'];
        }
    }

    // 2. Chart Data (Group by Date)
    $chart_data = ['labels' => [], 'completed' => [], 'pending' => [], 'missed' => []];
    
    $stmt = $conn->prepare("SELECT DATE(scheduled_date) as date, status, COUNT(*) as count FROM vaccination_schedule WHERE DATE(scheduled_date) BETWEEN ? AND ? GROUP BY DATE(scheduled_date), status ORDER BY date ASC");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $grouped_data = [];
    while($row = $res->fetch_assoc()) {
        $grouped_data[$row['date']][strtolower($row['status'])] = $row['count'];
    }

    $current = new DateTime($start_date);
    $end = new DateTime($end_date);
    
    while ($current <= $end) {
        $d = $current->format('Y-m-d');
        $chart_data['labels'][] = $current->format('M d');
        $chart_data['completed'][] = $grouped_data[$d]['vaccinated'] ?? 0;
        $chart_data['pending'][] = $grouped_data[$d]['pending'] ?? 0;
        $chart_data['missed'][] = $grouped_data[$d]['missed'] ?? 0;
        $current->modify('+1 day');
    }

    // 3. Detailed Records
    $records = [];
    $stmt = $conn->prepare("
        SELECT vs.id, c.id as child_id, c.name as child_name, v.vaccine_name, h.hospital_name, vs.status, vs.dose_number
        FROM vaccination_schedule vs
        JOIN children c ON vs.child_id = c.id
        JOIN vaccines v ON vs.vaccine_id = v.id
        LEFT JOIN hospitals h ON vs.hospital_id = h.id
        WHERE DATE(vs.scheduled_date) BETWEEN ? AND ?
        ORDER BY vs.scheduled_date ASC
    ");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()) {
        $records[] = [
            'id' => $row['id'],
            'child_id' => $row['child_id'],
            'child' => $row['child_name'],
            'vaccine' => $row['vaccine_name'],
            'dose' => $row['dose_number'],
            'hospital' => $row['hospital_name'] ?? 'N/A',
            'status' => ucfirst($row['status'])
        ];
    }

    echo json_encode([
        'summary' => $summary,
        'chart' => $chart_data,
        'records' => $records
    ]);
    exit;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<!-- ============================================
     Main Content
     ============================================ -->
<main class="main-content">
    <div class="container-fluid px-4">
        
        <!-- Page Header & Breadcrumb -->
        <div class="d-flex align-items-center justify-content-between mb-4 mt-4">
            <div>
                <h3 class="fw-bold mb-1">Vaccination Reports</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="vaccination_report.php" class="text-decoration-none">Vaccination</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vaccination Reports</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="upcoming_dates.php" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    <span>View Upcoming Vaccinations</span>
                </a>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label for="startDate" class="form-label fw-semibold small text-muted text-uppercase">Start Date</label>
                        <input type="date" id="startDate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="endDate" class="form-label fw-semibold small text-muted text-uppercase">End Date</label>
                        <input type="date" id="endDate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-4 text-md-end pt-md-4">
                        <span class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i> Showing data for: <strong id="displayDateLabel" class="text-primary">Today</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <!-- Completed -->
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Completed</h6>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-check-double fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2" id="completedCount">0</h2>
                    </div>
                </div>
            </div>
            <!-- Pending -->
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Pending</h6>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-clock fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2" id="pendingCount">0</h2>
                    </div>
                </div>
            </div>
            <!-- Missed -->
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Missed</h6>
                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-times-circle fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2" id="missedCount">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 border-0 rounded-top-4 px-4 pt-4">
                        <h5 class="card-title mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Vaccination Status Distribution</h5>
                    </div>
                    <div class="card-body p-4">
                        <div style="height: 350px;">
                            <canvas id="vaccinationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0 rounded-top-4 px-4 pt-4">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Daily Records Table</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">#</th>
                                <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Child Detail</th>
                                <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Vaccine</th>
                                <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Location</th>
                                <th class="py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Status</th>
                                <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <!-- JS will inject rows here -->
                        </tbody>
                    </table>
                </div>
                <!-- Empty State -->
                <div id="emptyState" class="text-center py-5 d-none">
                    <i class="fas fa-folder-open text-muted fs-1 mb-3"></i>
                    <h6 class="text-muted fw-semibold">No records found for the selected date.</h6>
                    <p class="text-muted small">Try selecting another date to view the report.</p>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- JS Logic for Dynamic UI -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const todayStr = new Date().toISOString().split('T')[0];

    // --------------------------------------------------
    // 2. CHART.JS INITIALIZATION
    // --------------------------------------------------
    const ctx = document.getElementById('vaccinationChart').getContext('2d');
    let vaccinationChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], // Will be filled dynamically (Last 7 Days)
            datasets: [
                {
                    label: 'Completed',
                    data: [],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3
                },
                {
                    label: 'Pending',
                    data: [],
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3
                },
                {
                    label: 'Missed',
                    data: [],
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    display: true,
                    position: 'top',
                    labels: { usePointStyle: true, boxWidth: 6, font: { size: 11 } }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' },
                    ticks: { precision: 0, color: '#6c757d' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6c757d', font: { size: 10 } }
                }
            }
        }
    });

    // --------------------------------------------------
    // 3. CORE UPDATE FUNCTION
    // --------------------------------------------------
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const labelDate = document.getElementById('displayDateLabel');
    const tableBody = document.getElementById('reportTableBody');
    const emptyState = document.getElementById('emptyState');
    
    function updateDashboard(startStr, endStr) {
        // Update Label
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        const sDate = new Date(startStr).toLocaleDateString(undefined, options);
        const eDate = new Date(endStr).toLocaleDateString(undefined, options);
        labelDate.innerText = `${sDate} - ${eDate}`;

        // Fetch Data from PHP
        fetch(`vaccination_report.php?action=fetch_report&start_date=${startStr}&end_date=${endStr}`)
            .then(response => response.json())
            .then(data => {
                // Update Cards
                document.getElementById('completedCount').innerText = data.summary.vaccinated || 0;
                document.getElementById('pendingCount').innerText = data.summary.pending || 0;
                document.getElementById('missedCount').innerText = data.summary.missed || 0;

                // Update Chart
                vaccinationChart.data.labels = data.chart.labels;
                vaccinationChart.data.datasets[0].data = data.chart.completed;
                vaccinationChart.data.datasets[1].data = data.chart.pending;
                vaccinationChart.data.datasets[2].data = data.chart.missed;
                vaccinationChart.update();

                // Update Table
                tableBody.innerHTML = '';
                if (data.records.length > 0) {
                    emptyState.classList.add('d-none');
                    data.records.forEach((rec, index) => {
                        let statusBadge = '';
                        if(rec.status === 'Vaccinated' || rec.status === 'Completed') {
                            statusBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-medium">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </span>`;
                        } else if(rec.status === 'Pending') {
                            statusBadge = `<span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-2 fw-medium">
                                <i class="fas fa-clock me-1"></i> Pending
                            </span>`;
                        } else {
                            statusBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-medium">
                                <i class="fas fa-exclamation-circle me-1"></i> Missed
                            </span>`;
                        }

                        const row = `
                            <tr class="align-middle border-bottom border-light">
                                <td class="ps-4 text-muted small">${index + 1}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-weight: 600;">
                                            ${rec.child.charAt(0)}
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark mb-0">${rec.child}</span>
                                            <span class="text-muted x-small" style="font-size: 0.75rem;">Patient ID: #VMS-${1000 + rec.child_id}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium text-primary">${rec.vaccine}</span>
                                        <span class="text-muted x-small" style="font-size: 0.75rem;">Dose ${rec.dose}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="fas fa-hospital me-2"></i>
                                        <span>${rec.hospital}</span>
                                    </div>
                                </td>
                                <td>${statusBadge}</td>
                                <td class="text-end pe-4">
                                    <a href="../children/child_profile.php?id=${rec.child_id}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    emptyState.classList.remove('d-none');
                }
            })
            .catch(error => console.error('Error fetching report:', error));
    }

    // --------------------------------------------------
    // 4. EVENT LISTENERS
    // --------------------------------------------------
    startDateInput.addEventListener('change', function() {
        updateDashboard(startDateInput.value, endDateInput.value);
    });
    endDateInput.addEventListener('change', function() {
        updateDashboard(startDateInput.value, endDateInput.value);
    });

    // Initial Load
    updateDashboard(todayStr, todayStr);
});
</script>

<?php
include '../includes/footer.php';
?>