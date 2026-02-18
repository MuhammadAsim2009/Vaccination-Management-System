<?php
// Required includes for database connection, authentication, and layout
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Check if child ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid child ID. Please go back and try again.</div>";
    include '../includes/footer.php';
    exit;
}

// Retrieve child ID from URL
$child_id = $_GET['id'];

// Fetch child details from database
$stmt_child = $conn->prepare("SELECT c.*, u.name AS parent_name, p.phone AS parent_phone, p.address AS parent_address FROM children c LEFT JOIN users u ON c.parent_id = u.id LEFT JOIN parents p ON c.parent_id = p.user_id WHERE c.id = ?");
$stmt_child->bind_param("i", $child_id);
$stmt_child->execute();
$result_child = $stmt_child->get_result();
$child_data = $result_child->fetch_assoc();

// If child not found, display error message
if (!$child_data) {
    echo "<div class='alert alert-danger'>Child not found. Please go back and try again.</div>";
    include '../includes/footer.php';
    exit;
}

// Fetch vaccination history
$stmt_history = $conn->prepare("SELECT vs.*, v.vaccine_name, h.hospital_name 
                                FROM vaccination_schedule vs 
                                JOIN vaccines v ON vs.vaccine_id = v.id 
                                LEFT JOIN hospitals h ON vs.hospital_id = h.id 
                                WHERE vs.child_id = ? 
                                ORDER BY vs.scheduled_date ASC");
$stmt_history->bind_param("i", $child_id);
$stmt_history->execute();
$result_history = $stmt_history->get_result();

$total_records = $result_history->num_rows;
$completed_count = 0;
$history_rows = [];

while ($row = $result_history->fetch_assoc()) {
    $history_rows[] = $row;
    if (isset($row['status']) && strtolower($row['status']) === 'vaccinated') {
        $completed_count++;
    }
}


?>

<div class="main-content">
    <div class="container-fluid px-4 py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 fw-bold">Child Profile</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="view_children.php" class="text-decoration-none">Children</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
            <a href="view_children.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="row">
            <!-- Left Column: Child & Parent Info -->
            <div class="col-lg-4 mb-4">
                <!-- Child Info Card -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Child Information</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="fas fa-baby"></i>
                        </div>
                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($child_data['name']); ?></h5>
                        <p class="text-muted mb-3">ID: #<?php echo htmlspecialchars($child_data['id']); ?></p>
                        
                        <div class="text-start mt-4">
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted small">Date of Birth</span>
                                <span class="fw-medium"><?php echo htmlspecialchars($child_data['date_of_birth']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted small">Gender</span>
                                <span class="fw-medium"><?php echo htmlspecialchars($child_data['gender']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-0">
                                <span class="text-muted small">Blood Group</span>
                                <span class="fw-medium"><?php echo htmlspecialchars($child_data['blood_group']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent Info Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 fw-bold text-primary">Parent Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($child_data['parent_name']); ?></h6>
                                <p class="text-muted small mb-0">Father</p>
                            </div>
                        </div>
                        
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item px-0 d-flex align-items-center border-0 pb-1">
                                <i class="fas fa-phone-alt text-muted me-2" style="width: 16px;"></i>
                                <span><?php echo htmlspecialchars($child_data['parent_phone']); ?></span>
                            </li>
                            <li class="list-group-item px-0 d-flex align-items-center border-0 pt-1">
                                <i class="fas fa-map-marker-alt text-muted me-2" style="width: 16px;"></i>
                                <span><?php echo htmlspecialchars($child_data['parent_address']); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Vaccination History -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">Vaccination History</h6>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill"><?php echo $completed_count; ?> / <?php echo $total_records; ?> Completed</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th class="ps-4">Vaccine Name</th>
                                        <th>Scheduled Date</th>
                                        <th>Vaccinated Date</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Administered By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($history_rows) > 0): ?>
                                        <?php foreach ($history_rows as $record): 
                                            // Determine Status Badge
                                            $status = ucfirst($record['status'] ?? 'Scheduled');
                                            $badgeClass = 'bg-secondary bg-opacity-10 text-secondary';
                                            
                                            if (strtolower($status) === 'vaccinated') {
                                                $badgeClass = 'bg-success bg-opacity-10 text-success';
                                            } elseif (strtolower($status) === 'pending' || strtolower($status) === 'scheduled') {
                                                $badgeClass = 'bg-warning bg-opacity-10 text-warning';
                                            } elseif (strtolower($status) === 'missed') {
                                                $badgeClass = 'bg-danger bg-opacity-10 text-danger';
                                            }

                                            // Format Dates
                                            $scheduledDate = date('M d, Y', strtotime($record['scheduled_date']));
                                            $vaccinatedDate = !empty($record['administered_date']) ? date('M d, Y', strtotime($record['administered_date'])) : '-';
                                            
                                            // Hospital Name
                                            $hospitalName = !empty($record['hospital_name']) ? htmlspecialchars($record['hospital_name']) : '<span class="text-muted">-</span>';
                                        ?>
                                        <tr>
                                            <td class="ps-4 fw-medium">
                                                <?php echo htmlspecialchars($record['vaccine_name']); ?> 
                                                <span class="text-muted small ms-1">(Dose <?php echo $record['dose_number']; ?>)</span>
                                            </td>
                                            <td><?php echo $scheduledDate; ?></td>
                                            <td><?php echo $vaccinatedDate; ?></td>
                                            <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span></td>
                                            <td class="text-end pe-4 text-muted small"><?php echo $hospitalName; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No vaccination history found for this child.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
