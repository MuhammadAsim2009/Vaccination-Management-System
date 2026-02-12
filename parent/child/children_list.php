<?php
/**
 * Children List Page - Parent Panel
 * Displays all registered children of the logged-in parent
 * with vaccination status and quick actions
 */

// Include authentication check
include '../includes/auth_check.php';
include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<!-- Main Content -->
    <div class="container-fluid px-4">
        
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="mb-2 fw-bold text-dark">My Children</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Children</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <button class="btn btn-primary btn-md shadow-sm" onclick="window.location.href='add_child.php'">
                            <i class="fas fa-plus-circle me-2"></i>Add Child
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search by Name -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small">Search Child</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchChild" class="form-control border-start-0 ps-0" placeholder="Search by name...">
                        </div>
                    </div>

                    <!-- Filter by Gender -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-secondary small">Gender</label>
                        <select id="filterGender" class="form-select">
                            <option value="">All Genders</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <!-- Filter by Vaccination Status -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-secondary small">Vaccination Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">All Status</option>
                            <option value="Completed">Completed</option>
                            <option value="Pending">Pending</option>
                            <option value="Missed">Missed</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="resetFilters" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children Cards Grid -->
        <div class="row g-4 mb-5" id="childrenCardsContainer">
            
            <!-- Child Card 1 -->
            <div class="col-lg-4 col-md-6 child-card" data-name="Sarah Ahmed" data-gender="Female" data-status="Completed">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle bg-soft-primary me-3">
                                <img src="https://ui-avatars.com/api/?name=Sarah+Ahmed&background=e3f2fd&color=1976d2&size=80&bold=true" 
                                     alt="Sarah Ahmed" class="rounded-circle" width="80" height="80">
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">Sarah Ahmed</h5>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-birthday-cake me-1"></i>5 years old
                                </p>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-venus text-danger mb-1"></i>
                                    <p class="mb-0 small fw-semibold">Female</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-tint text-danger mb-1"></i>
                                    <p class="mb-0 small fw-semibold">A+</p>
                                </div>
                            </div>
                        </div>

                        <div class="vaccination-stats mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Vaccines Completed</span>
                                <span class="badge bg-success">12/15</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 bg-soft-info py-2 mb-3">
                            <i class="fas fa-syringe me-2"></i>
                            <small><strong>3</strong> upcoming vaccines</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='child_details.php'">
                                <i class="fas fa-eye me-2"></i>View Details
                            </button>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-history me-1"></i>History
                                </button>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-calendar-plus me-1"></i>Schedule
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Child Card 2 -->
            <div class="col-lg-4 col-md-6 child-card" data-name="Ahmed Ali" data-gender="Male" data-status="Pending">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle bg-soft-success me-3">
                                <img src="https://ui-avatars.com/api/?name=Ahmed+Ali&background=e8f5e9&color=388e3c&size=80&bold=true" 
                                     alt="Ahmed Ali" class="rounded-circle" width="80" height="80">
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">Ahmed Ali</h5>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-birthday-cake me-1"></i>3 years old
                                </p>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-mars text-primary mb-1"></i>
                                    <p class="mb-0 small fw-semibold">Male</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-tint text-danger mb-1"></i>
                                    <p class="mb-0 small fw-semibold">O+</p>
                                </div>
                            </div>
                        </div>

                        <div class="vaccination-stats mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Vaccines Completed</span>
                                <span class="badge bg-warning text-dark">8/12</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 67%;" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 bg-soft-warning py-2 mb-3">
                            <i class="fas fa-syringe me-2"></i>
                            <small><strong>4</strong> upcoming vaccines</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='child_details.php'">
                                <i class="fas fa-eye me-2"></i>View Details
                            </button>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-history me-1"></i>History
                                </button>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-calendar-plus me-1"></i>Schedule
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Child Card 3 -->
            <div class="col-lg-4 col-md-6 child-card" data-name="Fatima Hassan" data-gender="Female" data-status="Missed">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle bg-soft-danger me-3">
                                <img src="https://ui-avatars.com/api/?name=Fatima+Hassan&background=ffebee&color=c62828&size=80&bold=true" 
                                     alt="Fatima Hassan" class="rounded-circle" width="80" height="80">
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">Fatima Hassan</h5>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-birthday-cake me-1"></i>7 years old
                                </p>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-venus text-danger mb-1"></i>
                                    <p class="mb-0 small fw-semibold">Female</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-tint text-danger mb-1"></i>
                                    <p class="mb-0 small fw-semibold">B+</p>
                                </div>
                            </div>
                        </div>

                        <div class="vaccination-stats mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Vaccines Completed</span>
                                <span class="badge bg-danger">10/18</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 56%;" aria-valuenow="56" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="alert alert-danger border-0 bg-soft-danger py-2 mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small><strong>2</strong> missed vaccines</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='child_details.php'">
                                <i class="fas fa-eye me-2"></i>View Details
                            </button>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-history me-1"></i>History
                                </button>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-calendar-plus me-1"></i>Schedule
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Child Card 4 -->
            <div class="col-lg-4 col-md-6 child-card" data-name="Omar Khan" data-gender="Male" data-status="Completed">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle bg-soft-info me-3">
                                <img src="https://ui-avatars.com/api/?name=Omar+Khan&background=e1f5fe&color=0277bd&size=80&bold=true" 
                                     alt="Omar Khan" class="rounded-circle" width="80" height="80">
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">Omar Khan</h5>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-birthday-cake me-1"></i>2 years old
                                </p>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-mars text-primary mb-1"></i>
                                    <p class="mb-0 small fw-semibold">Male</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-tint text-danger mb-1"></i>
                                    <p class="mb-0 small fw-semibold">AB+</p>
                                </div>
                            </div>
                        </div>

                        <div class="vaccination-stats mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Vaccines Completed</span>
                                <span class="badge bg-success">10/10</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="alert alert-success border-0 bg-soft-success py-2 mb-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <small>All vaccines up to date!</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='child_details.php'">
                                <i class="fas fa-eye me-2"></i>View Details
                            </button>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-history me-1"></i>History
                                </button>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-calendar-plus me-1"></i>Schedule
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Child Card 5 -->
            <div class="col-lg-4 col-md-6 child-card" data-name="Aisha Malik" data-gender="Female" data-status="Pending">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle bg-soft-warning me-3">
                                <img src="https://ui-avatars.com/api/?name=Aisha+Malik&background=fff3e0&color=f57c00&size=80&bold=true" 
                                     alt="Aisha Malik" class="rounded-circle" width="80" height="80">
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">Aisha Malik</h5>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-birthday-cake me-1"></i>4 years old
                                </p>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-venus text-danger mb-1"></i>
                                    <p class="mb-0 small fw-semibold">Female</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-tint text-danger mb-1"></i>
                                    <p class="mb-0 small fw-semibold">O-</p>
                                </div>
                            </div>
                        </div>

                        <div class="vaccination-stats mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Vaccines Completed</span>
                                <span class="badge bg-warning text-dark">9/14</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 64%;" aria-valuenow="64" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 bg-soft-info py-2 mb-3">
                            <i class="fas fa-syringe me-2"></i>
                            <small><strong>5</strong> upcoming vaccines</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='child_details.php'">
                                <i class="fas fa-eye me-2"></i>View Details
                            </button>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-history me-1"></i>History
                                </button>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-calendar-plus me-1"></i>Schedule
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Child Card 6 -->
            <div class="col-lg-4 col-md-6 child-card" data-name="Zain Abbas" data-gender="Male" data-status="Completed">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle bg-soft-success me-3">
                                <img src="https://ui-avatars.com/api/?name=Zain+Abbas&background=f1f8e9&color=558b2f&size=80&bold=true" 
                                     alt="Zain Abbas" class="rounded-circle" width="80" height="80">
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">Zain Abbas</h5>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-birthday-cake me-1"></i>6 years old
                                </p>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-mars text-primary mb-1"></i>
                                    <p class="mb-0 small fw-semibold">Male</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light rounded p-2 text-center">
                                    <i class="fas fa-tint text-danger mb-1"></i>
                                    <p class="mb-0 small fw-semibold">A-</p>
                                </div>
                            </div>
                        </div>

                        <div class="vaccination-stats mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Vaccines Completed</span>
                                <span class="badge bg-success">16/16</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="alert alert-success border-0 bg-soft-success py-2 mb-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <small>All vaccines up to date!</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='child_details.php'">
                                <i class="fas fa-eye me-2"></i>View Details
                            </button>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-history me-1"></i>History
                                </button>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-calendar-plus me-1"></i>Schedule
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children Table View -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-table me-2 text-primary"></i>Detailed Children List
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="childrenTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 fw-semibold text-secondary">Child Name</th>
                                <th class="px-4 py-3 fw-semibold text-secondary">Age</th>
                                <th class="px-4 py-3 fw-semibold text-secondary">Gender</th>
                                <th class="px-4 py-3 fw-semibold text-secondary">Last Vaccination</th>
                                <th class="px-4 py-3 fw-semibold text-secondary">Next Due Date</th>
                                <th class="px-4 py-3 fw-semibold text-secondary">Status</th>
                                <th class="px-4 py-3 fw-semibold text-secondary text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1 -->
                            <tr class="table-row" data-name="Sarah Ahmed" data-gender="Female" data-status="Completed">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Sarah+Ahmed&background=e3f2fd&color=1976d2&size=40&bold=true" 
                                             alt="Sarah Ahmed" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Sarah Ahmed</div>
                                            <small class="text-muted">Blood: A+</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">5 years</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-venus text-danger me-1"></i>Female
                                </td>
                                <td class="px-4 py-3">Jan 15, 2026</td>
                                <td class="px-4 py-3">Mar 20, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Completed
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="table-row" data-name="Ahmed Ali" data-gender="Male" data-status="Pending">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Ahmed+Ali&background=e8f5e9&color=388e3c&size=40&bold=true" 
                                             alt="Ahmed Ali" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Ahmed Ali</div>
                                            <small class="text-muted">Blood: O+</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">3 years</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-mars text-primary me-1"></i>Male
                                </td>
                                <td class="px-4 py-3">Dec 10, 2025</td>
                                <td class="px-4 py-3">Feb 28, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 3 -->
                            <tr class="table-row" data-name="Fatima Hassan" data-gender="Female" data-status="Missed">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Fatima+Hassan&background=ffebee&color=c62828&size=40&bold=true" 
                                             alt="Fatima Hassan" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Fatima Hassan</div>
                                            <small class="text-muted">Blood: B+</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">7 years</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-venus text-danger me-1"></i>Female
                                </td>
                                <td class="px-4 py-3">Nov 05, 2025</td>
                                <td class="px-4 py-3">Jan 12, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Missed
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 4 -->
                            <tr class="table-row" data-name="Omar Khan" data-gender="Male" data-status="Completed">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Omar+Khan&background=e1f5fe&color=0277bd&size=40&bold=true" 
                                             alt="Omar Khan" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Omar Khan</div>
                                            <small class="text-muted">Blood: AB+</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">2 years</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-mars text-primary me-1"></i>Male
                                </td>
                                <td class="px-4 py-3">Jan 20, 2026</td>
                                <td class="px-4 py-3">Apr 15, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Completed
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 5 -->
                            <tr class="table-row" data-name="Aisha Malik" data-gender="Female" data-status="Pending">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Aisha+Malik&background=fff3e0&color=f57c00&size=40&bold=true" 
                                             alt="Aisha Malik" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Aisha Malik</div>
                                            <small class="text-muted">Blood: O-</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">4 years</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-venus text-danger me-1"></i>Female
                                </td>
                                <td class="px-4 py-3">Dec 28, 2025</td>
                                <td class="px-4 py-3">Mar 05, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 6 -->
                            <tr class="table-row" data-name="Zain Abbas" data-gender="Male" data-status="Completed">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Zain+Abbas&background=f1f8e9&color=558b2f&size=40&bold=true" 
                                             alt="Zain Abbas" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Zain Abbas</div>
                                            <small class="text-muted">Blood: A-</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">6 years</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-mars text-primary me-1"></i>Male
                                </td>
                                <td class="px-4 py-3">Jan 18, 2026</td>
                                <td class="px-4 py-3">May 10, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Completed
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 7 -->
                            <tr class="table-row" data-name="Maryam Raza" data-gender="Female" data-status="Pending">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Maryam+Raza&background=fce4ec&color=c2185b&size=40&bold=true" 
                                             alt="Maryam Raza" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Maryam Raza</div>
                                            <small class="text-muted">Blood: B-</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">1 year</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-venus text-danger me-1"></i>Female
                                </td>
                                <td class="px-4 py-3">Jan 25, 2026</td>
                                <td class="px-4 py-3">Feb 25, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 8 -->
                            <tr class="table-row" data-name="Hassan Iqbal" data-gender="Male" data-status="Missed">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Hassan+Iqbal&background=ede7f6&color=5e35b1&size=40&bold=true" 
                                             alt="Hassan Iqbal" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Hassan Iqbal</div>
                                            <small class="text-muted">Blood: AB-</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">8 years</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-mars text-primary me-1"></i>Male
                                </td>
                                <td class="px-4 py-3">Oct 20, 2025</td>
                                <td class="px-4 py-3">Jan 05, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Missed
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 9 -->
                            <tr class="table-row" data-name="Laiba Tariq" data-gender="Female" data-status="Completed">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Laiba+Tariq&background=e0f2f1&color=00695c&size=40&bold=true" 
                                             alt="Laiba Tariq" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Laiba Tariq</div>
                                            <small class="text-muted">Blood: O+</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">5 years</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-venus text-danger me-1"></i>Female
                                </td>
                                <td class="px-4 py-3">Jan 22, 2026</td>
                                <td class="px-4 py-3">Apr 22, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Completed
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 10 -->
                            <tr class="table-row" data-name="Ibrahim Shah" data-gender="Male" data-status="Pending">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Ibrahim+Shah&background=fff9c4&color=f57f17&size=40&bold=true" 
                                             alt="Ibrahim Shah" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <div class="fw-semibold text-dark">Ibrahim Shah</div>
                                            <small class="text-muted">Blood: A+</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">3 years</td>
                                <td class="px-4 py-3">
                                    <i class="fas fa-mars text-primary me-1"></i>Male
                                </td>
                                <td class="px-4 py-3">Dec 15, 2025</td>
                                <td class="px-4 py-3">Mar 01, 2026</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" title="View Details" onclick="window.location.href='child_details.php'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Edit" onclick="window.location.href='update_child.php'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Search and Filter Functionality
    const searchInput = document.getElementById('searchChild');
    const genderFilter = document.getElementById('filterGender');
    const statusFilter = document.getElementById('filterStatus');
    const resetBtn = document.getElementById('resetFilters');
    
    // Filter Cards
    function filterCards() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedGender = genderFilter.value;
        const selectedStatus = statusFilter.value;
        
        const cards = document.querySelectorAll('.child-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            const gender = card.getAttribute('data-gender');
            const status = card.getAttribute('data-status');
            
            const matchesSearch = name.includes(searchTerm);
            const matchesGender = !selectedGender || gender === selectedGender;
            const matchesStatus = !selectedStatus || status === selectedStatus;
            
            if (matchesSearch && matchesGender && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Filter Table Rows
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedGender = genderFilter.value;
        const selectedStatus = statusFilter.value;
        
        const rows = document.querySelectorAll('.table-row');
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name').toLowerCase();
            const gender = row.getAttribute('data-gender');
            const status = row.getAttribute('data-status');
            
            const matchesSearch = name.includes(searchTerm);
            const matchesGender = !selectedGender || gender === selectedGender;
            const matchesStatus = !selectedStatus || status === selectedStatus;
            
            if (matchesSearch && matchesGender && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Apply filters on both cards and table
    function applyFilters() {
        filterCards();
        filterTable();
    }
    
    // Event Listeners
    searchInput.addEventListener('keyup', applyFilters);
    genderFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    
    // Reset Filters
    resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        genderFilter.value = '';
        statusFilter.value = '';
        applyFilters();
    });
    
    // Add smooth scroll animation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
});
</script>

<?php include '../includes/footer.php'; ?>
