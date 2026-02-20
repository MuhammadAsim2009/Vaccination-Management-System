<?php
// Essential includes for authentication, header, sidebar, and database connection
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../../config/functions.php';

// Handle Approval/Rejection Actions
$action_msg = '';
$action_type = '';

// Handle Approval
if (isset($_POST['approve_request_btn'])) {
    $request_id = $_POST['request_id'];

    // Start transaction to ensure both operations succeed or fail together
    $conn->begin_transaction();

    try {
        // First, update the appointment status from 'requested' to 'approved'
        $stmt_approved = $conn->prepare("UPDATE appointments SET status = 'approved' WHERE id = ? AND status = 'requested'");
        $stmt_approved->bind_param("i", $request_id);
        $stmt_approved->execute();

        // Check if the update was successful
        if ($stmt_approved->affected_rows > 0) {
            // Fetch appointment details to create a schedule entry
            $stmt_fetch = $conn->prepare("SELECT a.child_id, a.vaccine_id, a.hospital_id, a.appointment_date, a.dose_number, a.parent_id, h.user_id AS hospital_user_id, c.name AS child_name FROM appointments a JOIN hospitals h ON a.hospital_id = h.id JOIN children c ON a.child_id = c.id WHERE a.id = ?");
            $stmt_fetch->bind_param("i", $request_id);
            $stmt_fetch->execute();
            $appointment = $stmt_fetch->get_result()->fetch_assoc();
            $stmt_fetch->close();

            if ($appointment) {
                // Extract details for scheduling and notifications
                $child_name = $appointment['child_name'];
                $child_id = $appointment['child_id'];
                $vaccine_id = $appointment['vaccine_id'];
                $hospital_id = $appointment['hospital_id'];
                $parent_id = $appointment['parent_id'];
                $hospital_user_id = $appointment['hospital_user_id'];
                $dose_number = $appointment['dose_number'];
                $scheduled_date = date('Y-m-d H:i:s', strtotime($appointment['appointment_date']));

                // Insert into vaccination_schedule table.
                $stmt_schedule = $conn->prepare("INSERT INTO vaccination_schedule (child_id, vaccine_id, hospital_id, scheduled_date, dose_number) VALUES (?, ?, ?, ?, ?)");
                $stmt_schedule->bind_param("iiisi", $child_id, $vaccine_id, $hospital_id, $scheduled_date, $dose_number);
                $stmt_schedule->execute();
                $stmt_schedule->close();

                // --- Trigger Notifications ---
                // 1. Log Admin Action (for admin panel)
                $admin_id = $_SESSION['user_id'];
                $admin_name = $_SESSION['name'];
                send_notification($conn, 'admin', null, $admin_id, 'appointment', 'Appointment Approved', "Admin '$admin_name' approved appointment request #$request_id for child '$child_name'.");
                // 2. Notify Parent
                send_notification($conn, 'parent', $parent_id, $admin_id, 'appointment', 'Appointment Approved', "Your appointment request #$request_id for child '$child_name' has been approved and scheduled.");
                // 3. Notify Hospital
                send_notification($conn, 'hospital', $hospital_user_id, $admin_id, 'appointment', 'New Appointment Scheduled', "A new appointment (Request #$request_id) has been scheduled at your facility for child '$child_name'.");

                // Commit the transaction since all queries were successful
                $conn->commit();
                $action_msg = "Request #REQ-" . htmlspecialchars($request_id) . " approved and scheduled successfully.";
                $action_type = "success";
            } else {
                // This case is unlikely if update succeeded, but it's a good safety check
                $conn->rollback();
                $action_msg = "Error: Could not retrieve appointment details for scheduling after approval.";
                $action_type = "danger";
            }
        } else {
            // If no rows were affected, the request might have been already processed or didn't exist.
            $conn->rollback();
            $action_msg = "Request could not be approved. It may have already been processed or does not exist.";
            $action_type = "warning";
        }
        $stmt_approved->close();
    } catch (Exception $e) {
        // Rollback the transaction on any database error
        $conn->rollback();
        $action_msg = "A database error occurred during the approval process: " . $e->getMessage();
        $action_type = "danger";
    }
}

