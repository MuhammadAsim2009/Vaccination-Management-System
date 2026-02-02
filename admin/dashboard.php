<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VMS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- ============================================
         TOP NAVBAR
         ============================================ -->
    <?php include 'includes/header.php'; ?>

    <!-- ============================================
         SIDEBAR NAVIGATION
         ============================================ -->
    <?php include 'includes/sidebar.php'; ?>

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
                    <h1 class="h3 mb-1 fw-bold">Admin Dashboard</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Overview</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <button class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>
                        Export Report
                    </button>
                </div>
            </div>

            <!-- ============================================
                 SUMMARY CARDS SECTION
                 ============================================ -->
            <div class="row g-4 mb-4">
                
                <!-- Total Children Card -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h6 class="card-title">Total Children</h6>
                            <div class="card-icon card-icon-primary">
                                <i class="fas fa-child"></i>
                            </div>
                        </div>
                        <div class="card-value">1,247</div>
                        <div class="card-change card-change-positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>12% from last month</span>
                        </div>
                    </div>
                </div>

                <!-- Total Vaccinations Card -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h6 class="card-title">Total Vaccinations</h6>
                            <div class="card-icon card-icon-success">
                                <i class="fas fa-syringe"></i>
                            </div>
                        </div>
                        <div class="card-value">8,934</div>
                        <div class="card-change card-change-positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>8% from last month</span>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments Card -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h6 class="card-title">Upcoming Appointments</h6>
                            <div class="card-icon card-icon-warning">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <div class="card-value">342</div>
                        <div class="card-change card-change-negative">
                            <i class="fas fa-arrow-down"></i>
                            <span>5% from last week</span>
                        </div>
                    </div>
                </div>

                <!-- Registered Hospitals Card -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h6 class="card-title">Registered Hospitals</h6>
                            <div class="card-icon card-icon-teal">
                                <i class="fas fa-hospital"></i>
                            </div>
                        </div>
                        <div class="card-value">48</div>
                        <div class="card-change card-change-positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>3 new this month</span>
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
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="mb-0 fw-bold">Vaccination Trends</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Last 6 Months
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Last 3 Months</a></li>
                                    <li><a class="dropdown-item" href="#">Last 6 Months</a></li>
                                    <li><a class="dropdown-item" href="#">Last Year</a></li>
                                </ul>
                            </div>
                        </div>
                        <div style="height: 300px; position: relative;">
                            <canvas id="vaccinationTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Vaccination Status Pie Chart -->
                <div class="col-12 col-lg-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="mb-0 fw-bold">Vaccination Status</h5>
                        </div>
                        <div style="height: 300px; position: relative;">
                            <canvas id="vaccinationStatusChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-success me-2" style="width: 12px; height: 12px;"></div>
                                    <span class="small">Completed</span>
                                </div>
                                <span class="fw-semibold">65%</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-warning me-2" style="width: 12px; height: 12px;"></div>
                                    <span class="small">Pending</span>
                                </div>
                                <span class="fw-semibold">25%</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-danger me-2" style="width: 12px; height: 12px;"></div>
                                    <span class="small">Missed</span>
                                </div>
                                <span class="fw-semibold">10%</span>
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
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="mb-0 fw-bold">Recent Vaccination Activity</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                View All
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Child Name</th>
                                            <th>Vaccine</th>
                                            <th>Date</th>
                                            <th>Hospital</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 0.875rem; font-weight: 600;">
                                                        AR
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Aarav Raj</div>
                                                        <div class="text-muted small">ID: CH001</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">BCG</div>
                                                <div class="text-muted small">Tuberculosis</div>
                                            </td>
                                            <td>
                                                <div>Jan 15, 2024</div>
                                                <div class="text-muted small">10:30 AM</div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">City General Hospital</div>
                                                <div class="text-muted small">Mumbai</div>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Completed
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 0.875rem; font-weight: 600;">
                                                        PK
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Priya Kumar</div>
                                                        <div class="text-muted small">ID: CH002</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">DPT</div>
                                                <div class="text-muted small">Diphtheria, Pertussis, Tetanus</div>
                                            </td>
                                            <td>
                                                <div>Jan 18, 2024</div>
                                                <div class="text-muted small">2:00 PM</div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">Metro Health Center</div>
                                                <div class="text-muted small">Delhi</div>
                                            </td>
                                            <td>
                                                <span class="badge badge-pending">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Pending
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 0.875rem; font-weight: 600;">
                                                        RS
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Rohan Sharma</div>
                                                        <div class="text-muted small">ID: CH003</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">Polio</div>
                                                <div class="text-muted small">Poliomyelitis</div>
                                            </td>
                                            <td>
                                                <div>Jan 20, 2024</div>
                                                <div class="text-muted small">11:00 AM</div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">Central Medical</div>
                                                <div class="text-muted small">Bangalore</div>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Completed
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-teal text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 0.875rem; font-weight: 600;">
                                                        SK
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Sneha Kapoor</div>
                                                        <div class="text-muted small">ID: CH004</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">Measles</div>
                                                <div class="text-muted small">Measles, Mumps, Rubella</div>
                                            </td>
                                            <td>
                                                <div>Jan 22, 2024</div>
                                                <div class="text-muted small">3:30 PM</div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">Sunshine Hospital</div>
                                                <div class="text-muted small">Hyderabad</div>
                                            </td>
                                            <td>
                                                <span class="badge badge-rejected">
                                                    <i class="fas fa-times-circle me-1"></i>
                                                    Missed
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 0.875rem; font-weight: 600;">
                                                        VK
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Vikram Khanna</div>
                                                        <div class="text-muted small">ID: CH005</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">Hepatitis B</div>
                                                <div class="text-muted small">Hepatitis B Vaccine</div>
                                            </td>
                                            <td>
                                                <div>Jan 25, 2024</div>
                                                <div class="text-muted small">9:00 AM</div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">Prime Care Clinic</div>
                                                <div class="text-muted small">Pune</div>
                                            </td>
                                            <td>
                                                <span class="badge badge-pending">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Pending
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </button>
                                            </td>
                                        </tr>
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
         FOOTER
         ============================================ -->
    <?php include 'includes/footer.php'; ?>

    <!-- ============================================
         SCRIPTS
         ============================================ -->
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="../assets/js/script.js"></script>

    <!-- ============================================
         CHART INITIALIZATION
         ============================================ -->
    <script>
        // Wait for DOM and Chart.js to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Vaccination Trends Line Chart
            const trendsCtx = document.getElementById('vaccinationTrendsChart');
            if (trendsCtx) {
                new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
                        datasets: [{
                            label: 'Vaccinations',
                            data: [1200, 1350, 1420, 1580, 1650, 1800],
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

            // Vaccination Status Doughnut Chart
            const statusCtx = document.getElementById('vaccinationStatusChart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'Pending', 'Missed'],
                        datasets: [{
                            data: [65, 25, 10],
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

</body>
</html>

