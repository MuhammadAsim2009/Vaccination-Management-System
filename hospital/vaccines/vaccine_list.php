<?php
// Reuseable includes
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../../config/functions.php';

// Fetch data from datebase
$stmt_vaccine = $conn->prepare("SELECT * FROM vaccines");
$stmt_vaccine->execute();
$result_vaccine = $stmt_vaccine->get_result();

$vaccines = [];
if ($result_vaccine->num_rows > 0) {
    while ($row = $result_vaccine->fetch_assoc()) {
        $vaccines[] = [
            'id' => $row['id'],
            'name' => $row['vaccine_name'],
            'age_group' => $row['target_age_group'],
            'doses' => $row['total_dose'] ?? 1,
            'status' => ucfirst($row['availability_status']),
            'description' => $row['description'],
            'created_at' => date('M d, Y', strtotime($row['created_at']))
        ];
    }
}

// Calculate Stats
$total_vaccines = count($vaccines);
$available_vaccines = count(array_filter($vaccines, fn($v) => $v['status'] === 'Available'));
$unavailable_vaccines = $total_vaccines - $available_vaccines;

// Delete Vaccine
$delete_msg = '';
$delete_type = '';

if(isset($_POST['delete_btn'])) {
    $vaccine_id = $_POST['vaccine_id'];
    
    // Fetch vaccine name for notification before deletion
    $stmt_name = $conn->prepare("SELECT vaccine_name FROM vaccines WHERE id = ?");
    $stmt_name->bind_param("i", $vaccine_id);
    $stmt_name->execute();
    $res_name = $stmt_name->get_result();
    $vaccine_name = ($res_name->num_rows > 0) ? $res_name->fetch_assoc()['vaccine_name'] : 'Unknown Vaccine';
    $stmt_name->close();

    $stmt_delete = $conn->prepare("DELETE FROM vaccines WHERE id = ?");
    $stmt_delete->bind_param("i", $vaccine_id);
    if($stmt_delete->execute()) {
        // Trigger Notification to Admin
        $user_id = $_SESSION['user_id'];
        $hospital_name = $_SESSION['name'];
        $notif_title = "Vaccine Deleted";
        $notif_message = "Hospital '$hospital_name' deleted vaccine: " . htmlspecialchars($vaccine_name) . ".";
        send_notification($conn, 'admin', null, $user_id, 'vaccination', $notif_title, $notif_message);

        $delete_msg = "Vaccine deleted successfully.";
        $delete_type = "success";
    } else {
        $delete_msg = "Error deleting vaccine: " . $conn->error;
        $delete_type = "danger";
    }
}

?>

<!-- Main Content -->
<main class="mt-5 pt-3">
    <div class="container-fluid">

        <!-- Alert Placeholder -->
        <div id="alertPlaceholder"></div>

        <!-- 1. Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item">Vaccines</li>
                        <li class="breadcrumb-item active" aria-current="page">Vaccine List</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h2 class="fw-bold text-primary mb-0">Vaccine List</h2>
                        <p class="text-muted mb-0">Manage hospital vaccine inventory and availability.</p>
                    </div>
                    <a href="add_vaccine.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-plus-circle me-2"></i>Add New Vaccine
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Summary Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Vaccines -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Total Vaccines</h6>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-vials fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= $total_vaccines ?></h2>
                        <div class="d-flex align-items-center text-muted small fw-medium">
                            <i class="fas fa-cubes me-1"></i>
                            <span>Total types in inventory</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Vaccines -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Available</h6>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-check-circle fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= $available_vaccines ?></h2>
                        <div class="d-flex align-items-center text-success small fw-medium">
                            <i class="fas fa-check me-1"></i>
                            <span>Ready for appointments</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unavailable Vaccines -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Unavailable</h6>
                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-exclamation-triangle fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= $unavailable_vaccines ?></h2>
                        <div class="d-flex align-items-center text-danger small fw-medium">
                            <i class="fas fa-times-circle me-1"></i>
                            <span>Needs restocking</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Search & Filter -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="searchInput" class="form-label fw-bold small text-muted">Search Vaccine</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" id="searchInput" placeholder="Search by id, name, or age group...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="statusFilter" class="form-label fw-bold small text-muted">Filter by Status</label>
                        <select class="form-select bg-light" id="statusFilter">
                            <option value="all" selected>All Statuses</option>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100 fw-bold" id="resetBtn">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Vaccine List Table -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Inventory List</h5>
                <button class="btn btn-sm btn-outline-success rounded-pill"><i class="fas fa-file-export me-1"></i> Export CSV</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="vaccineTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Vaccine Name</th>
                                <th>Age Group</th>
                                <th class="text-center">Doses</th>
                                <th class="text-center">Status</th>
                                <th>Created At</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($vaccines as $vaccine): ?>
                            <tr data-status="<?= $vaccine['status'] ?>">
                                <td class="ps-4 fw-bold text-muted">#<?= $vaccine['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-syringe small"></i>
                                        </div>
                                        <span class="fw-semibold vaccine-name"><?= $vaccine['name'] ?></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= $vaccine['age_group'] ?></span></td>
                                <td class="text-center fw-bold"><?= $vaccine['doses'] ?></td>
                                <td class="text-center">
                                    <?php if($vaccine['status'] === 'Available'): ?>
                                        <span class="badge bg-soft-success text-success rounded-pill px-3">Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-soft-danger text-danger rounded-pill px-3">Unavailable</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small vaccine-created_at"><?= $vaccine['created_at'] ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewVaccineModal"
                                            onclick="populateViewModal(<?= htmlspecialchars(json_encode($vaccine)) ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="update_vaccine.php?id=<?= $vaccine['id'] ?>" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger"  data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="document.getElementById('DeleteVaccineId').value = <?= $vaccine['id'] ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- 5. Pagination -->
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <div class="small text-muted">Showing <span class="fw-bold">1</span> to <span class="fw-bold"><?= count($vaccines) ?></span> of <span class="fw-bold"><?= count($vaccines) ?></span> entries</div>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item disabled"><a class="page-link border-0" href="#"><i class="fas fa-chevron-left"></i></a></li>
                        <li class="page-item active"><a class="page-link border-0 rounded-circle bg-primary text-white mx-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" href="#">1</a></li>
                        <li class="page-item"><a class="page-link border-0 rounded-circle text-muted mx-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" href="#">2</a></li>
                        <li class="page-item"><a class="page-link border-0" href="#"><i class="fas fa-chevron-right"></i></a></li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</main>

