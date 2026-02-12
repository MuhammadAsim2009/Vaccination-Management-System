<?php
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<!-- Main Content -->
<main class="mt-5 pt-3">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item">Vaccination</li>
                        <li class="breadcrumb-item active" aria-current="page">Appointments</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary">Vaccination Appointments</h2>
                <p class="text-muted">Manage all booked vaccination appointments</p>
            </div>
        </div>

        <!-- Stats Summary Cards (Matching Dashboard Style) -->
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

            <!-- Today's Appointments -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Today's Appointments</h6>
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-calendar-day fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2">45</h2>
                        <div class="d-flex align-items-center text-success small fw-medium">
                            <i class="fas fa-arrow-up me-1"></i>
                            <span>5% from yesterday</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Appointments -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Completed</h6>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-check-circle fs-4"></i>
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
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Pending</h6>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-clock fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2">344</h2>
                         <div class="d-flex align-items-center text-danger small fw-medium">
                            <i class="fas fa-arrow-down me-1"></i>
                            <span>2% from last week</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search Section -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body">
                <form class="row g-3 align-items-end" id="filterForm">
                    <div class="col-md-4">
                        <label for="searchInput" class="form-label small fw-bold text-muted">Search Parent/Child</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" id="searchInput" placeholder="Type name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="dateInput" class="form-label small fw-bold text-muted">Filter by Date</label>
                        <input type="date" class="form-control bg-light" id="dateInput">
                    </div>
                    <div class="col-md-3">
                        <label for="statusInput" class="form-label small fw-bold text-muted">Status</label>
                        <select class="form-select bg-light" id="statusInput">
                            <option value="all" selected>All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary w-100 fw-bold" id="resetBtn"><i class="fas fa-undo me-2"></i>Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Appointment List Table (Matching Dashboard Style) -->
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-list-ul me-2 text-primary"></i>Appointment List</h5>
                <button class="btn btn-sm btn-outline-success rounded-pill"><i class="fas fa-file-export me-1"></i> Export CSV</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="appointmentTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Appt ID</th>
                                <th>Parent Name</th>
                                <th>Child Name</th>
                                <th>Vaccine</th>
                                <th>Date & Time</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="appointmentTableBody">
                            <!-- Row 1: Pending -->
                            <tr data-date="2023-10-25" data-status="pending">
                                <td class="ps-4 fw-bold text-primary">#APT-001</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold parent-name">Linda Hamilton</div>
                                            <div class="small text-muted">linda@example.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="child-name">Sarah Connor</td>
                                <td>Polio (OPV) - Dose 1</td>
                                <td>
                                    <div class="fw-bold">Oct 25, 2023</div>
                                    <div class="small text-muted">10:00 AM - 10:30 AM</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-warning text-warning rounded-pill px-3 status-badge">Pending</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a class="btn btn-sm btn-outline-primary me-2" href="#" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-primary" href='update_status.php' title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Row 2: Completed -->
                            <tr data-date="2023-10-24" data-status="completed">
                                <td class="ps-4 fw-bold text-primary">#APT-002</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold parent-name">Helen Wick</div>
                                            <div class="small text-muted">helen@example.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="child-name">John Wick</td>
                                <td>BCG - Dose 1</td>
                                <td>
                                    <div class="fw-bold">Oct 24, 2023</div>
                                    <div class="small text-muted">02:30 PM - 03:00 PM</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-success text-success rounded-pill px-3 status-badge">Completed</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a class="btn btn-sm btn-outline-primary me-2" href="#" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-primary" href='update_status.php' title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Row 3: Cancelled -->
                            <tr data-date="2023-10-26" data-status="cancelled">
                                <td class="ps-4 fw-bold text-primary">#APT-003</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold parent-name">Martha Wayne</div>
                                            <div class="small text-muted">martha@example.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="child-name">Bruce Wayne</td>
                                <td>MMR - Dose 1</td>
                                <td>
                                    <div class="fw-bold">Oct 26, 2023</div>
                                    <div class="small text-muted">09:15 AM - 09:45 AM</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-danger text-danger rounded-pill px-3 status-badge">Cancelled</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a class="btn btn-sm btn-outline-primary me-2" href="#" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-primary" href='update_status.php' title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                             <!-- Row 4: Pending -->
                             <tr data-date="2023-10-27" data-status="pending">
                                <td class="ps-4 fw-bold text-primary">#APT-004</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold parent-name">Martha Kent</div>
                                            <div class="small text-muted">kent@example.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="child-name">Clark Kent</td>
                                <td>Hepatitis B - Dose 2</td>
                                <td>
                                    <div class="fw-bold">Oct 27, 2023</div>
                                    <div class="small text-muted">11:00 AM - 11:30 AM</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-warning text-warning rounded-pill px-3 status-badge">Pending</span>
                                </td>
                                                                <td class="text-end pe-4">
                                    <a class="btn btn-sm btn-outline-primary me-2" href="#" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-primary" href='vaccination/update_status.php' title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                             <!-- Row 5: Pending -->
                             <tr data-date="2023-10-28" data-status="pending">
                                <td class="ps-4 fw-bold text-primary">#APT-005</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold parent-name">Hippolyta</div>
                                            <div class="small text-muted">queen@example.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="child-name">Diana Prince</td>
                                <td>Rotavirus - Dose 1</td>
                                <td>
                                    <div class="fw-bold">Oct 28, 2023</div>
                                    <div class="small text-muted">03:45 PM - 04:15 PM</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-warning text-warning rounded-pill px-3 status-badge">Pending</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a class="btn btn-sm btn-outline-primary me-2" href="#" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-primary" href='update_status.php' title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
             <!-- Pagination -->
             <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <div class="small text-muted">Showing <span class="fw-bold">1</span> to <span class="fw-bold">5</span> of <span class="fw-bold">12</span> entries</div>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item disabled">
                            <a class="page-link border-0" href="#" tabindex="-1"><i class="fas fa-chevron-left"></i></a>
                        </li>
                        <li class="page-item active"><a class="page-link border-0 rounded-circle bg-primary text-white mx-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" href="#">1</a></li>
                        <li class="page-item"><a class="page-link border-0 rounded-circle text-muted mx-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" href="#">2</a></li>
                        <li class="page-item"><a class="page-link border-0 rounded-circle text-muted mx-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link border-0" href="#"><i class="fas fa-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</main>

