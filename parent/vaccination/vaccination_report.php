<?php
// Include authentication and layout files
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Fetch Parent ID
$parent_user_id = $_SESSION['user_id'];

// 1. Fetch Children for Filter
$children = [];
$stmt_child = $conn->prepare("SELECT id, name FROM children WHERE parent_id = ?");
$stmt_child->bind_param("i", $parent_user_id);
$stmt_child->execute();
$res_child = $stmt_child->get_result();
while($row = $res_child->fetch_assoc()) {
    $children[] = $row;
}

// 2. Fetch Vaccination Records
$records = [];
$stats = ['total' => 0, 'completed' => 0, 'pending' => 0, 'missed' => 0];

$query = "SELECT 
            vs.id, 
            vs.scheduled_date, 
            vs.status, 
            vs.dose_number, 
            c.name as child_name, 
            v.vaccine_name, 
            h.hospital_name 
          FROM vaccination_schedule vs
          JOIN children c ON vs.child_id = c.id
          JOIN vaccines v ON vs.vaccine_id = v.id
          LEFT JOIN hospitals h ON vs.hospital_id = h.id
          WHERE c.parent_id = ?
          ORDER BY vs.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $parent_user_id);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    // Normalize status for display
    if ($row['status'] == 'vaccinated') $row['display_status'] = 'Completed';
    else $row['display_status'] = ucfirst($row['status']); // Pending, Missed
    
    $records[] = $row;
    
    // Stats Calculation
    $stats['total']++;
    if($row['display_status'] == 'Completed') $stats['completed']++;
    elseif($row['display_status'] == 'Pending') $stats['pending']++;
    elseif($row['display_status'] == 'Missed') $stats['missed']++;
}

// Calculate Completion Rate
$completion_rate = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0;
?>

