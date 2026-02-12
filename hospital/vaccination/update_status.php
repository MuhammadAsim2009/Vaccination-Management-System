<?php
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<!-- Main Content -->
<main class="mt-5 pt-3 update-status-page">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item">Vaccination</li>
                        <li class="breadcrumb-item active" aria-current="page">Update Status</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary mb-1">Update Vaccination Status</h2>
                <p class="text-muted mb-0">Update child vaccination appointment status</p>
            </div>
        </div>

        <!-- Simulated Alert Feedback (UI-only) -->
        <div class="row mb-4">
            <div class="col-12">
                <div id="statusSuccessAlert" class="alert alert-success border-0 shadow-sm rounded-4 d-none" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-circle-check me-2"></i>
                        <span id="statusSuccessMessage">Status updated successfully.</span>
                    </div>
                </div>
                <div id="statusErrorAlert" class="alert alert-danger border-0 shadow-sm rounded-4 d-none" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-circle-exclamation me-2"></i>
                        <span id="statusErrorMessage">Unable to update status. Please check form inputs.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <!-- Appointment Summary Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-file-medical me-2 text-primary"></i>Appointment Summary</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Appointment ID</p>
                                    <p class="fw-semibold mb-0">APT-2026-0451</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Parent Name</p>
                                    <p class="fw-semibold mb-0">Sarah Johnson</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Child Name</p>
                                    <p class="fw-semibold mb-0">Ethan Johnson</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Vaccine Name</p>
                                    <p class="fw-semibold mb-0">MMR - Dose 1</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Appointment Date & Time</p>
                                    <p class="fw-semibold mb-0">February 12, 2026 - 10:30 AM</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Current Status</p>
                                    <span id="currentStatusBadge" class="badge rounded-pill px-3 py-2 bg-warning-subtle text-warning-emphasis">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Status Form -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-pen-to-square me-2 text-success"></i>Update Status Form</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <form id="statusUpdateForm" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="appointmentStatus" class="form-label fw-semibold">Status</label>
                                    <select id="appointmentStatus" class="form-select" required>
                                        <option value="">Select status</option>
                                        <option value="completed">Completed</option>
                                        <option value="pending" selected>Pending</option>
                                        <option value="missed">Missed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="vaccinationDate" class="form-label fw-semibold">Vaccination Date</label>
                                    <input type="date" id="vaccinationDate" class="form-control" value="2026-02-12" required>
                                </div>
                                <div class="col-12">
                                    <label for="doctorNotes" class="form-label fw-semibold">Doctor/Nurse Notes</label>
                                    <textarea id="doctorNotes" class="form-control" rows="4" placeholder="Enter notes about vaccination status..."></textarea>
                                </div>
                                <div class="col-12 d-flex flex-wrap gap-2 pt-1">
                                    <button type="submit" id="updateStatusBtn" class="btn btn-success px-4">
                                        <i class="fas fa-floppy-disk me-2"></i>Update Status
                                    </button>
                                    <button type="reset" id="resetStatusBtn" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-rotate-left me-2"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Status Timeline / History -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-timeline me-2 text-info"></i>Status Timeline</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <ul class="status-timeline list-unstyled mb-0" id="statusTimelineList">
                            <li class="timeline-item">
                                <span class="timeline-icon bg-primary-subtle text-primary-emphasis"><i class="fas fa-calendar-plus"></i></span>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Booked</h6>
                                    <p class="text-muted small mb-1">Appointment created by parent portal</p>
                                    <span class="badge bg-light text-secondary">Feb 08, 2026 - 09:14 AM</span>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="timeline-icon bg-info-subtle text-info-emphasis"><i class="fas fa-circle-check"></i></span>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Confirmed</h6>
                                    <p class="text-muted small mb-1">Hospital staff confirmed the appointment slot</p>
                                    <span class="badge bg-light text-secondary">Feb 10, 2026 - 03:36 PM</span>
                                </div>
                            </li>
                            <li class="timeline-item mb-0">
                                <span id="timelineStatusIcon" class="timeline-icon bg-warning-subtle text-warning-emphasis"><i class="fas fa-hourglass-half"></i></span>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Current Outcome</h6>
                                    <p class="text-muted small mb-1">Latest appointment processing status</p>
                                    <span id="timelineStatusBadge" class="badge rounded-pill bg-warning-subtle text-warning-emphasis">Pending</span>
                                    <span id="timelineStatusTime" class="badge bg-light text-secondary ms-1">Waiting for update</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Panel -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body px-4 pb-4 d-grid gap-3">
                        <button type="button" onclick="window.print()" class="btn btn-outline-primary quick-action-btn text-start">
                            <i class="fas fa-print me-2"></i>Print Vaccination Record
                        </button>
                        <button type="button" id="viewProfileBtn" class="btn btn-outline-success quick-action-btn text-start">
                            <i class="fas fa-child-reaching me-2"></i>View Child Profile
                        </button>
                        <button type="button" onclick="window.location.href='appointments.php'" class="btn btn-outline-secondary quick-action-btn text-start">
                            <i class="fas fa-arrow-left me-2"></i>Back to Appointments
                        </button>
                    </div>
                </div>

                <!-- Status Guide -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-shield-heart me-2 text-success"></i>Status Guide</h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-success-subtle text-success-emphasis me-2">Completed</span>
                            <small class="text-muted">Vaccination given successfully</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis me-2">Pending</span>
                            <small class="text-muted">Awaiting appointment completion</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis me-2">Missed</span>
                            <small class="text-muted">Child did not attend appointment</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis me-2">Cancelled</span>
                            <small class="text-muted">Appointment cancelled by request</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Shared frontend interactions -->
<script src="/vaccination_management_system/assets/js/hospital_update_status.js"></script>

<?php include '../includes/footer.php'; ?>
