<?php
/**
 * Vaccination Management System - Admin Module
 * hospital_list.php: Manage and view all registered hospitals.
 * 
 * Tech Stack: PHP, HTML5, CSS3, Bootstrap 5, JavaScript, Font Awesome.
 * Design: SaaS Admin UI.
 */

// Reusable includes
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Dummy data for hospitals
$hospitals = [
    [
        'id' => 'HSP-101',
        'name' => 'City General Hospital',
        'reg_no' => 'REG-2023-001',
        'email' => 'contact@citygeneral.com',
        'phone' => '+1 (555) 123-4567',
        'address' => '123 Health Ave, Medical District',
        'status' => 'Accepted'
    ],
    [
        'id' => 'HSP-102',
        'name' => 'St. Mary\'s Pediatric Clinic',
        'reg_no' => 'REG-2023-002',
        'email' => 'info@stmarys.org',
        'phone' => '+1 (555) 987-6543',
        'address' => '456 Care Lane, Downtown',
        'status' => 'Pending'
    ],
    [
        'id' => 'HSP-103',
        'name' => 'Community Wellness Center',
        'reg_no' => 'REG-2022-045',
        'email' => 'wellness@community.org',
        'phone' => '+1 (555) 456-7890',
        'address' => '789 Hope St, Suburban Area',
        'status' => 'Rejected'
    ],
    [
        'id' => 'HSP-104',
        'name' => 'Metro Children\'s Hospital',
        'reg_no' => 'REG-2023-089',
        'email' => 'admin@metrochildrens.com',
        'phone' => '+1 (555) 222-3333',
        'address' => '321 Future Way, City Center',
        'status' => 'Accepted'
    ],
    [
        'id' => 'HSP-105',
        'name' => 'Sunrise Medical Institute',
        'reg_no' => 'REG-2021-112',
        'email' => 'hello@sunrise-med.com',
        'phone' => '+1 (555) 555-0199',
        'address' => '555 Morning Dr, East Side',
        'status' => 'Pending'
    ]
];

/**
 * Helper to get status badge class
 */
function getStatusBadge($status) {
    switch ($status) {
        case 'Accepted': return 'bg-success';
        case 'Rejected': return 'bg-danger';
        case 'Pending': return 'bg-warning text-dark';
        default: return 'bg-secondary';
    }
}
?>

<div class="main-content p-4">
    <!-- Breadcrumb & Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Hospital Management</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hospitals</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="add_hospital.php" class="btn btn-primary shadow-sm rounded-3">
                <i class="fas fa-plus me-1"></i> Add New Hospital
            </a>
        </div>
    </div>

    <!-- Main Card Content -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <div class="row align-items-center g-3">
                <!-- Search Input Area -->
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="hospitalSearch" class="form-control bg-light border-start-0" placeholder="Search hospitals by name, ID, or email...">
                    </div>
                </div>
                <!-- Status Filter -->
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select bg-light">
                        <option value="">All Statuses</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <!-- Record Count Info -->
                <div class="col text-md-end">
                    <span class="text-muted small">Total Hospitals: <span class="fw-bold text-primary"><?php echo count($hospitals); ?></span></span>
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="hospitalsTable">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="ps-4">Hospital ID</th>
                            <th>Hospital Name</th>
                            <th>Reg. Number</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hospitals as $hosp): ?>
                        <tr class="hospital-row" 
                            data-name="<?php echo strtolower($hosp['name']); ?>" 
                            data-id="<?php echo strtolower($hosp['id']); ?>" 
                            data-email="<?php echo strtolower($hosp['email']); ?>"
                            data-status="<?php echo $hosp['status']; ?>">
                            <td class="ps-4">
                                <span class="fw-medium text-primary"><?php echo $hosp['id']; ?></span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?php echo $hosp['name']; ?></div>
                                <div class="small text-muted text-truncate" style="max-width: 200px;" title="<?php echo $hosp['address']; ?>">
                                    <i class="fas fa-map-marker-alt me-1 opacity-50"></i><?php echo $hosp['address']; ?>
                                </div>
                            </td>
                            <td class="small"><?php echo $hosp['reg_no']; ?></td>
                            <td class="small"><?php echo $hosp['email']; ?></td>
                            <td class="small text-nowrap"><?php echo $hosp['phone']; ?></td>
                            <td>
                                <span class="badge <?php echo getStatusBadge($hosp['status']); ?> rounded-pill px-3 py-2">
                                    <?php echo $hosp['status']; ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="update_hospital.php?id=<?php echo $hosp['id']; ?>" class="btn btn-sm btn-outline-primary rounded-3" title="Edit Hospital" data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3" title="Delete Hospital" data-bs-toggle="tooltip" onclick="confirmDelete('<?php echo $hosp['id']; ?>', '<?php echo $hosp['name']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer / Pagination (Static Mockup) -->
        <div class="card-footer bg-white py-3 border-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-muted small mb-3 mb-md-0">Showing 1 to <?php echo count($hospitals); ?> of <?php echo count($hospitals); ?> records</p>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3 text-danger">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <h4 class="fw-bold">Delete Hospital?</h4>
                <p class="text-muted">Are you sure you want to delete <span id="deleteHospitalName" class="fw-bold text-dark"></span>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                <form action="delete_hospital.php" method="POST" id="deleteForm">
                    <input type="hidden" name="hospital_id" id="deleteHospitalId">
                    <button type="submit" class="btn btn-danger px-4 rounded-3">Delete Now</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * UI Interactions for Hospital List
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Search and Filter Logic
    const searchInput = document.getElementById('hospitalSearch');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.hospital-row');

    function performFilter() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;

        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const id = row.getAttribute('data-id');
            const email = row.getAttribute('data-email');
            const status = row.getAttribute('data-status');

            const matchesSearch = name.includes(searchTerm) || id.includes(searchTerm) || email.includes(searchTerm);
            const matchesStatus = selectedStatus === "" || status === selectedStatus;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', performFilter);
    statusFilter.addEventListener('change', performFilter);
});

/**
 * Handle delete confirmation modal trigger
 */
function confirmDelete(id, name) {
    document.getElementById('deleteHospitalId').value = id;
    document.getElementById('deleteHospitalName').textContent = name;
    
    // Using Bootstrap Modal API
    var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    myModal.show();
}
</script>

<?php include '../includes/footer.php'; ?>