<div class="container-fluid px-4">

    <!-- 1️⃣ Page Header & Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Vaccination Report</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item">Vaccination</li>
                            <li class="breadcrumb-item active" aria-current="page">Report</li>
                        </ol>
                    </nav>
                </div>
                
                <!-- Export Buttons -->
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary shadow-sm" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                    <button id="exportCsvBtn" class="btn btn-outline-primary shadow-sm">
                        <i class="fas fa-file-download me-2"></i>Export CSV
                    </button>
                    <button id="exportPdfBtn" class="btn btn-primary shadow-sm">
                        <i class="fas fa-file-pdf me-2"></i>Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Select Child</label>
                    <select class="form-select" id="filterChild">
                        <option value="all">All Children</option>
                        <?php foreach($children as $child): ?>
                            <option value="<?= htmlspecialchars($child['name']) ?>"><?= htmlspecialchars($child['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">From Date</label>
                    <input type="date" class="form-control" id="filterStartDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">To Date</label>
                    <input type="date" class="form-control" id="filterEndDate">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary w-100" id="btnApplyFilters">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2️⃣ Summary Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Vaccines -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Total Vaccines</p>
                            <h3 class="mb-0 fw-bold text-dark" id="statTotal"><?= $stats['total'] ?></h3>
                        </div>
                        <div class="stats-icon bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-syringe text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>Across all children
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Completed</p>
                            <h3 class="mb-0 fw-bold text-success" id="statCompleted"><?= $stats['completed'] ?></h3>
                        </div>
                        <div class="stats-icon bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $completion_rate ?>%"></div>
                        </div>
                        <small class="text-success mt-1 d-block"><?= $completion_rate ?>% Completion Rate</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Pending</p>
                            <h3 class="mb-0 fw-bold text-warning" id="statPending"><?= $stats['pending'] ?></h3>
                        </div>
                        <div class="stats-icon bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-warning">
                            <i class="fas fa-calendar-alt me-1"></i>Upcoming scheduled
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Missed -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Missed</p>
                            <h3 class="mb-0 fw-bold text-danger" id="statMissed"><?= $stats['missed'] ?></h3>
                        </div>
                        <div class="stats-icon bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-danger fw-semibold">
                            Action Required
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- 3️⃣ Vaccination Status Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Monthly Vaccination Trends
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="vaccinationTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- 5️⃣ Vaccination Timeline (Recent Activity) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-history me-2 text-info"></i>Recent Activity
                    </h6>
                </div>
                <div class="card-body p-4" style="max-height: 400px; overflow-y: auto;">
                    <div class="timeline-wrapper" id="timelineContainer">
                        <?php 
                        // Show top 5 recent activities
                        $recent_activity = array_slice($records, 0, 5);
                        foreach($recent_activity as $act): 
                            $status_class = '';
                            $icon = '';
                            $badge_class = '';
                            
                            if($act['display_status'] == 'Completed') {
                                $status_class = 'completed';
                                $icon = 'fa-check';
                                $badge_class = 'bg-success-subtle text-success border border-success border-opacity-25';
                            } elseif($act['display_status'] == 'Pending') {
                                $status_class = 'pending';
                                $icon = 'fa-clock';
                                $badge_class = 'bg-warning-subtle text-warning border border-warning border-opacity-25';
                            } else {
                                $status_class = 'missed';
                                $icon = 'fa-times';
                                $badge_class = 'bg-danger-subtle text-danger border border-danger border-opacity-25';
                            }
                        ?>
                        <div class="timeline-item" data-child="<?= htmlspecialchars($act['child_name']) ?>" data-date="<?= date('Y-m-d', strtotime($act['scheduled_date'])) ?>" data-status="<?= $act['display_status'] ?>">
                            <div class="timeline-icon <?= $status_class ?>">
                                <i class="fas <?= $icon ?> small"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($act['vaccine_name']) ?> - Dose <?= $act['dose_number'] ?></h6>
                            <p class="text-muted small mb-1">
                                <i class="fas fa-child me-1"></i> <?= htmlspecialchars($act['child_name']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge <?= $badge_class ?>"><?= $act['display_status'] ?></span>
                                <small class="text-muted"><?= date('M d, Y', strtotime($act['scheduled_date'])) ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($recent_activity)): ?>
                            <div class="text-center text-muted py-3">No recent activity found.</div>
                        <?php endif; ?>
                    </div>
                    <div class="text-center mt-2">
                        <!-- <a href="#" class="btn btn-link text-decoration-none small fw-bold">View Full Timeline</a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4️⃣ Date-wise Vaccination Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-table me-2 text-secondary"></i>Detailed Vaccination Records
            </h6>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control bg-light border-start-0" id="searchTable" placeholder="Search records...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="vaccinationTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Date</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Child Name</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Vaccine</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Dose</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Hospital</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase">Status</th>
                            <th class="px-4 py-3 text-secondary small text-uppercase text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach($records as $rec): 
                            // Determine styles based on status
                            $badge_class = '';
                            $avatar_bg = 'bg-soft-primary';
                            
                            if($rec['display_status'] == 'Completed') {
                                $badge_class = 'bg-success-subtle text-success border border-success border-opacity-25';
                                $avatar_bg = 'bg-soft-success';
                            } elseif($rec['display_status'] == 'Pending') {
                                $badge_class = 'bg-warning-subtle text-warning border border-warning border-opacity-25';
                                $avatar_bg = 'bg-soft-warning';
                            } else {
                                $badge_class = 'bg-danger-subtle text-danger border border-danger border-opacity-25';
                                $avatar_bg = 'bg-soft-danger';
                            }

                            // Initials
                            $initials = strtoupper(substr($rec['child_name'], 0, 2));
                            
                            // Ordinal Dose
                            $dose_display = $rec['dose_number'];
                            $ends = array('th','st','nd','rd','th','th','th','th','th','th');
                            if ((($dose_display % 100) >= 11) && (($dose_display%100) <= 13)) $dose_suffix = 'th';
                            else $dose_suffix = $ends[$dose_display % 10];
                        ?>
                        <tr data-child="<?= htmlspecialchars($rec['child_name']) ?>" data-date="<?= date('Y-m-d', strtotime($rec['scheduled_date'])) ?>" data-status="<?= $rec['display_status'] ?>">
                            <td class="px-4 py-3 fw-medium"><?= date('M d, Y', strtotime($rec['scheduled_date'])) ?></td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle <?= $avatar_bg ?> me-2 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:12px;"><?= $initials ?></div>
                                    <span><?= htmlspecialchars($rec['child_name']) ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3"><?= htmlspecialchars($rec['vaccine_name']) ?></td>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark border"><?= $dose_display . $dose_suffix ?></span></td>
                            <td class="px-4 py-3 text-muted small"><?= htmlspecialchars($rec['hospital_name'] ?? 'Not Assigned') ?></td>
                            <td class="px-4 py-3">
                                <span class="badge <?= $badge_class ?>"><?= $rec['display_status'] ?></span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewReportModal"
                                    data-child="<?= htmlspecialchars($rec['child_name']) ?>"
                                    data-vaccine="<?= htmlspecialchars($rec['vaccine_name']) ?>"
                                    data-dose="<?= $dose_display . $dose_suffix ?>"
                                    data-date="<?= date('M d, Y', strtotime($rec['scheduled_date'])) ?>"
                                    data-hospital="<?= htmlspecialchars($rec['hospital_name'] ?? 'Not Assigned') ?>"
                                    data-status="<?= $rec['display_status'] ?>"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($records)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">No vaccination records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

</div>

<!-- Report Details Modal -->
<div class="modal fade" id="viewReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Vaccination Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="text-center mb-4">
                    <div class="avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="fas fa-file-medical-alt fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-1" id="modalVaccine"></h5>
                    <span class="badge bg-light text-dark border" id="modalDose"></span>
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
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Hospital</label>
                        <div class="fw-medium text-dark" id="modalHospital"></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Status</label>
                        <div id="modalStatus"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="window.print()"><i class="fas fa-print me-2"></i>Print</button>
            </div>
        </div>
    </div>
</div>

<!-- PDF Generation Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<!-- Chart.js Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Filter Logic ---
    const filterChild = document.getElementById('filterChild');
    const filterStartDate = document.getElementById('filterStartDate');
    const filterEndDate = document.getElementById('filterEndDate');
    const btnApplyFilters = document.getElementById('btnApplyFilters');
    const searchTable = document.getElementById('searchTable');

    let vaccinationTrendChart; // To hold the chart instance
    
    const tableRows = document.querySelectorAll('#tableBody tr');
    const timelineItems = document.querySelectorAll('#timelineContainer .timeline-item');
    
    // Stats Elements
    const statTotal = document.getElementById('statTotal');
    const statCompleted = document.getElementById('statCompleted');
    const statPending = document.getElementById('statPending');
    const statMissed = document.getElementById('statMissed');

    function updateStats() {
        let total = 0;
        let completed = 0;
        let pending = 0;
        let missed = 0;

        tableRows.forEach(row => {
            if (row.style.display !== 'none') {
                total++;
                const status = row.getAttribute('data-status');
                if (status === 'Completed') completed++;
                else if (status === 'Pending') pending++;
                else if (status === 'Missed') missed++;
            }
        });

        statTotal.textContent = total;
        statCompleted.textContent = completed;
        statPending.textContent = pending;
        statMissed.textContent = missed;
    }

    function updateChart(visibleRecords) {
        if (!vaccinationTrendChart) return;

        const monthLabels = vaccinationTrendChart.data.labels;
        const newChartData = {
            completed: Array(monthLabels.length).fill(0),
            pending: Array(monthLabels.length).fill(0),
            missed: Array(monthLabels.length).fill(0)
        };

        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        visibleRecords.forEach(record => {
            // Add T12:00:00 to avoid timezone issues where new Date() might interpret it as the previous day
            const recordDate = new Date(record.date + 'T12:00:00');
            const recordMonth = monthNames[recordDate.getMonth()];
            const monthIndex = monthLabels.indexOf(recordMonth);

            if (monthIndex !== -1) {
                if (record.status === 'Completed') {
                    newChartData.completed[monthIndex]++;
                } else if (record.status === 'Pending') {
                    newChartData.pending[monthIndex]++;
                } else if (record.status === 'Missed') {
                    newChartData.missed[monthIndex]++;
                }
            }
        });

        vaccinationTrendChart.data.datasets[0].data = newChartData.completed;
        vaccinationTrendChart.data.datasets[1].data = newChartData.pending;
        vaccinationTrendChart.data.datasets[2].data = newChartData.missed;
        vaccinationTrendChart.update();
    }

    function applyFilters() {
        const selectedChild = filterChild.value;
        const startDate = filterStartDate.value ? new Date(filterStartDate.value) : null;
        const endDate = filterEndDate.value ? new Date(filterEndDate.value) : null;
        const searchTerm = searchTable.value.toLowerCase();

        // Filter Table
        let visibleRecords = [];

        tableRows.forEach(row => {
            const childName = row.getAttribute('data-child');
            const rowDateStr = row.getAttribute('data-date');
            const rowDate = new Date(rowDateStr);
            const rowText = row.innerText.toLowerCase();

            let isVisible = true;

            // Child Filter
            if (selectedChild !== 'all' && childName !== selectedChild) {
                isVisible = false;
            }

            // Date Filter
            if (isVisible && startDate && rowDate < startDate) isVisible = false;
            if (isVisible && endDate && rowDate > endDate) isVisible = false;

            // Search Filter
            if (isVisible && searchTerm && !rowText.includes(searchTerm)) isVisible = false;

            row.style.display = isVisible ? '' : 'none';

            if (isVisible) {
                visibleRecords.push({ date: rowDateStr, status: row.getAttribute('data-status') });
            }
        });

        // Filter Timeline (Similar logic, ignoring search term for simplicity)
        timelineItems.forEach(item => {
            const childName = item.getAttribute('data-child');
            const itemDateStr = item.getAttribute('data-date');
            const itemDate = new Date(itemDateStr);

            let isVisible = true;

            if (selectedChild !== 'all' && childName !== selectedChild) isVisible = false;
            if (isVisible && startDate && itemDate < startDate) isVisible = false;
            if (isVisible && endDate && itemDate > endDate) isVisible = false;

            item.style.display = isVisible ? '' : 'none';
        });

        updateStats();
        updateChart(visibleRecords);
    }

    // Event Listeners
    btnApplyFilters.addEventListener('click', applyFilters);
    
    searchTable.addEventListener('keyup', function() {
        applyFilters();
    });

    // Initial Stats Update
    updateStats();

    // --- Export Logic ---
    const exportCsvBtn = document.getElementById('exportCsvBtn');
    const exportPdfBtn = document.getElementById('exportPdfBtn');

    // CSV Export
    exportCsvBtn.addEventListener('click', function() {
        const childName = filterChild.value;
        const startDate = filterStartDate.value;
        const endDate = filterEndDate.value;

        const queryParams = new URLSearchParams({
            child_name: childName,
            start_date: startDate,
            end_date: endDate
        });

        window.location.href = `export_csv.php?${queryParams.toString()}`;
    });

    // PDF Export (Client-side)
    exportPdfBtn.addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const table = document.getElementById('vaccinationTable');
        const visibleRows = Array.from(table.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');

        const head = [['Date', 'Child Name', 'Vaccine', 'Dose', 'Hospital', 'Status']];
        const body = visibleRows.map(row => {
            const cells = row.querySelectorAll('td');
            // Extract text from cells, skipping the last one (Action button)
            return Array.from(cells).slice(0, -1).map(cell => cell.innerText.trim());
        });

        doc.autoTable({
            head: head,
            body: body,
            startY: 20,
            didDrawPage: function (data) {
                doc.setFontSize(20);
                doc.setTextColor(40);
                doc.text("Vaccination Report", data.settings.margin.left, 15);
            }
        });

        doc.save('vaccination-report.pdf');
    });

    <?php
    // Prepare Chart Data (Last 6 Months)
    $months = [];
    for ($i = 5; $i >= 0; $i--) {
        $months[] = date('M', strtotime("-$i months"));
    }

    $chart_data = [
        'completed' => array_fill(0, 6, 0),
        'pending' => array_fill(0, 6, 0),
        'missed' => array_fill(0, 6, 0)
    ];

    foreach ($records as $rec) {
        $rec_month = date('M', strtotime($rec['scheduled_date']));
        $key = array_search($rec_month, $months);
        if ($key !== false) {
            if ($rec['display_status'] == 'Completed') $chart_data['completed'][$key]++;
            elseif ($rec['display_status'] == 'Pending') $chart_data['pending'][$key]++;
            elseif ($rec['display_status'] == 'Missed') $chart_data['missed'][$key]++;
        }
    }
    ?>

    // --- Modal Logic ---
    const reportModal = document.getElementById('viewReportModal');
    if (reportModal) {
        reportModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const child = button.getAttribute('data-child');
            const vaccine = button.getAttribute('data-vaccine');
            const dose = button.getAttribute('data-dose');
            const date = button.getAttribute('data-date');
            const hospital = button.getAttribute('data-hospital');
            const status = button.getAttribute('data-status');

            reportModal.querySelector('#modalChild').textContent = child;
            reportModal.querySelector('#modalVaccine').textContent = vaccine;
            reportModal.querySelector('#modalDose').textContent = dose;
            reportModal.querySelector('#modalDate').textContent = date;
            reportModal.querySelector('#modalHospital').textContent = hospital;
            
            const statusContainer = reportModal.querySelector('#modalStatus');
            if(status === 'Completed') {
                statusContainer.innerHTML = '<span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill">Completed</span>';
            } else if(status === 'Pending') {
                statusContainer.innerHTML = '<span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill">Pending</span>';
            } else {
                statusContainer.innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill">Missed</span>';
            }
        });
    }

    // --- Chart Logic ---
    // Vaccination Trend Chart
    const ctx = document.getElementById('vaccinationTrendChart').getContext('2d');
    
    // Gradient for Completed
    const gradientCompleted = ctx.createLinearGradient(0, 0, 0, 400);
    gradientCompleted.addColorStop(0, 'rgba(25, 135, 84, 0.5)');
    gradientCompleted.addColorStop(1, 'rgba(25, 135, 84, 0.0)');

    // Gradient for Pending
    const gradientPending = ctx.createLinearGradient(0, 0, 0, 400);
    gradientPending.addColorStop(0, 'rgba(255, 193, 7, 0.5)');
    gradientPending.addColorStop(1, 'rgba(255, 193, 7, 0.0)');

    vaccinationTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [
                {
                    label: 'Completed',
                    data: <?= json_encode($chart_data['completed']) ?>,
                    borderColor: '#198754',
                    backgroundColor: gradientCompleted,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#198754',
                    pointRadius: 4
                },
                {
                    label: 'Pending',
                    data: <?= json_encode($chart_data['pending']) ?>,
                    borderColor: '#ffc107',
                    backgroundColor: gradientPending,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#ffc107',
                    pointRadius: 4
                },
                {
                    label: 'Missed',
                    data: <?= json_encode($chart_data['missed']) ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#dc3545',
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { family: "'Inter', sans-serif", size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f0f0f0'
                    },
                    ticks: {
                        stepSize: 5
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
