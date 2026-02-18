<?php

// Include authentication check
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php'; 
include '../includes/sidebar.php'; 

// Get logged-in parent's ID
$parent_id = $_SESSION['user_id'];

// Fetch children of the logged-in parent
$stmt_children = $conn->prepare("SELECT * FROM children WHERE parent_id = ? ORDER BY created_at DESC");
$stmt_children->bind_param("i", $parent_id);
$stmt_children->execute();
$result_children = $stmt_children->get_result();

// Store children and their IDs
$children_data = [];
$child_ids = [];
if ($result_children->num_rows > 0) {
    while ($child = $result_children->fetch_assoc()) {
        $children_data[$child['id']] = $child;
        $child_ids[] = $child['id'];
    }
}

// Fetch vaccination statistics for all children of this parent
$vaccination_stats = [];
if (!empty($child_ids)) {
    // Create placeholders for the IN clause
    $ids_placeholder = implode(',', array_fill(0, count($child_ids), '?'));
    $types = str_repeat('i', count($child_ids));

    $sql_stats = "SELECT 
                    child_id,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'vaccinated' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'missed' THEN 1 ELSE 0 END) as missed
                  FROM vaccination_schedule
                  WHERE child_id IN ($ids_placeholder)
                  GROUP BY child_id";
    
    $stmt_stats = $conn->prepare($sql_stats);
    $stmt_stats->bind_param($types, ...$child_ids);
    $stmt_stats->execute();
    $result_stats = $stmt_stats->get_result();

    while ($stat_row = $result_stats->fetch_assoc()) {
        $vaccination_stats[$stat_row['child_id']] = $stat_row;
    }
}

?>

