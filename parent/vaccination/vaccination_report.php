<?php
/**
 * Vaccination Report Page
 * Parent Panel
 * 
 * Displays comprehensive vaccination reports, statistics, and history
 * with filtering and export options.
 */

// Include authentication and layout files
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
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
                    <button class="btn btn-outline-primary shadow-sm">
                        <i class="fas fa-file-download me-2"></i>Export CSV
                    </button>
                    <button class="btn btn-primary shadow-sm">
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
                        <option value="Sarah Ahmed">Sarah Ahmed</option>
                        <option value="Ahmed Ali">Ahmed Ali</option>
                        <option value="Fatima Hassan">Fatima Hassan</option>
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
                            <h3 class="mb-0 fw-bold text-dark" id="statTotal">45</h3>
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
                            <h3 class="mb-0 fw-bold text-success" id="statCompleted">32</h3>
                        </div>
                        <div class="stats-icon bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 71%"></div>
                        </div>
                        <small class="text-success mt-1 d-block">71% Completion Rate</small>
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
                            <h3 class="mb-0 fw-bold text-warning" id="statPending">10</h3>
                        </div>
                        <div class="stats-icon bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-warning">
                            <i class="fas fa-calendar-alt me-1"></i>5 upcoming this month
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
                            <h3 class="mb-0 fw-bold text-danger" id="statMissed">3</h3>
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
                        <!-- Item 1 -->
                        <div class="timeline-item" data-child="Sarah Ahmed" data-date="2026-02-10" data-status="Completed">
                            <div class="timeline-icon completed">
                                <i class="fas fa-check small"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Polio (OPV) - Dose 3</h6>
                            <p class="text-muted small mb-1">
                                <i class="fas fa-child me-1"></i> Sarah Ahmed
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Completed</span>
                                <small class="text-muted">Feb 10, 2026</small>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="timeline-item" data-child="Ahmed Ali" data-date="2026-02-15" data-status="Pending">
                            <div class="timeline-icon pending">
                                <i class="fas fa-clock small"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Measles - Dose 1</h6>
                            <p class="text-muted small mb-1">
                                <i class="fas fa-child me-1"></i> Ahmed Ali
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">Due Soon</span>
                                <small class="text-muted">Feb 15, 2026</small>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div class="timeline-item" data-child="Fatima Hassan" data-date="2026-01-28" data-status="Missed">
                            <div class="timeline-icon missed">
                                <i class="fas fa-times small"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Hepatitis B - Dose 2</h6>
                            <p class="text-muted small mb-1">
                                <i class="fas fa-child me-1"></i> Fatima Hassan
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25">Missed</span>
                                <small class="text-muted">Jan 28, 2026</small>
                            </div>
                        </div>

                        <!-- Item 4 -->
                        <div class="timeline-item" data-child="Sarah Ahmed" data-date="2026-01-15" data-status="Completed">
                            <div class="timeline-icon completed">
                                <i class="fas fa-check small"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">BCG Vaccine</h6>
                            <p class="text-muted small mb-1">
                                <i class="fas fa-child me-1"></i> Sarah Ahmed
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Completed</span>
                                <small class="text-muted">Jan 15, 2026</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <a href="#" class="btn btn-link text-decoration-none small fw-bold">View Full Timeline</a>
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
                        <!-- Row 1 -->
                        <tr data-child="Sarah Ahmed" data-date="2026-02-10" data-status="Completed">
                            <td class="px-4 py-3 fw-medium">Feb 10, 2026</td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-soft-primary me-2 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:12px;">SA</div>
                                    <span>Sarah Ahmed</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">Polio (OPV)</td>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark border">3rd</span></td>
                            <td class="px-4 py-3 text-muted small">City General Hospital</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Completed</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                        
                        <!-- Row 2 -->
                        <tr data-child="Ahmed Ali" data-date="2026-02-15" data-status="Pending">
                            <td class="px-4 py-3 fw-medium">Feb 15, 2026</td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-soft-success me-2 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:12px;">AA</div>
                                    <span>Ahmed Ali</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">Measles</td>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark border">1st</span></td>
                            <td class="px-4 py-3 text-muted small">Metro Health Center</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">Pending</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>

                        <!-- Row 3 -->
                        <tr data-child="Fatima Hassan" data-date="2026-01-28" data-status="Missed">
                            <td class="px-4 py-3 fw-medium">Jan 28, 2026</td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-soft-danger me-2 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:12px;">FH</div>
                                    <span>Fatima Hassan</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">Hepatitis B</td>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark border">2nd</span></td>
                            <td class="px-4 py-3 text-muted small">City General Hospital</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25">Missed</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>

                        <!-- Row 4 -->
                        <tr data-child="Sarah Ahmed" data-date="2026-01-15" data-status="Completed">
                            <td class="px-4 py-3 fw-medium">Jan 15, 2026</td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-soft-primary me-2 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:12px;">SA</div>
                                    <span>Sarah Ahmed</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">BCG</td>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark border">1st</span></td>
                            <td class="px-4 py-3 text-muted small">City General Hospital</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Completed</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>

                        <!-- Row 5 -->
                        <tr data-child="Ahmed Ali" data-date="2026-01-05" data-status="Completed">
                            <td class="px-4 py-3 fw-medium">Jan 05, 2026</td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-soft-success me-2 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:12px;">AA</div>
                                    <span>Ahmed Ali</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">Pentavalent</td>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark border">1st</span></td>
                            <td class="px-4 py-3 text-muted small">Metro Health Center</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Completed</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
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

<!-- Chart.js Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Filter Logic ---
    const filterChild = document.getElementById('filterChild');
    const filterStartDate = document.getElementById('filterStartDate');
    const filterEndDate = document.getElementById('filterEndDate');
    const btnApplyFilters = document.getElementById('btnApplyFilters');
    const searchTable = document.getElementById('searchTable');
    
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

    function applyFilters() {
        const selectedChild = filterChild.value;
        const startDate = filterStartDate.value ? new Date(filterStartDate.value) : null;
        const endDate = filterEndDate.value ? new Date(filterEndDate.value) : null;
        const searchTerm = searchTable.value.toLowerCase();

        // Filter Table
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
    }

    // Event Listeners
    btnApplyFilters.addEventListener('click', applyFilters);
    
    searchTable.addEventListener('keyup', function() {
        applyFilters();
    });

    // Initial Stats Update
    updateStats();

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

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb'],
            datasets: [
                {
                    label: 'Completed',
                    data: [5, 8, 12, 15, 20, 32],
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
                    data: [2, 3, 4, 6, 8, 10],
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
                    data: [0, 1, 1, 2, 2, 3],
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