<!-- 6. View Vaccine Modal -->
<div class="modal fade" id="viewVaccineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Vaccine Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="text-center mb-4">
                    <div class="avatar-lg bg-soft-primary text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-vial fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-1" id="modalVaccineName">Vaccine Name</h4>
                    <span class="badge bg-light text-muted border" id="modalVaccineId">Vaccine Id</span>
                </div>
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted text-uppercase fw-bold">Age Group</small><div class="fw-medium" id="modalAgeGroup">-</div></div>
                    <div class="col-6"><small class="text-muted text-uppercase fw-bold">Doses Required</small><div class="fw-medium" id="modalDoses">-</div></div>
                    <div class="col-6"><small class="text-muted text-uppercase fw-bold">Current Status</small><div class="fw-medium" id="modalStatus">-</div></div>
                    <div class="col-6"><small class="text-muted text-uppercase fw-bold">Created At</small><div class="fw-medium" id="modalCreatedAt">-</div></div>
                    <div class="col-12"><small class="text-muted text-uppercase fw-bold">Description</small><p class="small text-muted mb-0" id="modalDescription">-</p></div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 6. Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 text-center p-3">
            <div class="modal-body">
                <div class="avatar-md bg-soft-danger text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-trash-alt fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Delete Vaccine?</h5>
                <p class="text-muted small mb-4">Are you sure you want to delete this vaccine from the inventory? This action cannot be undone.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <form method="post">
                        <input type="hidden" name="vaccine_id" id="DeleteVaccineId">
                        <button type="submit" name="delete_btn" class="btn btn-danger rounded-pill px-4">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Search & Filter Logic
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const resetBtn = document.getElementById('resetBtn');
        const tableRows = document.querySelectorAll('#vaccineTable tbody tr');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;

            tableRows.forEach(row => {
                const name = row.querySelector('.vaccine-name').textContent.toLowerCase();
                const id = row.cells[0].textContent.toLowerCase();
                const age = row.cells[2].textContent.toLowerCase();
                const status = row.getAttribute('data-status');

                const matchesSearch = name.includes(searchTerm) || id.includes(searchTerm) || age.includes(searchTerm);
                const matchesStatus = statusValue === 'all' || status === statusValue;

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('keyup', filterTable);
        statusFilter.addEventListener('change', filterTable);

        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            statusFilter.value = 'all';
            filterTable();
        });
    });

    // Populate View Modal
    function populateViewModal(vaccine) {
        document.getElementById('modalVaccineName').textContent = vaccine.name;
        document.getElementById('modalVaccineId').textContent = '#' + vaccine.id;
        document.getElementById('modalAgeGroup').textContent = vaccine.age_group;
        document.getElementById('modalDoses').textContent = vaccine.doses;
        document.getElementById('modalStatus').textContent = vaccine.status;
        document.getElementById('modalCreatedAt').textContent = vaccine.created_at;
        document.getElementById('modalDescription').textContent = vaccine.description;
        
        // Color code status in modal
        const statusEl = document.getElementById('modalStatus');
        if(vaccine.status === 'Available') {
            statusEl.className = 'fw-bold text-success';
        } else {
            statusEl.className = 'fw-bold text-danger';
        }
    }

    // Alert Logic
    const alertMsg = <?= json_encode($delete_msg) ?>;
    const alertType = <?= json_encode($delete_type) ?>;
    
    if(alertMsg) {
        showAlert(alertMsg, alertType);
    }

    function showAlert(message, type) {
        const placeholder = document.getElementById('alertPlaceholder');
        if(!placeholder) return;
        
        const wrapper = document.createElement('div');
        wrapper.innerHTML = [
            `<div class="alert alert-${type} alert-dismissible fade show border-0 shadow-sm rounded-3 py-3" role="alert">`,
            `   <div class="d-flex align-items-center">`,
            `       <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} fs-4 me-3"></i>`,
            `       <div>${message}</div>`,
            `   </div>`,
            '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
            '</div>'
        ].join('');
        
        placeholder.append(wrapper);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            if (wrapper.parentNode) {
                wrapper.remove();
            }
        }, 5000);
    }
</script>

<?php include '../includes/footer.php'; ?>