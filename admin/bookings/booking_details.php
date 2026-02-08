<?php
// Reusable includes
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

/**
 * Vaccination Management System - Admin Bookings Module
 * booking_details.php: View and monitor all vaccination bookings.
 * 
 * Tech Stack: PHP, HTML5, CSS3, Bootstrap 5, JavaScript, Font Awesome.
 * Design: SaaS Admin UI, Medical/Healthcare Theme.
 */

// Dummy data for bookings
$bookings = [
    [
        'id' => 'BKN-001',
        'parent_name' => 'Michael Scott',
        'child_name' => 'Holly Scott',
        'vaccine_name' => 'BCG',
        'hospital_name' => 'City General Hospital',
        'appointment_date' => '2026-02-10',
        'status' => 'Pending',
        'created_at' => '2026-02-01'
    ],
    [
        'id' => 'BKN-002',
        'parent_name' => 'Jim Halpert',
        'child_name' => 'Cecilia Halpert',
        'vaccine_name' => 'Hepatitis B',
        'hospital_name' => 'St. Marys Pediatric Clinic',
        'appointment_date' => '2026-02-08',
        'status' => 'Approved',
        'created_at' => '2026-02-02'
    ],
    [
        'id' => 'BKN-003',
        'parent_name' => 'Pam Beesly',
        'child_name' => 'Philip Halpert',
        'vaccine_name' => 'OPV (Polio)',
        'hospital_name' => 'City General Hospital',
        'appointment_date' => '2026-01-25',
        'status' => 'Completed',
        'created_at' => '2026-01-15'
    ],
    [
        'id' => 'BKN-004',
        'parent_name' => 'Dwight Schrute',
        'child_name' => 'Mose Schrute Jr.',
        'vaccine_name' => 'DPT',
        'hospital_name' => 'Schrute Farms Medical',
        'appointment_date' => '2026-02-15',
        'status' => 'Cancelled',
        'created_at' => '2026-02-04'
    ],
    [
        'id' => 'BKN-005',
        'parent_name' => 'Angela Martin',
        'child_name' => 'Philip Streatley',
        'vaccine_name' => 'Measles',
        'hospital_name' => 'St. Marys Pediatric Clinic',
        'appointment_date' => '2026-02-12',
        'status' => 'Pending',
        'created_at' => '2026-02-03'
    ]
];

/**
 * Get status badge class based on booking status.
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'Pending': return 'bg-warning text-dark';
        case 'Approved': return 'bg-primary';
        case 'Completed': return 'bg-success';
        case 'Cancelled': return 'bg-danger';
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
                            <h4 class="fw-bold mb-0">1,245</h4>
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
                            <h4 class="fw-bold mb-0">84</h4>
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
                            <h4 class="fw-bold mb-0">1,102</h4>
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
                            <p class="text-muted mb-0 small fw-medium">Cancelled</p>
                            <h4 class="fw-bold mb-0">59</h4>
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
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <!-- Hospital Filter -->
                <div class="col-md-3">
                    <select class="form-select bg-light" id="hospitalFilter">
                        <option value="">All Hospitals</option>
                        <option value="City General Hospital">City General Hospital</option>
                        <option value="St. Marys Pediatric Clinic">St. Marys Pediatric Clinic</option>
                        <option value="Schrute Farms Medical">Schrute Farms Medical</option>
                    </select>
                </div>
                <!-- Action Buttons (Reset) -->
                <div class="col-md-2 text-md-end">
                    <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
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
                            <th>Hospital</th>
                            <th>Appt. Date</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th class="text-center pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr class="booking-row" 
                            data-status="<?php echo $booking['status']; ?>" 
                            data-hospital="<?php echo $booking['hospital_name']; ?>">
                            <td class="ps-4 fw-medium text-primary"><?php echo $booking['id']; ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo $booking['parent_name']; ?></div>
                            </td>
                            <td><?php echo $booking['child_name']; ?></td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal">
                                    <i class="fas fa-syringe me-1 text-primary"></i>
                                    <?php echo $booking['vaccine_name']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="small text-muted text-truncate" style="max-width: 150px;">
                                    <?php echo $booking['hospital_name']; ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium"><?php echo date('M d, Y', strtotime($booking['appointment_date'])); ?></div>
                            </td>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($booking['status']); ?> rounded-pill px-3 py-2">
                                    <?php echo $booking['status']; ?>
                                </span>
                            </td>
                            <td class="small text-muted">
                                <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-info text-white rounded-3 px-3 shadow-none" title="View Details">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination UI (Static) -->
        <div class="card-footer bg-white py-3 border-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-muted small mb-0 mb-md-0">Showing 1 to 5 of 1,245 bookings</p>
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

<script>
/**
 * UI Interactions for Bookings Page
 */

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('bookingSearch');
    const statusFilter = document.getElementById('statusFilter');
    const hospitalFilter = document.getElementById('hospitalFilter');
    const rows = document.querySelectorAll('.booking-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;
        const selectedHospital = hospitalFilter.value;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const status = row.getAttribute('data-status');
            const hospital = row.getAttribute('data-hospital');
            
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
});

function resetFilters() {
    document.getElementById('bookingSearch').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('hospitalFilter').value = '';
    
    const rows = document.querySelectorAll('.booking-row');
    rows.forEach(row => row.style.display = '');
}
</script>

<?php include '../includes/footer.php'; ?>