// Handle Rejection
if (isset($_POST['reject_request_btn'])) {
    $request_id = $_POST['request_id'];
    $rejection_reason = trim($_POST['rejection_reason'] ?? 'No reason provided.');

    // Fetch parent_id before updating to notify the correct parent
    $stmt_fetch_parent = $conn->prepare("SELECT a.parent_id, c.name AS child_name FROM appointments a JOIN children c ON a.child_id = c.id WHERE a.id = ?");
    $stmt_fetch_parent->bind_param("i", $request_id);
    $stmt_fetch_parent->execute();
    $appointment_details = $stmt_fetch_parent->get_result()->fetch_assoc();
    $stmt_fetch_parent->close();

    if ($appointment_details) {
        $parent_id = $appointment_details['parent_id'];
        $child_name = $appointment_details['child_name'];

        if ($stmt_rejected = $conn->prepare("UPDATE appointments SET status = 'rejected' WHERE id = ?")) {
            $stmt_rejected->bind_param("i", $request_id);

            if ($stmt_rejected->execute()) {
                // --- Trigger Notifications ---
                // 1. Log Admin Action
                $admin_id = $_SESSION['user_id'];
                $admin_name = $_SESSION['name'];
                send_notification($conn, 'admin', null, $admin_id, 'appointment', 'Appointment Rejected', "Admin '$admin_name' rejected appointment request #$request_id for child '$child_name'.");

                // 2. Notify Parent
                $notif_message = "Your appointment request #$request_id for child '$child_name' has been rejected.";
                if (!empty($rejection_reason) && $rejection_reason !== 'No reason provided.') {
                    $notif_message .= " Reason: " . htmlspecialchars($rejection_reason);
                }
                send_notification($conn, 'parent', $parent_id, $admin_id, 'appointment', 'Appointment Rejected', $notif_message);

                $action_msg = "Request #REQ-" . htmlspecialchars($request_id) . " rejected successfully.";
                $action_type = "success";
            } else {
                $action_msg = "Error rejecting request: " . $stmt_rejected->error;
                $action_type = "danger";
            }
            $stmt_rejected->close();
        }
    } else {
        $action_msg = "Error: Could not find appointment to reject.";
        $action_type = "danger";
    }
}

// Fetch appointment requests (Moved after update logic to show latest data)
$stmt_requests = $conn->prepare("SELECT r.id, c.name AS child_name, u.name AS parent_name, v.vaccine_name, r.appointment_date, h.hospital_name, r.status FROM appointments r JOIN children c ON r.child_id = c.id JOIN parents p ON r.parent_id = p.id JOIN users u ON p.user_id = u.id JOIN vaccines v ON r.vaccine_id = v.id JOIN hospitals h ON r.hospital_id = h.id ORDER BY r.created_at DESC");
$stmt_requests->execute();
$result_requests = $stmt_requests->get_result();

// Fetch statistics (Moved after update logic)
$stmt_cards = $conn->prepare("SELECT COUNT(*) AS total_requests, SUM(status = 'approved') AS total_approved, SUM(status = 'requested') AS total_pending FROM appointments");
$stmt_cards->execute();
$result_cards = $stmt_cards->get_result()->fetch_assoc();

$total_requests = $result_cards['total_requests'];
$total_approved = $result_cards['total_approved'];
$total_pending  = $result_cards['total_pending'];

?>

