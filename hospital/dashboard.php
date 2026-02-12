<?php
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="mt-5 pt-3">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Home</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary">Hospital Dashboard</h2>
                <p class="text-muted">Welcome, City Hospital</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Appointments -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Total Appointments</h6>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-calendar-check fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2">1,245</h2>
                        <div class="d-flex align-items-center text-success small fw-medium">
                            <i class="fas fa-arrow-up me-1"></i>
                            <span>12% from last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Vaccinations -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Completed Vaccinations</h6>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-syringe fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2">856</h2>
                        <div class="d-flex align-items-center text-success small fw-medium">
                            <i class="fas fa-arrow-up me-1"></i>
                            <span>8% from last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Appointments -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Pending Appointments</h6>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-clock fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2">42</h2>
                        <div class="d-flex align-items-center text-danger small fw-medium">
                            <i class="fas fa-arrow-down me-1"></i>
                            <span>5% from last week</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Vaccines -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Available Vaccines</h6>
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-boxes fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2">12 Types</h2>
                        <div class="d-flex align-items-center text-success small fw-medium">
                            <i class="fas fa-arrow-up me-1"></i>
                            <span>New stock added</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Appointment Analytics Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Weekly Appointment Analytics</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="appointmentChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments Widget -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Upcoming</h5>
                        <a href="appointments.php" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <!-- Item 1 -->
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">Sarah Connor</h6>
                                    <small class="text-muted">Polio Vaccine - Dose 1</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-soft-primary text-primary mb-1">Today</span>
                                    <div class="small fw-bold text-muted">10:00 AM</div>
                                </div>
                            </li>
                            <!-- Item 2 -->
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">John Wick</h6>
                                    <small class="text-muted">BCG Vaccine</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-soft-warning text-warning mb-1">Tomorrow</span>
                                    <div class="small fw-bold text-muted">02:30 PM</div>
                                </div>
                            </li>
                            <!-- Item 3 -->
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">Bruce Wayne</h6>
                                    <small class="text-muted">MMR Vaccine</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-soft-success text-success mb-1">In 2 Days</span>
                                    <div class="small fw-bold text-muted">09:15 AM</div>
                                </div>
                            </li>
                            <!-- Item 4 -->
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">Clark Kent</h6>
                                    <small class="text-muted">Hepatitis B</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark mb-1">In 3 Days</span>
                                    <div class="small fw-bold text-muted">11:00 AM</div>
                                </div>
                            </li>
                             <!-- Item 5 -->
                             <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">Diana Prince</h6>
                                    <small class="text-muted">Rotavirus</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark mb-1">In 4 Days</span>
                                    <div class="small fw-bold text-muted">03:45 PM</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Today's Appointments Table -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                     <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-list-ul me-2 text-primary"></i>Today's Appointments</h5>
                        <div class="input-group w-auto">
                             <input type="text" class="form-control form-control-sm" placeholder="Search...">
                             <button class="btn btn-outline-secondary btn-sm" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">Appt ID</th>
                                        <th>Child Name</th>
                                        <th>Parent</th>
                                        <th>Vaccine</th>
                                        <th>Time</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Row 1 -->
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">#APT-001</td>
                                        <td>Sarah Connor</td>
                                        <td>Linda Hamilton</td>
                                        <td>Polio (OPV)</td>
                                        <td>10:00 AM</td>
                                        <td class="text-center"><span class="badge bg-soft-warning text-warning">Pending</span></td>
                                        <td class="text-end pe-3">
                                            <button class="btn btn-sm btn-primary rounded-pill px-3">Update</button>
                                        </td>
                                    </tr>
                                    <!-- Row 2 -->
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">#APT-002</td>
                                        <td>John Wick</td>
                                        <td>Helen Wick</td>
                                        <td>BCG</td>
                                        <td>11:30 AM</td>
                                        <td class="text-center"><span class="badge bg-soft-success text-success">Completed</span></td>
                                        <td class="text-end pe-3">
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled>Update</button>
                                        </td>
                                    </tr>
                                    <!-- Row 3 -->
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">#APT-003</td>
                                        <td>Bruce Wayne</td>
                                        <td>Martha Wayne</td>
                                        <td>MMR</td>
                                        <td>01:00 PM</td>
                                        <td class="text-center"><span class="badge bg-soft-danger text-danger">Missed</span></td>
                                        <td class="text-end pe-3">
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Reschedule</button>
                                        </td>
                                    </tr>
                                    <!-- Row 4 -->
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">#APT-004</td>
                                        <td>Clark Kent</td>
                                        <td>Martha Kent</td>
                                        <td>Hepatitis B</td>
                                        <td>02:45 PM</td>
                                        <td class="text-center"><span class="badge bg-soft-warning text-warning">Pending</span></td>
                                        <td class="text-end pe-3">
                                            <button class="btn btn-sm btn-primary rounded-pill px-3">Update</button>
                                        </td>
                                    </tr>
                                     <!-- Row 5 -->
                                     <tr>
                                        <td class="ps-3 fw-bold text-primary">#APT-005</td>
                                        <td>Diana Prince</td>
                                        <td>Hippolyta</td>
                                        <td>Rotavirus</td>
                                        <td>04:00 PM</td>
                                        <td class="text-center"><span class="badge bg-soft-warning text-warning">Pending</span></td>
                                        <td class="text-end pe-3">
                                            <button class="btn btn-sm btn-primary rounded-pill px-3">Update</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                     <div class="card-footer bg-white text-center py-2">
                         <a href="appointments.php" class="text-decoration-none small fw-bold">View All Appointments</a>
                     </div>
                </div>
            </div>

            <!-- Notifications Panel -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-bell me-2 text-primary"></i>Recent Notifications</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <!-- Notification 1 -->
                            <a href="#" class="list-group-item list-group-item-action py-3 border-bottom">
                                <div class="d-flex w-100 justify-content-between mb-1">
                                    <h6 class="mb-1 fw-bold text-dark">New Appointment Request</h6>
                                    <small class="text-muted">Now</small>
                                </div>
                                <p class="mb-1 small text-secondary">New appointment request received for Child: <strong>Peter Parker</strong>.</p>
                            </a>
                            <!-- Notification 2 -->
                            <a href="#" class="list-group-item list-group-item-action py-3 border-bottom">
                                <div class="d-flex w-100 justify-content-between mb-1">
                                    <h6 class="mb-1 fw-bold text-success">Vaccination Completed</h6>
                                    <small class="text-muted">1 hour ago</small>
                                </div>
                                <p class="mb-1 small text-secondary">Vaccination for <strong>John Wick</strong> marked as completed.</p>
                            </a>
                            <!-- Notification 3 -->
                            <a href="#" class="list-group-item list-group-item-action py-3 border-bottom">
                                <div class="d-flex w-100 justify-content-between mb-1">
                                    <h6 class="mb-1 fw-bold text-primary">Message from Parent</h6>
                                    <small class="text-muted">3 hours ago</small>
                                </div>
                                <p class="mb-1 small text-secondary">"Can we reschedule the appointment for tomorrow?"</p>
                            </a>
                             <!-- Notification 4 -->
                             <a href="#" class="list-group-item list-group-item-action py-3 border-bottom">
                                <div class="d-flex w-100 justify-content-between mb-1">
                                    <h6 class="mb-1 fw-bold text-warning">Stock Alert</h6>
                                    <small class="text-muted">Yesterday</small>
                                </div>
                                <p class="mb-1 small text-secondary">Polio vaccine stock is running low (Current: 15 doses).</p>
                            </a>
                        </div>
                    </div>
                     <div class="card-footer bg-white text-center py-2">
                         <a href="#" class="text-decoration-none small fw-bold">View All Notifications</a>
                     </div>
                </div>
            </div>
        </div>


    
    <!-- Initialize Chart -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('appointmentChart').getContext('2d');
            var appointmentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Appointments Received',
                        data: [12, 19, 15, 25, 22, 10, 8],
                        backgroundColor: 'rgba(13, 110, 253, 0.7)', // Primary color
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    },
                    {
                        label: 'Vaccinations Completed',
                        data: [10, 15, 12, 20, 18, 8, 5],
                        backgroundColor: 'rgba(25, 135, 84, 0.7)', // Success color
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: '#f0f0f0' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
    


<!-- Footer include closes the div and main tags opened here -->
<?php include 'includes/footer.php'; ?>
