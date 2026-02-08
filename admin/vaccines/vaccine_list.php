<?php
/**
 * Vaccine Availability List
 * 
 * Purpose: View all pre-defined vaccines and manage availability status.
 * Admin CANNOT add new vaccines.
 */

// Reusable Includes
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Dummy Vaccine Data (Simulating database records)
$vaccines = [
    [
        'id' => 'VAC001',
        'name' => 'BCG',
        'age_group' => 'At Birth',
        'doses' => 1,
        'status' => 'Available',
        'updated_at' => 'Feb 01, 2026 10:30 AM'
    ],
    [
        'id' => 'VAC002',
        'name' => 'OPV (Oral Polio Vaccine)',
        'age_group' => '0-5 Years',
        'doses' => 4,
        'status' => 'Available',
        'updated_at' => 'Jan 25, 2026 02:15 PM'
    ],
    [
        'id' => 'VAC003',
        'name' => 'Pentavalent',
        'age_group' => '6, 10, 14 Weeks',
        'doses' => 3,
        'status' => 'Unavailable',
        'updated_at' => 'Feb 03, 2026 09:00 AM'
    ],
    [
        'id' => 'VAC004',
        'name' => 'PCV (Pneumococcal)',
        'age_group' => '6, 10, 14 Weeks',
        'doses' => 3,
        'status' => 'Available',
        'updated_at' => 'Jan 30, 2026 11:45 AM'
    ],
    [
        'id' => 'VAC005',
        'name' => 'Measles',
        'age_group' => '9 Months',
        'doses' => 2,
        'status' => 'Available',
        'updated_at' => 'Feb 02, 2026 04:20 PM'
    ],
    [
        'id' => 'VAC006',
        'name' => 'Hepatitis B',
        'age_group' => 'At Birth',
        'doses' => 1,
        'status' => 'Available',
        'updated_at' => 'Jan 20, 2026 09:00 AM'
    ]
];
?>

<!-- ============================================
     Main Content
     ============================================ -->
<main class="main-content">
    <div class="container-fluid px-4">
        
        <!-- Page Header & Breadcrumb -->
        <div class="d-flex align-items-center justify-content-between mb-4 mt-4">
            <div>
                <h3 class="fw-bold mb-1">Vaccine Availability</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vaccines</li>
                    </ol>
                </nav>
            </div>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Hospitals can add vaccines. Admin updates availability, can't add or delete.
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="vaccineSearch" class="form-control border-start-0" placeholder="Search vaccine name or ID...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select id="statusFilter" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2" onclick="resetFilters()">
                            <i class="fas fa-undo"></i>
                            <span>Reset Filters</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vaccine Table -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-muted text-uppercase small fw-bold" style="width: 100px;">ID</th>
                                <th class="px-4 py-3 text-muted text-uppercase small fw-bold">Vaccine Name</th>
                                <th class="px-4 py-3 text-muted text-uppercase small fw-bold">Age Group</th>
                                <th class="px-4 py-3 text-muted text-uppercase small fw-bold text-center">Dose Count</th>
                                <th class="px-4 py-3 text-muted text-uppercase small fw-bold">Status</th>
                                <th class="px-4 py-3 text-muted text-uppercase small fw-bold">Last Updated</th>
                                <th class="px-4 py-3 text-muted text-uppercase small fw-bold text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="vaccineTableBody">
                            <?php foreach ($vaccines as $vaccine): ?>
                            <tr class="vaccine-row" 
                                data-name="<?= strtolower($vaccine['name']) ?>" 
                                data-id="<?= strtolower($vaccine['id']) ?>" 
                                data-status="<?= $vaccine['status'] ?>">
                                <td class="px-4">
                                    <span class="badge bg-light text-dark border fw-medium"><?= $vaccine['id'] ?></span>
                                </td>
                                <td class="px-4">
                                    <div class="fw-bold text-dark"><?= $vaccine['name'] ?></div>
                                </td>
                                <td class="px-4">
                                    <span class="text-muted"><?= $vaccine['age_group'] ?></span>
                                </td>
                                <td class="px-4 text-center">
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3"><?= $vaccine['doses'] ?></span>
                                </td>
                                <td class="px-4">
                                    <?php if ($vaccine['status'] == 'Available'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> Available
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i> Unavailable
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4">
                                    <div class="small text-muted"><?= $vaccine['updated_at'] ?></div>
                                </td>
                                <td class="px-4 text-end">
                                    <a href="update_vaccine_status.php?id=<?= $vaccine['id'] ?>&name=<?= urlencode($vaccine['name']) ?>&age=<?= urlencode($vaccine['age_group']) ?>&doses=<?= $vaccine['doses'] ?>&status=<?= $vaccine['status'] ?>" 
                                       class="btn btn-sm btn-white border shadow-sm rounded-pill px-3">
                                        <i class="fas fa-sync-alt me-1 text-primary"></i> 
                                        Update Status
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination UI (Static for UI Demo) -->
            <div class="card-footer bg-white border-0 py-3 rounded-bottom-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="small text-muted">
                        Showing <b>1</b> to <b><?= count($vaccines) ?></b> of <b><?= count($vaccines) ?></b> vaccines
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link shadow-none" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link shadow-none" href="#">1</a></li>
                            <li class="page-item"><a class="page-link shadow-none" href="#">2</a></li>
                            <li class="page-item">
                                <a class="page-link shadow-none" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Frontend Logic for Search and Filter -->
<script>
function applyFilters() {
    const searchQuery = document.getElementById('vaccineSearch').value.toLowerCase();
    const statusQuery = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.vaccine-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const id = row.getAttribute('data-id');
        const status = row.getAttribute('data-status');

        const matchesSearch = name.includes(searchQuery) || id.includes(searchQuery);
        const matchesStatus = statusQuery === "" || status === statusQuery;

        if (matchesSearch && matchesStatus) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    });

    // Handle empty state (optional)
    const tableBody = document.getElementById('vaccineTableBody');
    let emptyState = document.getElementById('emptyStateRow');
    
    if (visibleCount === 0) {
        if (!emptyState) {
            emptyState = document.createElement('tr');
            emptyState.id = 'emptyStateRow';
            emptyState.innerHTML = `<td colspan="7" class="text-center py-5 text-muted">
                <i class="fas fa-search mb-3 fs-1 d-block"></i>
                No vaccines found matching your criteria.
            </td>`;
            tableBody.appendChild(emptyState);
        }
    } else if (emptyState) {
        emptyState.remove();
    }
}

function resetFilters() {
    document.getElementById('vaccineSearch').value = '';
    document.getElementById('statusFilter').value = '';
    applyFilters();
}

// Attach event listeners for real-time feel
document.getElementById('vaccineSearch').addEventListener('input', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);
</script>

<?php include '../includes/footer.php'; ?>