<!-- Main Content -->
    <div class="container-fluid px-4">
        
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="mb-2 fw-bold text-dark">My Children</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Children</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <button class="btn btn-primary btn-md shadow-sm" onclick="window.location.href='add_child.php'">
                            <i class="fas fa-plus-circle me-2"></i>Add Child
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search by Name -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary small">Search Child</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchChild" class="form-control border-start-0 ps-0" placeholder="Search by name...">
                        </div>
                    </div>

                    <!-- Filter by Gender -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-secondary small">Gender</label>
                        <select id="filterGender" class="form-select">
                            <option value="">All Genders</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <!-- Filter by Vaccination Status -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-secondary small">Vaccination Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">All Status</option>
                            <option value="Completed">Completed</option>
                            <option value="Pending">Pending</option>
                            <option value="Missed">Missed</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="resetFilters" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children Cards Grid -->
        <div class="row g-4 mb-5" id="childrenCardsContainer">
            
            <?php if (!empty($children_data)): ?>
                <?php foreach ($children_data as $child_id => $child): ?>
                    <?php
                        // Age calculation
                        $dob = new DateTime($child['date_of_birth']);
                        $today = new DateTime();
                        $age_interval = $today->diff($dob);
                        $age_years = $age_interval->y;
                        $age_months = $age_interval->m;
                        $age_days = $age_interval->d;

                        if ($age_years > 0) {
                            $age_string = $age_years . ($age_years == 1 ? ' year' : ' years');
                        } elseif ($age_months > 0) {
                            $age_string = $age_months . ($age_months == 1 ? ' month' : ' months');
                        } else {
                            $age_string = $age_days . ($age_days == 1 ? ' day' : ' days');
                        }

                        // Vaccination stats
                        $stats = $vaccination_stats[$child_id] ?? ['total' => 0, 'completed' => 0, 'pending' => 0, 'missed' => 0];
                        $total_vaccines = (int)($stats['total'] ?? 0);
                        $completed_vaccines = (int)($stats['completed'] ?? 0);
                        $pending_vaccines = (int)($stats['pending'] ?? 0);
                        $missed_vaccines = (int)($stats['missed'] ?? 0);

                        $completion_percentage = $total_vaccines > 0 ? ($completed_vaccines / $total_vaccines) * 100 : 0;

                        // Determine overall status for filtering and display
                        $overall_status = 'Completed';
                        $alert_class = 'alert-success bg-soft-success';
                        $alert_icon = 'fa-check-circle';
                        $alert_text = 'All vaccines up to date!';

                        if ($missed_vaccines > 0) {
                            $overall_status = 'Missed';
                            $alert_class = 'alert-danger bg-soft-danger';
                            $alert_icon = 'fa-exclamation-triangle';
                            $alert_text = "<strong>{$missed_vaccines}</strong> missed vaccine(s)";
                        } elseif ($pending_vaccines > 0) {
                            $overall_status = 'Pending';
                            $alert_class = 'alert-info bg-soft-info';
                            $alert_icon = 'fa-syringe';
                            $alert_text = "<strong>{$pending_vaccines}</strong> upcoming vaccine(s)";
                        }

                        // Determine progress bar and badge color
                        $progress_bar_class = 'bg-success';
                        if ($completion_percentage < 50) {
                            $progress_bar_class = 'bg-danger';
                        } elseif ($completion_percentage < 80) {
                            $progress_bar_class = 'bg-warning';
                        }
                        $badge_class = $progress_bar_class . ($progress_bar_class == 'bg-warning' ? ' text-dark' : '');
                    ?>
                    <div class="col-lg-4 col-md-6 child-card" data-name="<?= htmlspecialchars($child['name']) ?>" data-gender="<?= htmlspecialchars($child['gender']) ?>" data-status="<?= $overall_status ?>">
                        <div class="card h-100 border-0 shadow-sm hover-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-circle bg-soft-primary me-3">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($child['name']) ?>&background=e3f2fd&color=1976d2&size=80&bold=true" 
                                             alt="<?= htmlspecialchars($child['name']) ?>" class="rounded-circle" width="80" height="80">
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($child['name']) ?></h5>
                                        <p class="mb-0 text-muted small">
                                            <i class="fas fa-birthday-cake me-1"></i><?= $age_string ?> old
                                        </p>
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="info-box bg-light rounded p-2 text-center">
                                            <?php if ($child['gender'] == 'Male'): ?>
                                                <i class="fas fa-mars text-primary mb-1"></i>
                                            <?php else: ?>
                                                <i class="fas fa-venus text-danger mb-1"></i>
                                            <?php endif; ?>
                                            <p class="mb-0 small fw-semibold"><?= htmlspecialchars($child['gender']) ?></p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box bg-light rounded p-2 text-center">
                                            <i class="fas fa-tint text-danger mb-1"></i>
                                            <p class="mb-0 small fw-semibold"><?= htmlspecialchars($child['blood_group']) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="vaccination-stats mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Vaccines Completed</span>
                                        <span class="badge <?= $badge_class ?>"><?= $completed_vaccines ?>/<?= $total_vaccines ?></span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar <?= $progress_bar_class ?>" role="progressbar" style="width: <?= $completion_percentage ?>%;" aria-valuenow="<?= $completion_percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="alert <?= $alert_class ?> border-0 py-2 mb-3">
                                    <i class="fas <?= $alert_icon ?> me-2"></i>
                                    <small><?= $alert_text ?></small>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="child_details.php?id=<?= $child['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                    <div class="btn-group" role="group">
                                        <a href="../vaccination/vaccination_report.php?child_id=<?= $child['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-history me-1"></i>History
                                        </a>
                                        <a href="update_child.php?id=<?= $child['id'] ?>" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit me-1"></i>Update
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5 bg-light rounded-3">
                        <div class="mb-3">
                            <i class="fas fa-child fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">No Children Found</h5>
                        <p class="text-muted small">You haven't added any children yet. Click the button below to get started.</p>
                        <a href="add_child.php" class="btn btn-primary mt-2">
                            <i class="fas fa-plus-circle me-2"></i>Add Your First Child
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Search and Filter Functionality
    const searchInput = document.getElementById('searchChild');
    const genderFilter = document.getElementById('filterGender');
    const statusFilter = document.getElementById('filterStatus');
    const resetBtn = document.getElementById('resetFilters');
    
    // Filter Cards
    function filterCards() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedGender = genderFilter.value;
        const selectedStatus = statusFilter.value;
        
        const cards = document.querySelectorAll('.child-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            const gender = card.getAttribute('data-gender') || '';
            const status = card.getAttribute('data-status');
            
            const matchesSearch = name.includes(searchTerm);
            const matchesGender = !selectedGender || gender === selectedGender;
            const matchesStatus = !selectedStatus || status === selectedStatus;
            
            if (matchesSearch && matchesGender && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Filter Table Rows
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedGender = genderFilter.value;
        const selectedStatus = statusFilter.value;
        
        const rows = document.querySelectorAll('.table-row');
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name').toLowerCase();
            const gender = row.getAttribute('data-gender');
            const status = row.getAttribute('data-status');
            
            const matchesSearch = name.includes(searchTerm);
            const matchesGender = !selectedGender || gender === selectedGender;
            const matchesStatus = !selectedStatus || status === selectedStatus;
            
            if (matchesSearch && matchesGender && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Apply filters on both cards and table
    function applyFilters() {
        filterCards();
        filterTable();
    }
    
    // Event Listeners
    searchInput.addEventListener('keyup', applyFilters);
    genderFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    
    // Reset Filters
    resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        genderFilter.value = '';
        statusFilter.value = '';
        applyFilters();
    });
    
    // Add smooth scroll animation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
});
</script>

<?php include '../includes/footer.php'; ?>
