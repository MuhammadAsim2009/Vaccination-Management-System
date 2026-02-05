<?php
// Reusable Includes
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                        <li class="breadcrumb-item active" aria-current="page">Reports</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="upcoming_dates.php" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    <span>View Upcoming Vaccinations</span>
                </a>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form class="row g-3 align-items-end" id="filterForm">
                    <div class="col-md-4">
                        <label class="form-label fw-medium small">Filter by Date</label>
                        <input type="date" id="dateFilter" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium small">Vaccine Type</label>
                        <select class="form-select" id="vaccineFilter">
                            <option value="">All Vaccines</option>
                            <option value="BCG">BCG</option>
                            <option value="Polio">Polio (OPV)</option>
                            <option value="DTP">DTP-HepB-Hib</option>
                            <option value="Measles">Measles</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="fas fa-filter me-2"></i>Apply
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border w-100" onclick="resetFilters()">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <!-- Total -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-3 bg-primary-subtle p-2 me-3">
                                <i class="fas fa-syringe text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small uppercase fw-bold">Total Vaccinations</h6>
                                <h4 class="fw-bold mb-0">1,248</h4>
                            </div>
                        </div>
                        <div class="text-success small fw-medium">
                            <i class="fas fa-arrow-up me-1"></i>12% increase
                        </div>
                    </div>
                </div>
            </div>
            <!-- Completed -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-3 bg-success-subtle p-2 me-3">
                                <i class="fas fa-check-circle text-success fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small uppercase fw-bold">Completed</h6>
                                <h4 class="fw-bold mb-0">1,012</h4>
                            </div>
                        </div>
                        <div class="text-success small fw-medium">
                            <i class="fas fa-arrow-up me-1"></i>8% increase
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pending -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-3 bg-info-subtle p-2 me-3">
                                <i class="fas fa-clock text-info fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small uppercase fw-bold">Pending</h6>
                                <h4 class="fw-bold mb-0">186</h4>
                            </div>
                        </div>
                        <div class="text-muted small fw-medium">
                            Stable from last month
                        </div>
                    </div>
                </div>
            </div>
            <!-- Missed -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-3 bg-danger-subtle p-2 me-3">
                                <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small uppercase fw-bold">Missed</h6>
                                <h4 class="fw-bold mb-0">50</h4>
                            </div>
                        </div>
                        <div class="text-danger small fw-medium">
                            <i class="fas fa-arrow-down me-1"></i>5% decrease
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="card-title mb-0 fw-bold">Vaccination Trends (Last 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas id="vaccinationTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold">Detailed Report</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Date</th>
                                <th>Vaccine Name</th>
                                <th>Total Scheduled</th>
                                <th>Completed</th>
                                <th>Pending</th>
                                <th class="text-end px-4">Success Rate</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <!-- Dummy Data Rows -->
                            <?php
                            $dummyReports = [
                                ['date' => 'Feb 01, 2026', 'iso_date' => '2026-02-01', 'vaccine' => 'BCG', 'total' => 45, 'completed' => 42, 'pending' => 3],
                                ['date' => 'Feb 02, 2026', 'iso_date' => '2026-02-02', 'vaccine' => 'Polio (OPV)', 'total' => 38, 'completed' => 35, 'pending' => 3],
                                ['date' => 'Feb 03, 2026', 'iso_date' => '2026-02-03', 'vaccine' => 'DTP-HepB-Hib', 'total' => 52, 'completed' => 48, 'pending' => 4],
                                ['date' => 'Feb 04, 2026', 'iso_date' => '2026-02-04', 'vaccine' => 'Measles', 'total' => 29, 'completed' => 25, 'pending' => 4],
                            ];
                            foreach($dummyReports as $row):
                                $percent = round(($row['completed'] / $row['total']) * 100);
                            ?>
                            <tr class="report-row" data-vaccine="<?php echo $row['vaccine']; ?>" data-date="<?php echo $row['iso_date']; ?>">
                                <td class="px-4"><?php echo $row['date']; ?></td>
                                <td class="fw-semibold text-primary vaccine-name"><?php echo $row['vaccine']; ?></td>
                                <td><?php echo $row['total']; ?></td>
                                <td><span class="text-success fw-medium"><?php echo $row['completed']; ?></span></td>
                                <td><span class="text-warning fw-medium"><?php echo $row['pending']; ?></span></td>
                                <td class="text-end px-4">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <div class="progress w-100" style="height: 6px; max-width: 100px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percent; ?>%" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="small fw-bold"><?php echo $percent; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Chart Implementation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('vaccinationTrendChart').getContext('2d');
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan 29', 'Jan 30', 'Jan 31', 'Feb 01', 'Feb 02', 'Feb 03', 'Feb 04'],
            datasets: [{
                label: 'Vaccinations Completed',
                data: [65, 59, 80, 81, 56, 95, 105],
                borderColor: '#0d6efd',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#0d6efd',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
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
                    mode: 'index',
                    intersect: false,
                    padding: 12,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6c757d',
                        font: { size: 12 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [5, 5],
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        color: '#6c757d',
                        font: { size: 12 },
                        stepSize: 20
                    }
                }
            }
        }
    });
});

/**
 * Apply filters to the report table using JS
 */
function applyFilters() {
    const dateQuery = document.getElementById('dateFilter').value; // YYYY-MM-DD
    const vaccineQuery = document.getElementById('vaccineFilter').value;
    const rows = document.querySelectorAll('.report-row');

    rows.forEach(row => {
        const rowDate = row.getAttribute('data-date'); // YYYY-MM-DD
        const rowVaccine = row.getAttribute('data-vaccine');

        const dateMatch = dateQuery === "" || rowDate === dateQuery;
        const vaccineMatch = vaccineQuery === "" || rowVaccine.includes(vaccineQuery);

        if (dateMatch && vaccineMatch) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });

    // Handle "No results" message if needed
    const visibleRows = document.querySelectorAll('.report-row[style="display: px-4;"]'); // Wait, check empty string
    // Better way:
    let visibleCount = 0;
    rows.forEach(r => { if(r.style.display !== "none") visibleCount++; });
    
    // Optional: show a message if visibleCount === 0
}

/**
 * Reset filters and show all rows
 */
function resetFilters() {
    document.getElementById('filterForm').reset();
    const rows = document.querySelectorAll('.report-row');
    rows.forEach(row => {
        row.style.display = "";
    });
}
</script>

<?php
include '../includes/footer.php';
?>