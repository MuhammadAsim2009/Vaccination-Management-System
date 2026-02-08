<?php
// Reusable Includes
include '../includes/auth_check.php';
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
                        <label for="dateFilter" class="form-label fw-semibold small text-muted text-uppercase">Select Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-calendar-day"></i>
                            </span>
                            <input type="date" id="dateFilter" class="form-control border-start-0 ps-0" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="col-md-8 text-md-end pt-md-4">
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
                        <div class="d-flex align-items-center text-success small fw-medium">
                            <i class="fas fa-arrow-up me-1"></i>
                            <span>8% from yesterday</span>
                        </div>
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
                        <div class="d-flex align-items-center text-muted small fw-medium">
                            <i class="fas fa-minus me-1"></i>
                            <span>Stable today</span>
                        </div>
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
                        <div class="d-flex align-items-center text-danger small fw-medium">
                            <i class="fas fa-arrow-down me-1"></i>
                            <span>5% from yesterday</span>
                        </div>
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
    // --------------------------------------------------
    // 1. DUMMY DATA SETUP
    // --------------------------------------------------
    const todayStr = new Date().toISOString().split('T')[0];
    
    // Generate some fake dates around today
    const reportsData = {
        [todayStr]: {
            completed: 24,
            pending: 8,
            missed: 3,
            records: [
                { id: 1, child: "Ahmad Khan", vaccine: "BCG", hospital: "City General Hospital", status: "Completed" },
                { id: 2, child: "Sara Malik", vaccine: "Polio (OPV)", hospital: "Children's Medical Center", status: "Completed" },
                { id: 3, child: "Zain Ali", vaccine: "DTP-HepB-Hib", hospital: "Family Wellness Clinic", status: "Pending" },
                { id: 4, child: "Noor Fatima", vaccine: "Measles", hospital: "City General Hospital", status: "Missed" },
                { id: 5, child: "Bilal Raza", vaccine: "BCG", hospital: "Aga Khan Health Service", status: "Completed" },
                { id: 6, child: "Hania Sheikh", vaccine: "Hepatitis B", hospital: "Children's Medical Center", status: "Pending" }
            ]
        },
        "2026-02-05": {
            completed: 18,
            pending: 5,
            missed: 2,
            records: [
                { id: 1, child: "Arham Tahir", vaccine: "BCG", hospital: "City General Hospital", status: "Completed" },
                { id: 2, child: "Eshal Naveed", vaccine: "Polio (OPV)", hospital: "Family Wellness", status: "Completed" },
                { id: 3, child: "Hamza Butt", vaccine: "Rotavirus", hospital: "Children's Medical Center", status: "Missed" }
            ]
        },
        "2026-02-04": {
            completed: 30,
            pending: 12,
            missed: 5,
            records: [
                { id: 1, child: "Yusuf Khan", vaccine: "PCV", hospital: "Shaukat Khanum", status: "Completed" },
                { id: 2, child: "Ayesha Bibi", vaccine: "IPV", hospital: "PIMS Hospital", status: "Pending" }
            ]
        }
    };

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
    const dateInput = document.getElementById('dateFilter');
    const labelDate = document.getElementById('displayDateLabel');
    const tableBody = document.getElementById('reportTableBody');
    const emptyState = document.getElementById('emptyState');
    
    function updateDashboard(selectedDate) {
        // Handle "Today" label
        if (selectedDate === todayStr) {
            labelDate.innerText = "Today";
        } else {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            labelDate.innerText = new Date(selectedDate).toLocaleDateString(undefined, options);
        }

        const data = reportsData[selectedDate] || { completed: 0, pending: 0, missed: 0, records: [] };

        // Update Cards
        document.getElementById('completedCount').innerText = data.completed;
        document.getElementById('pendingCount').innerText = data.pending;
        document.getElementById('missedCount').innerText = data.missed;

        // --- Update Chart (7-Day Trend) ---
        const last7Days = [];
        const completedTrend = [];
        const pendingTrend = [];
        const missedTrend = [];

        for (let i = 6; i >= 0; i--) {
            const d = new Date(selectedDate);
            d.setDate(d.getDate() - i);
            const dStr = d.toISOString().split('T')[0];
            
            // Format label for chart (e.g., "Feb 06")
            last7Days.push(d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' }));
            
            const dayData = reportsData[dStr] || { 
                // Generate some random consistent noise for dates with no hardcoded dummy data
                completed: Math.floor(Math.random() * 10) + 15, 
                pending: Math.floor(Math.random() * 5) + 3, 
                missed: Math.floor(Math.random() * 3) 
            };
            
            completedTrend.push(dayData.completed);
            pendingTrend.push(dayData.pending);
            missedTrend.push(dayData.missed);
        }

        vaccinationChart.data.labels = last7Days;
        vaccinationChart.data.datasets[0].data = completedTrend;
        vaccinationChart.data.datasets[1].data = pendingTrend;
        vaccinationChart.data.datasets[2].data = missedTrend;
        vaccinationChart.update();

        // Update Table
        tableBody.innerHTML = '';
        if (data.records.length > 0) {
            emptyState.classList.add('d-none');
            data.records.forEach((rec, index) => {
                let statusBadge = '';
                if(rec.status === 'Completed') {
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
                                    <span class="text-muted x-small" style="font-size: 0.75rem;">Patient ID: #VMS-${1000 + rec.id}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium text-primary">${rec.vaccine}</span>
                                <span class="text-muted x-small" style="font-size: 0.75rem;">Dose 1 of 2</span>
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
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm border shadow-sm dropdown-toggle hide-caret" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item py-2 small" href="#"><i class="fas fa-eye me-2 text-primary"></i>View Details</a></li>
                                    <li><a class="dropdown-item py-2 small" href="#"><i class="fas fa-edit me-2 text-info"></i>Edit Record</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2 small text-danger" href="#"><i class="fas fa-trash me-2"></i>Remove</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            emptyState.classList.remove('d-none');
        }
    }

    // --------------------------------------------------
    // 4. EVENT LISTENERS
    // --------------------------------------------------
    dateInput.addEventListener('change', function() {
        updateDashboard(this.value);
    });

    // Initial Load
    updateDashboard(todayStr);
});
</script>

<?php
include '../includes/footer.php';
?>