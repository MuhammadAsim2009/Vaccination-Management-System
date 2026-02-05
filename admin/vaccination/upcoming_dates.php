<?php
// Reusable Includes
include '../includes/header.php';
include '../includes/sidebar.php';
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
                            <option value="BCG">BCG</option>
                            <option value="Polio">Polio (OPV)</option>
                            <option value="Hepatitis B">Hepatitis B</option>
                            <option value="DTP">DTP</option>
                            <option value="Measles">Measles</option>
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
                                <th>Hospital Name</th>
                                <th>Status</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dummy Row 1 -->
                            <tr class="vaccination-row" data-vaccine="Polio (OPV)" data-date="2026-02-15">
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: 600;">
                                            AA
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Ayaan Ahmed</div>
                                            <small class="text-muted">ID: #C-5012</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Mohammad Ahmed</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info fw-semibold rounded-pill px-3">Polio (OPV)</span>
                                </td>
                                <td>
                                    <div class="fw-medium">Feb 15, 2026</div>
                                    <small class="text-muted">10:30 AM</small>
                                </td>
                                <td>City General Hospital</td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning fw-semibold rounded-pill px-3">Upcoming</span>
                                </td>
                                <td class="text-end px-4">
                                    <a href="../children/child_profile.php?id=1" class="btn btn-sm btn-light border" title="View Child Profile">
                                        <i class="fas fa-user-circle text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            <!-- Dummy Row 2 -->
                            <tr class="vaccination-row" data-vaccine="BCG" data-date="2026-02-01">
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: 600;">
                                            ZK
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Zainab Khan</div>
                                            <small class="text-muted">ID: #C-4988</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Abdul Wahab</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold rounded-pill px-3">BCG</span>
                                </td>
                                <td>
                                    <div class="fw-medium text-danger">Feb 01, 2026</div>
                                    <small class="text-danger">Overdue</small>
                                </td>
                                <td>Life Care Clinic</td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger fw-semibold rounded-pill px-3">Missed</span>
                                </td>
                                <td class="text-end px-4">
                                    <a href="../children/child_profile.php?id=2" class="btn btn-sm btn-light border" title="View Child Profile">
                                        <i class="fas fa-user-circle text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            <!-- Dummy Row 3 -->
                            <tr class="vaccination-row" data-vaccine="Hepatitis B" data-date="2026-02-20">
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: 600;">
                                            OM
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Omar Malik</div>
                                            <small class="text-muted">ID: #C-5025</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Bilal Malik</td>
                                <td>
                                    <span class="badge bg-purple-subtle text-purple fw-semibold rounded-pill px-3" style="background-color: #f3e8ff; color: #7e22ce;">Hepatitis B</span>
                                </td>
                                <td>
                                    <div class="fw-medium">Feb 20, 2026</div>
                                    <small class="text-muted">09:00 AM</small>
                                </td>
                                <td>Mother & Child Health</td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning fw-semibold rounded-pill px-3">Upcoming</span>
                                </td>
                                <td class="text-end px-4">
                                    <a href="../children/child_profile.php?id=3" class="btn btn-sm btn-light border" title="View Child Profile">
                                        <i class="fas fa-user-circle text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            <!-- Dummy Row 4 -->
                            <tr class="vaccination-row" data-vaccine="Measles" data-date="2026-02-28">
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: 600;">
                                            FS
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Fatima Sheikh</div>
                                            <small class="text-muted">ID: #C-5044</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Imran Sheikh</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info fw-semibold rounded-pill px-3">Measles</span>
                                </td>
                                <td>
                                    <div class="fw-medium">Feb 28, 2026</div>
                                    <small class="text-muted">11:15 AM</small>
                                </td>
                                <td>City General Hospital</td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning fw-semibold rounded-pill px-3">Upcoming</span>
                                </td>
                                <td class="text-end px-4">
                                    <a href="../children/child_profile.php?id=4" class="btn btn-sm btn-light border" title="View Child Profile">
                                        <i class="fas fa-user-circle text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-top">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted small mb-0">Showing 4 of 24 upcoming schedules</p>
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
