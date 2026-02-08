<?php

include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Mock ID fetch - in real app would fetch from DB
$childId = isset($_GET['id']) ? $_GET['id'] : 'CH-2023001';
?>

<div class="main-content">
    <div class="container-fluid px-4 py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold">Child Profile</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="view_children.php" class="text-decoration-none">Children</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
            <a href="view_children.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="row">
            <!-- Left Column: Child & Parent Info -->
            <div class="col-lg-4 mb-4">
                <!-- Child Info Card -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Child Information</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="fas fa-baby"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Ahmed Ali</h5>
                        <p class="text-muted mb-3">ID: #<?php echo htmlspecialchars($childId); ?></p>
                        
                        <div class="text-start mt-4">
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted small">Date of Birth</span>
                                <span class="fw-medium">15 Jan, 2023</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted small">Gender</span>
                                <span class="fw-medium">Male</span>
                            </div>
                            <div class="d-flex justify-content-between mb-0">
                                <span class="text-muted small">Blood Group</span>
                                <span class="fw-medium">O+</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent Info Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Parent Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Muhammad Ali</h6>
                                <p class="text-muted small mb-0">Father</p>
                            </div>
                        </div>
                        
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item px-0 d-flex align-items-center border-0 pb-1">
                                <i class="fas fa-phone-alt text-muted me-2" style="width: 16px;"></i>
                                <span>+92 300 1234567</span>
                            </li>
                            <li class="list-group-item px-0 d-flex align-items-center border-0 pt-1">
                                <i class="fas fa-map-marker-alt text-muted me-2" style="width: 16px;"></i>
                                <span>House #123, Street 5, Scheme 33, Karachi</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Vaccination History -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">Vaccination History</h6>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill">3 / 10 Completed</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th class="ps-4">Vaccine Name</th>
                                        <th>Scheduled Date</th>
                                        <th>Vaccinated Date</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Administered By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Completed Vaccinations -->
                                    <tr>
                                        <td class="ps-4 fw-medium">BCG</td>
                                        <td>2023-01-15</td>
                                        <td>2023-01-16</td>
                                        <td><span class="badge bg-success bg-opacity-10 text-success">Completed</span></td>
                                        <td class="text-end pe-4 text-muted small">Dr. Smith</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-medium">OPV-0</td>
                                        <td>2023-01-15</td>
                                        <td>2023-01-16</td>
                                        <td><span class="badge bg-success bg-opacity-10 text-success">Completed</span></td>
                                        <td class="text-end pe-4 text-muted small">Nurse Joy</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-medium">Hepatitis B-0</td>
                                        <td>2023-01-15</td>
                                        <td>2023-01-16</td>
                                        <td><span class="badge bg-success bg-opacity-10 text-success">Completed</span></td>
                                        <td class="text-end pe-4 text-muted small">Dr. Smith</td>
                                    </tr>
                                    
                                    <!-- Pending Vaccinations -->
                                    <tr class="table-active bg-light bg-opacity-10">
                                        <td class="ps-4 fw-medium">OPV-1</td>
                                        <td>2023-02-26</td>
                                        <td>-</td>
                                        <td><span class="badge bg-warning bg-opacity-10 text-warning">Pending</span></td>
                                        <td class="text-end pe-4 text-muted small">-</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-medium">Penta-1</td>
                                        <td>2023-02-26</td>
                                        <td>-</td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Upcoming</span></td>
                                        <td class="text-end pe-4 text-muted small">-</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-medium">Pneumococcal-1</td>
                                        <td>2023-02-26</td>
                                        <td>-</td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Upcoming</span></td>
                                        <td class="text-end pe-4 text-muted small">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
