<?php
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

?>

<div class="main-content">
    <div class="container-fluid px-4 py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold">Children Management</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Children</li>
                    </ol>
                </nav>
            </div>
            <!-- Optional: Add New Child Button if needed in future -->
            <!-- <a href="add_child.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Register Child</a> -->
        </div>

        <!-- Search and Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="searchChild" class="form-label visually-hidden">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="searchChild" placeholder="Search by name, ID, or parent name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="all" selected>All Statuses</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                         <select class="form-select" id="genderFilter">
                            <option value="all" selected>All Genders</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-outline-secondary" id="btnReset">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children List Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Child ID</th>
                                <th>Child Name</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>Parent Name</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dummy Data Row 1 -->
                            <tr>
                                <td class="ps-4 fw-bold">#CH-2023001</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-baby"></i>
                                        </div>
                                        <span>Ahmed Ali</span>
                                    </div>
                                </td>
                                <td>2023-01-15</td>
                                <td>Male</td>
                                <td>Muhammad Ali</td>
                                <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill">Completed</span></td>
                                <td class="text-end pe-4">
                                    <a href="child_profile.php?id=CH-2023001" class="btn btn-sm btn-outline-primary" title="View Profile">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Dummy Data Row 2 -->
                            <tr>
                                <td class="ps-4 fw-bold">#CH-2023045</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-baby"></i>
                                        </div>
                                        <span>Fatima Khan</span>
                                    </div>
                                </td>
                                <td>2023-05-20</td>
                                <td>Female</td>
                                <td>Omar Khan</td>
                                <td><span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Pending</span></td>
                                <td class="text-end pe-4">
                                    <a href="child_profile.php?id=CH-2023045" class="btn btn-sm btn-outline-primary" title="View Profile">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>

                            <!-- Dummy Data Row 3 -->
                            <tr>
                                <td class="ps-4 fw-bold">#CH-2023089</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-baby"></i>
                                        </div>
                                        <span>Zain Malik</span>
                                    </div>
                                </td>
                                <td>2023-08-10</td>
                                <td>Male</td>
                                <td>Bilal Malik</td>
                                <td><span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Pending</span></td>
                                <td class="text-end pe-4">
                                    <a href="child_profile.php?id=CH-2023089" class="btn btn-sm btn-outline-primary" title="View Profile">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>

                             <!-- Dummy Data Row 4 -->
                             <tr>
                                <td class="ps-4 fw-bold">#CH-2022156</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-baby"></i>
                                        </div>
                                        <span>Ayesha Ahmed</span>
                                    </div>
                                </td>
                                <td>2022-11-05</td>
                                <td>Female</td>
                                <td>Naveed Ahmed</td>
                                <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill">Completed</span></td>
                                <td class="text-end pe-4">
                                    <a href="child_profile.php?id=CH-2022156" class="btn btn-sm btn-outline-primary" title="View Profile">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination UI -->
            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3">
                <div class="text-muted small">Showing 1 to 4 of 24 entries</div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active" aria-current="page"><a class="page-link" href="#">1</a></li>
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

<?php include '../includes/footer.php'; ?>

<!-- Client-side Filtering Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnReset = document.getElementById('btnReset');
    const searchInput = document.getElementById('searchChild');
    const statusFilter = document.getElementById('statusFilter');
    const genderFilter = document.getElementById('genderFilter');
    const tableRows = document.querySelectorAll('tbody tr');

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const genderValue = genderFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            const childGender = row.cells[3].textContent.trim().toLowerCase();
            const childStatus = row.cells[5].textContent.trim().toLowerCase();

            const matchesSearch = textContent.includes(searchTerm);
            const matchesStatus = statusValue === 'all' || childStatus.includes(statusValue);
            const matchesGender = genderValue === 'all' || childGender === genderValue;

            if (matchesSearch && matchesStatus && matchesGender) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function resetFilters() {
        searchInput.value = '';
        statusFilter.value = 'all';
        genderFilter.value = 'all';
        applyFilters();
    }

    // Trigger on Reset button click
    btnReset.addEventListener('click', resetFilters);

    // Real-time filtering as the user types/changes
    searchInput.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    genderFilter.addEventListener('change', applyFilters);
});
</script>
