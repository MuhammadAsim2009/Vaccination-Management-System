<?php
/**
 * Parent Dashboard
 * Vaccination Management System
 * 
 * Overview page showing children vaccination status and statistics
 */

// Set current page for sidebar active state
// $current_page = 'dashboard';

// Include authentication check
include 'includes/auth_check.php';

// Include header
include 'includes/header.php';

// Include sidebar
include 'includes/sidebar.php';
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
                        <i class="fas fa-user-circle me-2"></i>Welcome, <span class="text-primary">John Doe</span>
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
                            <h3 class="mb-0 fw-bold text-dark">3</h3>
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
                            <h3 class="mb-0 fw-bold text-dark">5</h3>
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
                            <h3 class="mb-0 fw-bold text-dark">24</h3>
                        </div>
                        <div class="stats-icon bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i>80% completion rate
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
                            <h3 class="mb-0 fw-bold text-dark">2</h3>
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
                    
                    <!-- Reminder Item 1 -->
                    <div class="reminder-item p-3 mb-3 rounded-3" style="background: linear-gradient(135deg, rgba(74, 144, 226, 0.05) 0%, rgba(80, 200, 120, 0.05) 100%);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Emma Doe</h6>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-syringe me-1"></i>MMR Vaccine
                                </p>
                            </div>
                            <span class="badge bg-warning text-dark" id="countdown1">3 days</span>
                        </div>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-calendar me-1"></i>Due: Feb 12, 2026
                        </p>
                    </div>

                    <!-- Reminder Item 2 -->
                    <div class="reminder-item p-3 mb-3 rounded-3" style="background: linear-gradient(135deg, rgba(74, 144, 226, 0.05) 0%, rgba(80, 200, 120, 0.05) 100%);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Oliver Doe</h6>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-syringe me-1"></i>Hepatitis B
                                </p>
                            </div>
                            <span class="badge bg-info text-dark" id="countdown2">7 days</span>
                        </div>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-calendar me-1"></i>Due: Feb 16, 2026
                        </p>
                    </div>

                    <!-- Reminder Item 3 -->
                    <div class="reminder-item p-3 rounded-3" style="background: linear-gradient(135deg, rgba(74, 144, 226, 0.05) 0%, rgba(80, 200, 120, 0.05) 100%);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Sophia Doe</h6>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-syringe me-1"></i>Polio Vaccine
                                </p>
                            </div>
                            <span class="badge bg-success text-white" id="countdown3">14 days</span>
                        </div>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-calendar me-1"></i>Due: Feb 23, 2026
                        </p>
                    </div>

                </div>
                <div class="card-footer bg-white border-0 pb-4">
                    <a href="vaccination_schedule.php" class="btn btn-primary btn-sm w-100">
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
                        <a href="vaccination_report.php" class="btn btn-outline-primary btn-sm">
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
                                <!-- Row 1 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-child text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">Emma Doe</span>
                                        </div>
                                    </td>
                                    <td>Hepatitis A</td>
                                    <td>Feb 5, 2026</td>
                                    <td>City General Hospital</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 2 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-child text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">Oliver Doe</span>
                                        </div>
                                    </td>
                                    <td>DPT Vaccine</td>
                                    <td>Feb 3, 2026</td>
                                    <td>Metro Health Center</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 3 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-child text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">Sophia Doe</span>
                                        </div>
                                    </td>
                                    <td>Polio (IPV)</td>
                                    <td>Jan 28, 2026</td>
                                    <td>Children's Hospital</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 4 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-child text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">Emma Doe</span>
                                        </div>
                                    </td>
                                    <td>Varicella</td>
                                    <td>Jan 20, 2026</td>
                                    <td>City General Hospital</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 5 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-child text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">Oliver Doe</span>
                                        </div>
                                    </td>
                                    <td>Influenza</td>
                                    <td>Jan 15, 2026</td>
                                    <td>Metro Health Center</td>
                                    <td><span class="badge bg-danger">Missed</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 6 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-child text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">Sophia Doe</span>
                                        </div>
                                    </td>
                                    <td>MMR Vaccine</td>
                                    <td>Jan 10, 2026</td>
                                    <td>Children's Hospital</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 7 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-child text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">Emma Doe</span>
                                        </div>
                                    </td>
                                    <td>Rotavirus</td>
                                    <td>Jan 5, 2026</td>
                                    <td>City General Hospital</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 8 -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-child text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">Oliver Doe</span>
                                        </div>
                                    </td>
                                    <td>Pneumococcal</td>
                                    <td>Dec 28, 2025</td>
                                    <td>Metro Health Center</td>
                                    <td><span class="badge bg-danger">Missed</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
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

<!-- Chart.js Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Vaccination Progress Chart
    const ctx = document.getElementById('vaccinationChart').getContext('2d');
    const vaccinationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Emma Doe', 'Oliver Doe', 'Sophia Doe'],
            datasets: [
                {
                    label: 'Completed',
                    data: [8, 7, 9],
                    backgroundColor: 'rgba(80, 200, 120, 0.8)',
                    borderColor: 'rgba(80, 200, 120, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                },
                {
                    label: 'Pending',
                    data: [2, 3, 1],
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                },
                {
                    label: 'Missed',
                    data: [0, 2, 0],
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

    // Dummy countdown timer (updates every second)
    function updateCountdowns() {
        // This is a simple dummy implementation
        // In real app, calculate actual days remaining
        const countdown1 = document.getElementById('countdown1');
        const countdown2 = document.getElementById('countdown2');
        const countdown3 = document.getElementById('countdown3');
        
        // Static countdown for demo purposes
        // You can implement dynamic countdown based on actual dates
    }
    
    // Call once on load
    updateCountdowns();
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
