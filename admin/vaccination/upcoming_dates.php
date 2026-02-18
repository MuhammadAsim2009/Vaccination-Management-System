<?php
// Reusable Includes
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Fetch Vaccines for Filter
$vaccines_list = [];
$stmt_v = $conn->prepare("SELECT DISTINCT vaccine_name FROM vaccines ORDER BY vaccine_name ASC");
$stmt_v->execute();
$res_v = $stmt_v->get_result();
while($row_v = $res_v->fetch_assoc()){
    $vaccines_list[] = $row_v['vaccine_name'];
}

// Fetch Upcoming Vaccinations
$query = "SELECT 
            vs.scheduled_date, 
            vs.status, 
            vs.dose_number, 
            c.id as child_id, 
            c.name as child_name, 
            u.name as parent_name, 
            v.vaccine_name, 
            h.hospital_name 
          FROM vaccination_schedule vs
          JOIN children c ON vs.child_id = c.id
          JOIN users u ON c.parent_id = u.id
          JOIN vaccines v ON vs.vaccine_id = v.id
          LEFT JOIN hospitals h ON vs.hospital_id = h.id
          ORDER BY vs.scheduled_date ASC";
$result = $conn->query($query);
?>

<!-- ============================================
     Main Content
     ============================================ -->
<main class="main-content">
    <div class="container-fluid px-4">
        
        <!-- Page Header & Breadcrumb -->
        <div class="d-flex align-items-center justify-content-between mb-4 mt-4">
            <div>
                <h3 class="fw-bold mb-1">Upcoming Vaccinations</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="vaccination_report.php" class="text-decoration-none">Vaccination</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Upcoming</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="vaccination_report.php" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fas fa-chart-bar"></i>
                    <span>View Vaccination Reports</span>
                </a>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form class="row g-3 align-items-end" id="filterForm">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Filter by Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-calendar-alt text-muted"></i></span>
                            <input type="date" id="dateFilter" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Vaccine Type</label>
                        <select class="form-select" id="vaccineFilter">
                            <option value="">All Vaccines</option>
                            <?php foreach($vaccines_list as $vac): ?>
                                <option value="<?= htmlspecialchars($vac) ?>"><?= htmlspecialchars($vac) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="fas fa-filter me-2"></i>Apply
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light border w-100" onclick="resetFilters()">
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Scheduled List</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Child Name</th>
                                <th>Parent Name</th>
                                <th>Vaccine Name</th>
                                <th>Scheduled Date</th>
                                <th>Dose Number</th>
                                <th>Hospital Name</th>
                                <th>Status</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $date_raw = date('Y-m-d', strtotime($row['scheduled_date']));
                                $display_date = date('M d, Y', strtotime($row['scheduled_date']));
                                $time = date('h:i A', strtotime($row['scheduled_date']));
                                
                                // Status Logic
                                $status_badge = 'bg-secondary-subtle text-secondary';
                                $status_text = ucfirst($row['status']);
                                
                                if(strtolower($row['status']) == 'pending') {
                                    $status_badge = 'bg-warning-subtle text-warning';
                                    $status_text = 'Upcoming';
                                } elseif(strtolower($row['status']) == 'vaccinated') {
                                    $status_badge = 'bg-success-subtle text-success';
                                    $status_text = 'Completed';
                                } elseif(strtolower($row['status']) == 'missed') {
                                    $status_badge = 'bg-danger-subtle text-danger';
                                }

                                // Initials for Avatar
                                $initials = strtoupper(substr($row['child_name'], 0, 2));
                                $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                $color = $colors[array_rand($colors)];
                            ?>
                            <tr class="vaccination-row" data-vaccine="<?= htmlspecialchars($row['vaccine_name']) ?>" data-date="<?= $date_raw ?>">
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-<?= $color ?>-subtle text-<?= $color ?> d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: 600;">
                                            <?= $initials ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($row['child_name']) ?></div>
                                            <small class="text-muted">ID: #<?= $row['child_id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['parent_name']) ?></td>
                                <td>
                                    <span class="badge bg-info-subtle text-info fw-semibold rounded-pill px-3"><?= htmlspecialchars($row['vaccine_name']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-medium"><?= $display_date ?></div>
                                    <small class="text-muted"><?= $time ?></small>
                                </td>
                                <td class="text-center"><span class="badge bg-primary-subtle text-primary fw-semibold rounded-pill px-3"><?= htmlspecialchars($row['dose_number']) ?></span></td>
                                <td><?= htmlspecialchars($row['hospital_name'] ?? 'Not Assigned') ?></td>
                                <td>
                                    <span class="badge <?= $status_badge ?> fw-semibold rounded-pill px-3"><?= $status_text ?></span>
                                </td>
                                <td class="text-end px-4">
                                    <a href="../children/child_profile.php?id=<?= $row['child_id'] ?>" class="btn btn-sm btn-light border" title="View Child Profile">
                                        <i class="fas fa-user-circle text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">No upcoming vaccinations found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-top">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted small mb-0">Showing <?= $result ? $result->num_rows : 0 ?> upcoming schedules</p>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active" aria-current="page"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- JS Filtering Logic -->
<script>
/**
 * Apply filters to the scheduled vaccinations list
 */
function applyFilters() {
    const dateQuery = document.getElementById('dateFilter').value; // YYYY-MM-DD
    const vaccineQuery = document.getElementById('vaccineFilter').value.toLowerCase();
    const rows = document.querySelectorAll('.vaccination-row');

    rows.forEach(row => {
        const rowDate = row.getAttribute('data-date'); // YYYY-MM-DD
        const rowVaccine = row.getAttribute('data-vaccine').toLowerCase();

        const dateMatch = dateQuery === "" || rowDate === dateQuery;
        const vaccineMatch = vaccineQuery === "" || rowVaccine.includes(vaccineQuery);

        if (dateMatch && vaccineMatch) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

/**
 * Reset filters and show all rows
 */
function resetFilters() {
    document.getElementById('filterForm').reset();
    const rows = document.querySelectorAll('.vaccination-row');
    rows.forEach(row => {
        row.style.display = "";
    });
}
</script>

<?php
include '../includes/footer.php';
?>