<!-- Appointment Detail Modal -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1" aria-labelledby="viewAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="viewAppointmentModalLabel">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-lg bg-soft-primary text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-calendar-check fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Confimation #APT-2024-001</h5>
                    <span class="badge bg-soft-warning text-warning rounded-pill px-3">Pending</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Parent Name</label>
                        <div class="fw-medium">Linda Hamilton</div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Child Name</label>
                        <div class="fw-medium">Sarah Connor</div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Vaccine</label>
                        <div class="fw-medium">Polio (OPV)</div>
                    </div>
                     <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Dose Application</label>
                        <div class="fw-medium">Dose 1 (Oral)</div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Date</label>
                        <div class="fw-medium">Oct 25, 2023</div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Time Slot</label>
                        <div class="fw-medium">10:00 AM - 10:30 AM</div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Hospital</label>
                        <div class="fw-medium">City General Hospital</div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Notes</label>
                        <div class="alert alert-light border small text-muted mb-0">
                            Parent requested morning slot. Child has mild allergy to dust.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-between p-4 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#cancelAppointmentModal">Cancel Appointment</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#markCompletedModal">Mark Completed</button>
            </div>
        </div>
    </div>
</div>

<!-- Mark Completed Modal -->
<div class="modal fade" id="markCompletedModal" tabindex="-1" aria-labelledby="markCompletedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 text-center p-3">
            <div class="modal-body">
                <div class="avatar-md bg-soft-success text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-check fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Mark as Completed?</h5>
                <p class="text-muted small mb-4">Are you sure you want to mark this appointment as completed?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-success rounded-pill px-4">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Appointment Modal -->
<div class="modal fade" id="cancelAppointmentModal" tabindex="-1" aria-labelledby="cancelAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 text-center p-3">
            <div class="modal-body">
                <div class="avatar-md bg-soft-danger text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-exclamation-triangle fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Cancel Appointment?</h5>
                <p class="text-muted small mb-4">Are you sure you want to cancel this appointment? This action cannot be undone.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4">Yes, Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const dateInput = document.getElementById('dateInput');
    const statusInput = document.getElementById('statusInput');
    const resetBtn = document.getElementById('resetBtn');
    const tableBody = document.getElementById('appointmentTableBody');
    const rows = tableBody.getElementsByTagName('tr');

    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const searchDate = dateInput.value;
        const searchStatus = statusInput.value.toLowerCase();

        for (let row of rows) {
            const parentName = row.querySelector('.parent-name').textContent.toLowerCase();
            const childName = row.querySelector('.child-name').textContent.toLowerCase();
            const rowDate = row.getAttribute('data-date');
            const rowStatus = row.getAttribute('data-status');

            // Search Filter
            const matchesSearch = parentName.includes(searchText) || childName.includes(searchText);

            // Date Filter
            const matchesDate = !searchDate || rowDate === searchDate;

            // Status Filter
            const matchesStatus = searchStatus === 'all' || rowStatus === searchStatus;

            if (matchesSearch && matchesDate && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    // Event Listeners
    searchInput.addEventListener('input', filterTable);
    dateInput.addEventListener('input', filterTable);
    statusInput.addEventListener('change', filterTable);

    resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        dateInput.value = '';
        statusInput.value = 'all';
        filterTable();
    });
});
</script>

<?php include '../includes/footer.php'; ?>
