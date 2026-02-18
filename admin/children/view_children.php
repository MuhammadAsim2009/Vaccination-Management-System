<?php
// Reusable Includes
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Fetch children from database
$stmt_children = $conn->prepare("SELECT c.*, u.name AS parent_name FROM children c LEFT JOIN users u ON c.parent_id = u.id ORDER BY c.created_at ASC");
$stmt_children->execute();
$stmt_results = $stmt_children->get_result();


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
        </div>

        <!-- Search and Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="searchChild" class="form-label visually-hidden">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="searchChild" placeholder="Search by name, ID, or parent name...">
                        </div>
                    </div>
                    <div class="col-md-4">
                         <select class="form-select" id="genderFilter">
                            <option value="all" selected>All Genders</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-grid">
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
                                <th>Enrollment Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Children Rows-->
                            <?php 
                                if ($stmt_results->num_rows > 0) {
                                    while ($children = $stmt_results->fetch_assoc()) {
                                        echo "
                                            <tr>
                                                <td class='ps-4 fw-bold'>#".$children['id']."</td>
                                                <td>
                                                    <div class='d-flex align-items-center'>
                                                        <div class='" . (($children['gender'] == 'Male') ? 'bg-primary bg-opacity-10 text-primary' : 'bg-danger bg-opacity-10 text-danger') . " rounded-circle me-3 d-flex align-items-center justify-content-center' style='width: 32px; height: 32px;'>
                                                            <i class='fas fa-baby'></i>
                                                        </div>
                                                        <span>".$children['name']."</span>
                                                    </div>
                                                </td>
                                                <td>".$children['date_of_birth']."</td>
                                                <td><span class='badge " . (($children['gender'] == 'Male') ? 'bg-primary bg-opacity-10 text-primary' : 'bg-danger bg-opacity-10 text-danger') . " rounded-pill'>".$children['gender']."</span></td>
                                                <td>".$children['parent_name']."</td>
                                                <td>".date('Y-m-d', strtotime($children['created_at']))."</td>
                                                <td class='text-end pe-4'>
                                                    <a href='child_profile.php?id=".$children['id']."' class='btn btn-sm btn-outline-primary' title='View Profile'>
                                                        <i class='fas fa-eye me-1'></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        ";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3">
                <div class="text-muted small">Showing <?= $stmt_results->num_rows; ?> entries</div>
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
    const genderFilter = document.getElementById('genderFilter');
    const tableRows = document.querySelectorAll('tbody tr');

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const genderValue = genderFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            const childGender = row.cells[3].textContent.trim().toLowerCase();

            const matchesSearch = textContent.includes(searchTerm);
            const matchesGender = genderValue === 'all' || childGender === genderValue;

            if (matchesSearch && matchesGender) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function resetFilters() {
        searchInput.value = '';
        genderFilter.value = 'all';
        applyFilters();
    }

    // Trigger on Reset button click
    btnReset.addEventListener('click', resetFilters);

    // Real-time filtering as the user types/changes
    searchInput.addEventListener('input', applyFilters);
    genderFilter.addEventListener('change', applyFilters);
});
</script>
