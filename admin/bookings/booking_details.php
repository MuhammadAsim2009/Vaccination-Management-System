<?php
// Reusable includes
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Fetch statistics from vaccination_schedule
$stmt_stats = $conn->prepare("SELECT
        COUNT(*) AS total_bookings,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS total_pending,
        SUM(CASE WHEN status = 'vaccinated' THEN 1 ELSE 0 END) AS total_completed,
        SUM(CASE WHEN status = 'missed' THEN 1 ELSE 0 END) AS total_missed
    FROM
        vaccination_schedule
");
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();

// Fetch booking details from vaccination_schedule and related tables
$stmt_bookings = $conn->prepare("SELECT
        vs.id,
        u.name AS parent_name,
        c.name AS child_name,
        v.vaccine_name,
        vs.dose_number,
        h.hospital_name,
        vs.scheduled_date AS appointment_date,
        vs.status,
        vs.created_at
    FROM
        vaccination_schedule AS vs
    JOIN
        children AS c ON vs.child_id = c.id
    JOIN
        users AS u ON c.parent_id = u.id
    JOIN
        vaccines AS v ON vs.vaccine_id = v.id
    LEFT JOIN
        hospitals AS h ON vs.hospital_id = h.id
    ORDER BY
        vs.created_at DESC
");
$stmt_bookings->execute();
$bookings = $stmt_bookings->get_result();

// Fetch distinct hospital names for the filter
$hospitals_result = $conn->query("SELECT DISTINCT hospital_name FROM hospitals WHERE hospital_name IS NOT NULL ORDER BY hospital_name ASC");
$hospital_names = [];
while ($row = $hospitals_result->fetch_assoc()) {
    $hospital_names[] = $row['hospital_name'];
}

/**
 * Get status badge class based on booking status.
 */
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'Pending': return 'bg-warning text-dark';
        case 'vaccinated': return 'bg-success';
        case 'missed': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>

<div class="main-content p-4">
    <!-- Breadcrumb & Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6 text-start">
            <h3 class="fw-bold text-dark mb-1">Vaccination Bookings</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bookings</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="text-muted small">
                <i class="fas fa-calendar-alt me-1"></i> 
                Today's Date: <span class="fw-semibold"><?php echo date('F d, Y'); ?></span>
            </div>
        </div>
    </div>

    <!-- Stats Cards (Quick Overview) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
                            <i class="fas fa-book-medical fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-medium">Total Bookings</p>
                            <h4 class="fw-bold mb-0"><?php echo number_format($stats['total_bookings'] ?? 0); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 text-warning p-3 rounded-circle me-3">
                            <i class="fas fa-clock fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-medium">Pending</p>
                            <h4 class="fw-bold mb-0"><?php echo number_format($stats['total_pending'] ?? 0); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 text-success p-3 rounded-circle me-3">
                            <i class="fas fa-check-circle fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-medium">Completed</p>
                            <h4 class="fw-bold mb-0"><?php echo number_format($stats['total_completed'] ?? 0); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 text-danger p-3 rounded-circle me-3">
                            <i class="fas fa-times-circle fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-medium">Missed</p>
                            <h4 class="fw-bold mb-0"><?php echo number_format($stats['total_missed'] ?? 0); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Section -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <div class="row align-items-center g-3">
                <!-- Search Input -->
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-start-0" id="bookingSearch" placeholder="Search by ID or name...">
                    </div>
                </div>
                <!-- Status Filter -->
                <div class="col-md-3">
                    <select class="form-select bg-light" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="vaccinated">Completed</option>
                        <option value="missed">Missed</option>
                    </select>
                </div>
                <!-- Hospital Filter -->
                <div class="col-md-3">
                    <select class="form-select bg-light" id="hospitalFilter">
                        <option value="">All Hospitals</option>
                        <?php foreach ($hospital_names as $name): ?>
                        <option value="<?php echo htmlspecialchars($name); ?>"><?php echo htmlspecialchars($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Action Buttons (Reset) -->
                <div class="col-md-2 text-md-end">
                    <button type="button" class="btn btn-outline-secondary w-100" id="resetBtn">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="bookingsTable">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="ps-4 py-3">Booking ID</th>
                            <th>Parent Name</th>
                            <th>Child Name</th>
                            <th>Vaccine</th>
                            <th>Dose Number</th>
                            <th>Hospital</th>
                            <th>Appt. Date</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($bookings->num_rows > 0): ?>
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                        <?php
                            $display_status = ucfirst($booking['status']);
                            $badge_class = getStatusBadgeClass($booking['status']);
                            if (strtolower($booking['status']) === 'vaccinated') {
                                $display_status = 'Completed';
                            }
                        ?>
                        <tr class="booking-row" 
                            data-status="<?php echo strtolower($booking['status']); ?>" 
                            data-hospital="<?php echo htmlspecialchars($booking['hospital_name']); ?>">
                            <td class="ps-4 fw-medium text-primary">#<?php echo $booking['id']; ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo htmlspecialchars($booking['parent_name']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($booking['child_name']); ?></td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal">
                                    <i class="fas fa-syringe me-1 text-primary"></i>
                                    <?php echo htmlspecialchars($booking['vaccine_name']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Dose <?php echo $booking['dose_number']; ?></span>
                            </td>
                            <td>
                                <div class="small text-muted text-truncate" style="max-width: 150px;">
                                    <?php echo htmlspecialchars($booking['hospital_name']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium"><?php echo date('M d, Y', strtotime($booking['appointment_date'])); ?></div>
                            </td>
                            <td>
                                <span class="badge <?php echo $badge_class; ?> rounded-pill px-3 py-2">
                                    <?php echo $display_status; ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-info text-white rounded-3 px-3 shadow-none view-booking-btn" 
                                        title="View Details"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewBookingModal"
                                        data-id="#<?php echo $booking['id']; ?>"
                                        data-parent="<?php echo htmlspecialchars($booking['parent_name']); ?>"
                                        data-child="<?php echo htmlspecialchars($booking['child_name']); ?>"
                                        data-vaccine="<?php echo htmlspecialchars($booking['vaccine_name']); ?>"
                                        data-hospital="<?php echo htmlspecialchars($booking['hospital_name']); ?>"
                                        data-date="<?php echo date('M d, Y', strtotime($booking['appointment_date'])); ?>"
                                        data-status="<?php echo $display_status; ?>"
                                        data-created="<?php echo date('M d, Y', strtotime($booking['created_at'])); ?>"
                                        data-dose="<?php echo $booking['dose_number']; ?>">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No bookings found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        <div class="card-footer bg-white py-3 border-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-muted small mb-0 mb-md-0">Showing <?php echo $bookings->num_rows; ?> of <?php echo $stats['total_bookings'] ?? 0; ?> bookings</p>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
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
</div>

<!-- View Booking Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-calendar-check fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-1" id="modalBookingId"></h5>
                    <span class="badge rounded-pill px-3 py-2" id="modalStatus"></span>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Parent Name</label>
                        <div class="fw-medium" id="modalParent"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Child Name</label>
                        <div class="fw-medium" id="modalChild"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Vaccine</label>
                        <div class="fw-medium" id="modalVaccine"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Dose Number</label>
                        <div class="fw-medium" id="modalDose"></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Hospital</label>
                        <div class="fw-medium" id="modalHospital"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Appointment Date</label>
                        <div class="fw-medium" id="modalDate"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Created On</label>
                        <div class="fw-medium" id="modalCreated"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * UI Interactions for Bookings Page
 */

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('bookingSearch');
    const statusFilter = document.getElementById('statusFilter');
    const hospitalFilter = document.getElementById('hospitalFilter');
    const resetBtn = document.getElementById('resetBtn');
    const rows = document.querySelectorAll('.booking-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;
        const selectedHospital = hospitalFilter.value.toLowerCase();

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const status = row.getAttribute('data-status').toLowerCase();
            const hospital = row.getAttribute('data-hospital').toLowerCase();
            
            const matchesSearch = text.includes(searchTerm);
            const matchesStatus = selectedStatus === "" || status === selectedStatus;
            const matchesHospital = selectedHospital === "" || hospital === selectedHospital;

            if (matchesSearch && matchesStatus && matchesHospital) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Add event listeners
    searchInput.addEventListener('keyup', filterTable);
    statusFilter.addEventListener('change', filterTable);
    hospitalFilter.addEventListener('change', filterTable);

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            statusFilter.value = '';
            hospitalFilter.value = '';
            filterTable();
        });
    }

    // Modal Population
    const viewBtns = document.querySelectorAll('.view-booking-btn');
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalBookingId').textContent = this.getAttribute('data-id');
            document.getElementById('modalParent').textContent = this.getAttribute('data-parent');
            document.getElementById('modalChild').textContent = this.getAttribute('data-child');
            document.getElementById('modalVaccine').textContent = this.getAttribute('data-vaccine');
            document.getElementById('modalDose').textContent = 'Dose ' + this.getAttribute('data-dose');
            document.getElementById('modalHospital').textContent = this.getAttribute('data-hospital');
            document.getElementById('modalDate').textContent = this.getAttribute('data-date');
            document.getElementById('modalCreated').textContent = this.getAttribute('data-created');
            
            const status = this.getAttribute('data-status');
            const statusBadge = document.getElementById('modalStatus');
            statusBadge.textContent = status;
            
            // Reset classes
            statusBadge.className = 'badge rounded-pill px-3 py-2';
            
            if(status === 'Completed') statusBadge.classList.add('bg-success');
            else if(status === 'Missed') statusBadge.classList.add('bg-danger');
            else if(status === 'Pending') statusBadge.classList.add('bg-warning', 'text-dark');
            else statusBadge.classList.add('bg-secondary');
        });
    });
});

</script>

<?php include '../includes/footer.php'; ?>