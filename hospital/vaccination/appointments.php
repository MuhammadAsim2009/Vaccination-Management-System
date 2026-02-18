<?php
// Required Includes
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';


// Fetch Hospital ID
$user_id = $_SESSION['user_id'];
$hospital_id = 0;
$stmt = $conn->prepare("SELECT id FROM hospitals WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $hospital_id = $res->fetch_assoc()['id'];
}

// Fetch Statistics
$stats = ['total' => 0, 'today' => 0, 'completed' => 0, 'pending' => 0];
if ($hospital_id) {
    $sql_stats = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN DATE(scheduled_date) = CURDATE() THEN 1 ELSE 0 END) as today,
                    SUM(CASE WHEN status = 'vaccinated' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                  FROM vaccination_schedule 
                  WHERE hospital_id = ?";
    $stmt_stats = $conn->prepare($sql_stats);
    $stmt_stats->bind_param("i", $hospital_id);
    $stmt_stats->execute();
    $stats_res = $stmt_stats->get_result();
    if ($stats_res->num_rows > 0) {
        $stats = $stats_res->fetch_assoc();
    }

    // Fetch Appointments (Schedule)
    $sql_list = "SELECT vs.id, vs.scheduled_date, vs.status, vs.dose_number,
                        c.name as child_name, u.name as parent_name, u.email as parent_email,
                        v.vaccine_name
                 FROM vaccination_schedule vs
                 JOIN children c ON vs.child_id = c.id
                 JOIN users u ON c.parent_id = u.id
                 JOIN vaccines v ON vs.vaccine_id = v.id
                 WHERE vs.hospital_id = ?
                 ORDER BY vs.created_at DESC";
    $stmt_list = $conn->prepare($sql_list);
    $stmt_list->bind_param("i", $hospital_id);
    $stmt_list->execute();
    $result_list = $stmt_list->get_result();
}

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

        <!-- Stats Summary Cards -->
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
                        <h2 class="fw-bold mb-2"><?= number_format($stats['total']) ?></h2>
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
                        <h2 class="fw-bold mb-2"><?= number_format($stats['today']) ?></h2>
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
                        <h2 class="fw-bold mb-2"><?= number_format($stats['completed']) ?></h2>
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
                        <h2 class="fw-bold mb-2"><?= number_format($stats['pending']) ?></h2>
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
                            <option value="vaccinated">Vaccinated</option>
                            <option value="missed">Missed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary w-100 fw-bold" id="resetBtn"><i class="fas fa-undo me-2"></i>Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Appointment List Table -->
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
                            <?php if (isset($result_list) && $result_list->num_rows > 0): ?>
                            <?php while ($row = $result_list->fetch_assoc()): 
                                $status_class = 'bg-soft-warning text-warning';
                                $status_display = ucfirst($row['status']);
                                if ($row['status'] == 'vaccinated') {
                                    $status_class = 'bg-soft-success text-success';
                                    $status_display = 'Vaccinated';
                                } elseif ($row['status'] == 'missed') {
                                    $status_class = 'bg-soft-danger text-danger';
                                }
                                
                                $date_obj = new DateTime($row['scheduled_date']);
                                $formatted_date = $date_obj->format('M d, Y');
                                $formatted_time = $date_obj->format('h:i A');
                                $filter_date = $date_obj->format('Y-m-d');
                            ?>
                            <tr data-date="<?= $filter_date ?>" data-status="<?= strtolower($status_display) ?>">
                                <td class="ps-4 fw-bold text-primary">#<?= $row['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold parent-name"><?= htmlspecialchars($row['parent_name']) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($row['parent_email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="child-name"><?= htmlspecialchars($row['child_name']) ?></td>
                                <td><?= htmlspecialchars($row['vaccine_name']) ?> <?= isset($row['dose_number']) ? '- Dose ' . $row['dose_number'] : '' ?></td>
                                <td>
                                    <div class="fw-bold"><?= $formatted_date ?></div>
                                    <div class="small text-muted"><?= $formatted_time ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $status_class ?> rounded-pill px-3 status-badge"><?= $status_display ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary me-2 view-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewAppointmentModal" 
                                            title="View Details"
                                            data-id="<?= $row['id'] ?>"
                                            data-parent="<?= htmlspecialchars($row['parent_name']) ?>"
                                            data-child="<?= htmlspecialchars($row['child_name']) ?>"
                                            data-vaccine="<?= htmlspecialchars($row['vaccine_name']) ?>"
                                            data-dose="<?= isset($row['dose_number']) ? $row['dose_number'] : '' ?>"
                                            data-date="<?= $formatted_date ?>"
                                            data-time="<?= $formatted_time ?>"
                                            data-status="<?= $status_display ?>"
                                            data-status-class="<?= $status_class ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a class="btn btn-sm btn-primary" href='update_status.php?id=<?= $row['id'] ?>' title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">No scheduled vaccinations found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
             <!-- Pagination -->
             <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <div class="small text-muted">Showing all records</div>
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
                    <h5 class="fw-bold mb-1" id="modalApptId">Confirmation #SCH-000</h5>
                    <span class="badge bg-soft-warning text-warning rounded-pill px-3" id="modalStatus">Pending</span>
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
                        <label class="small text-muted fw-bold text-uppercase">Dose Application</label>
                        <div class="fw-medium" id="modalDose"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Date</label>
                        <div class="fw-medium" id="modalDate"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Time Slot</label>
                        <div class="fw-medium" id="modalTime"></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Hospital</label>
                        <div class="fw-medium"><?= $_SESSION['name'] ?></div>
                    </div>
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

    // Modal Population
    const viewBtns = document.querySelectorAll('.view-btn');
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const parent = this.getAttribute('data-parent');
            const child = this.getAttribute('data-child');
            const vaccine = this.getAttribute('data-vaccine');
            const dose = this.getAttribute('data-dose');
            const date = this.getAttribute('data-date');
            const time = this.getAttribute('data-time');
            const status = this.getAttribute('data-status');
            const statusClass = this.getAttribute('data-status-class');

            document.getElementById('modalApptId').textContent = 'Confirmation #' + id;
            
            const modalStatus = document.getElementById('modalStatus');
            modalStatus.textContent = status;
            modalStatus.className = 'badge rounded-pill px-3 ' + statusClass;
            
            document.getElementById('modalParent').textContent = parent;
            document.getElementById('modalChild').textContent = child;
            document.getElementById('modalVaccine').textContent = vaccine;
            document.getElementById('modalDose').textContent = dose ? 'Dose ' + dose : 'N/A';
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalTime').textContent = time;
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