<main class="main-content">
    <div class="container-fluid px-4">
        
        <!-- Breadcrumb & Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-4 mt-4">
            <div>
                <h3 class="fw-bold mb-1">Appointment Requests</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Requests</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button class="btn btn-outline-primary shadow-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh List
                </button>
            </div>
        </div>

        <!-- Alerts Placeholder -->
        <div id="alertPlaceholder"><?php if($action_msg): ?><div class="alert alert-<?= $action_type ?> alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 mb-4" role="alert"><div class="d-flex align-items-center"><i class="fas fa-<?= $action_type == 'success' ? 'check-circle' : 'exclamation-circle' ?> me-3 fs-4"></i><div><?= $action_msg ?></div></div><button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?></div>

        <!-- Requests Statistics -->
        <div class="row g-4 mb-4">
            <!-- Pending Requests Card -->
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Pending Requests</h6>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-clock fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= $total_pending ?></h2>
                        <div class="d-flex align-items-center text-warning small fw-medium">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <span>Action required</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Today Card -->
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Total Approved</h6>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-check-circle fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= $total_approved ?></h2>
                        <div class="d-flex align-items-center text-success small fw-medium">
                            <i class="fas fa-arrow-up me-1"></i>
                            <span>5 new since 8 AM</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Requests Card -->
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Total Requests</h6>
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-calendar-alt fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= $total_requests ?></h2>
                        <div class="d-flex align-items-center text-info small fw-medium">
                            <i class="fas fa-chart-line me-1"></i>
                            <span>15% growth this month</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Requests Table -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-4 px-4 align-items-center d-flex justify-content-between">
                <h5 class="fw-bold mb-0">Recent Requests</h5>
                <div class="d-flex gap-3">
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control bg-light border-0 shadow-none" placeholder="Search requests...">
                    </div>
                    <select id="statusFilter" class="form-select bg-light border-0 shadow-none" style="width: 150px;">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="requestsTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted fw-semibold">REQUEST ID</th>
                                <th class="py-3 text-muted fw-semibold">CHILD NAME</th>
                                <th class="py-3 text-muted fw-semibold">PARENT NAME</th>
                                <th class="py-3 text-muted fw-semibold">VACCINE</th>
                                <th class="py-3 text-muted fw-semibold">REQ. DATE</th>
                                <th class="py-3 text-muted fw-semibold">HOSPITAL</th>
                                <th class="py-3 text-muted fw-semibold text-center">STATUS</th>
                                <th class="pe-4 py-3 text-center text-muted fw-semibold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_requests->num_rows > 0): ?>
                            <?php while ($row = $result_requests->fetch_assoc()): 
                                $status_badge = '';
                                switch(strtolower($row['status'])) {
                                    case 'approved': $status_badge = 'bg-success bg-opacity-10 text-success'; break;
                                    case 'rejected': $status_badge = 'bg-danger bg-opacity-10 text-danger'; break;
                                    default: $status_badge = 'bg-warning bg-opacity-10 text-warning'; break;
                                }
                                
                                // Initials for avatar
                                $initials = strtoupper(substr($row['child_name'], 0, 2));
                                $avatar_colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                                $avatar_bg = $avatar_colors[array_rand($avatar_colors)];
                            ?>
                            <tr class="request-row" data-status="<?= strtolower($row['status']) ?>">
                                <td class="ps-4 fw-medium">#REQ-<?= $row['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle <?= $avatar_bg ?> bg-opacity-10 text-dark d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;"><?= $initials ?></div>
                                        <span class="search-target"><?= htmlspecialchars($row['child_name']) ?></span>
                                    </div>
                                </td>
                                <td class="search-target"><?= htmlspecialchars($row['parent_name']) ?></td>
                                <td class="search-target"><?= htmlspecialchars($row['vaccine_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($row['appointment_date'])) ?></td>
                                <td class="search-target"><?= htmlspecialchars($row['hospital_name']) ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?= $status_badge ?> px-3 py-2 fw-medium"><?= ucfirst($row['status']) ?></span>
                                </td>
                                <td class="pe-4 text-center">
                                    <?php if (strtolower($row['status']) === 'requested' || strtolower($row['status']) === 'pending'): ?>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-success rounded-3 px-3 shadow-none border-0" data-bs-toggle="modal" data-bs-target="#approveModal" onclick="setRequestId(<?= $row['id'] ?>)">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-danger rounded-3 px-3 shadow-none border-0" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="setRequestId(<?= $row['id'] ?>)">
                                            <i class="fas fa-times me-1"></i> Reject
                                        </button>
                                    </div>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-light text-muted rounded-3 px-3" disabled>
                                            <i class="fas fa-lock me-1"></i> <?= ucfirst($row['status']) ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center py-4 text-muted">No appointment requests found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination UI -->
            <div class="card-footer bg-white border-0 py-4 px-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-between align-items-center mb-0">
                        <li class="page-item disabled">
                            <span class="text-muted small">Showing all records</span>
                        </li>
                        <div class="d-flex gap-2">
                            <li class="page-item disabled"><a class="page-link border-0 bg-light rounded-3 px-3 text-dark" href="#"><i class="fas fa-chevron-left me-1"></i> Previous</a></li>
                            <li class="page-item active"><a class="page-link border-0 rounded-3 px-3" href="#">1</a></li>
                            <li class="page-item disabled"><a class="page-link border-0 bg-light rounded-3 px-3 text-dark" href="#">Next <i class="fas fa-chevron-right ms-1"></i></a></li>
                        </div>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</main>

<!-- Approve Confirmation Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-5 text-center">
                <div class="bg-success bg-opacity-10 text-success p-4 rounded-circle d-inline-block mb-4">
                    <i class="fas fa-check fs-1"></i>
                </div>
                <h4 class="fw-bold mb-3">Approve Request?</h4>
                <p class="text-muted mb-4">Are you sure you want to approve this appointment request? This will schedule the vaccination for the selected date.</p>
                <form method="POST">
                    <input type="hidden" name="request_id" id="approveRequestId">
                    <input type="hidden" name="approve_request_btn" value="1">
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 py-2 rounded-3">Confirm Approval</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-5 text-center">
                <div class="bg-danger bg-opacity-10 text-danger p-4 rounded-circle d-inline-block mb-4">
                    <i class="fas fa-times fs-1"></i>
                </div>
                <h4 class="fw-bold mb-3">Reject Request?</h4>
                <p class="text-muted mb-4">Are you sure you want to reject this appointment request? This action cannot be undone.</p>
                <form method="POST">
                    <input type="hidden" name="request_id" id="rejectRequestId">
                    <input type="hidden" name="reject_request_btn" value="1">
                    <div class="mb-4 text-start">
                        <label class="form-label small fw-semibold text-muted">Reason for Rejection</label>
                        <textarea class="form-control bg-light border-0 shadow-none" name="rejection_reason" rows="3" placeholder="Enter reason..."></textarea>
                    </div>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 py-2 rounded-3">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Helper to pass ID to modals (if you implement backend logic later)
function setRequestId(id) {
    // You can set this ID to a hidden input field in the modal form
    console.log("Selected Request ID: " + id);
    document.getElementById('approveRequestId').value = id;
    document.getElementById('rejectRequestId').value = id;
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.request-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status').toLowerCase();
            const textTargets = row.querySelectorAll('.search-target');
            let matchesSearch = false;

            textTargets.forEach(target => {
                if (target.textContent.toLowerCase().includes(searchTerm)) {
                    matchesSearch = true;
                }
            });

            // If it's a new row without .search-target, we skip or handle (IDs aren't in search-target)
            // But I've added .search-target to the relevant spans/tds.
            
            const matchesStatus = (statusValue === 'all' || rowStatus === statusValue);

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
});

function showAlert(type, message) {
    const placeholder = document.getElementById('alertPlaceholder');
    const wrapper = document.createElement('div');
    wrapper.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-3 fs-4"></i>
                <div>${message}</div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    placeholder.appendChild(wrapper);
    setTimeout(() => {
        const alert = bootstrap.Alert.getOrCreateInstance(wrapper.querySelector('.alert'));
        alert.close();
    }, 5000);
}
</script>

<?php include '../includes/footer.php'; ?>
