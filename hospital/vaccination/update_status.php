<?php
ob_start(); // Start output buffering to prevent header errors
include '../../config/db.php';
include '../includes/auth_check.php';

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

// Handle AJAX POST Request for Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_clean(); // Clear any previous output to ensure valid JSON
    header('Content-Type: application/json');
    
    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $schedule_id = isset($input['id']) ? intval($input['id']) : 0;
    $status = isset($input['status']) ? $input['status'] : '';
    $notes = isset($input['notes']) ? trim($input['notes']) : ''; 
    $vaccination_date = !empty($input['date']) ? $input['date'] : date('Y-m-d');

    if (!$schedule_id || !$status || !$hospital_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }

    if ($status === 'vaccinated' && empty($notes)) {
        echo json_encode(['success' => false, 'message' => 'Remarks are required when status is Vaccinated.']);
        exit;
    }

    // Verify that this schedule belongs to the logged-in hospital
    $check_stmt = $conn->prepare("SELECT id, child_id, vaccine_id, dose_number FROM vaccination_schedule WHERE id = ? AND hospital_id = ?");
    $check_stmt->bind_param("ii", $schedule_id, $hospital_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Record not found or access denied.']);
        exit;
    }
    $schedule_data = $check_result->fetch_assoc();

    // Update Status
    $update_stmt = $conn->prepare("UPDATE vaccination_schedule SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $status, $schedule_id);
    
    if ($update_stmt->execute()) {
        // If status is vaccinated, update the corresponding appointment to completed
        if ($status === 'vaccinated') {
            // Find the appointment first
            $find_appt = $conn->prepare("SELECT id FROM appointments WHERE child_id = ? AND vaccine_id = ? AND hospital_id = ? AND dose_number = ? AND status = 'approved'");
            $find_appt->bind_param("iiii", $schedule_data['child_id'], $schedule_data['vaccine_id'], $hospital_id, $schedule_data['dose_number']);
            $find_appt->execute();
            $appt_res = $find_appt->get_result();

            if ($appt_res->num_rows > 0) {
                $appt_row = $appt_res->fetch_assoc();
                $appointment_id = $appt_row['id'];

                // Update appointment status
                $appt_stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
                $appt_stmt->bind_param("i", $appointment_id);
                $appt_stmt->execute();

                // Insert into vaccination_records
                $rec_stmt = $conn->prepare("INSERT INTO vaccination_records (appointment_id, vaccinated_date, remarks) VALUES (?, ?, ?)");
                $rec_stmt->bind_param("iss", $appointment_id, $vaccination_date, $notes);
                $rec_stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
    exit;
}

// Fetch Schedule Details for Display
$schedule_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = null;

if ($schedule_id && $hospital_id) {
    $sql = "SELECT vs.id AS schedule_id, vs.scheduled_date, vs.status, vs.dose_number,
                   v.vaccine_name,
                   c.name AS child_name, c.date_of_birth, c.gender, c.blood_group, c.id AS child_id,
                   u.name AS parent_name, u.email AS parent_email,
                   p.phone AS parent_phone, p.address AS parent_address
            FROM vaccination_schedule vs
            JOIN children c ON vs.child_id = c.id
            JOIN users u ON c.parent_id = u.id
            LEFT JOIN parents p ON u.id = p.user_id
            JOIN vaccines v ON vs.vaccine_id = v.id
            WHERE vs.id = ? AND vs.hospital_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $schedule_id, $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    }
}

if (!$data) {
    // Redirect if invalid ID or not found
    header("Location: appointments.php");
    exit;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<!-- Main Content -->
<main class="mt-5 pt-3 update-status-page">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item">Vaccination</li>
                        <li class="breadcrumb-item active" aria-current="page">Update Status</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary mb-1">Update Vaccination Status</h2>
                <p class="text-muted mb-0">Update child vaccination appointment status</p>
            </div>
        </div>

        <!-- Simulated Alert Feedback (UI-only) -->
        <div class="row mb-4">
            <div class="col-12">
                <div id="statusSuccessAlert" class="alert alert-success border-0 shadow-sm rounded-4 d-none" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-circle-check me-2"></i>
                        <span id="statusSuccessMessage">Status updated successfully.</span>
                    </div>
                </div>
                <div id="statusErrorAlert" class="alert alert-danger border-0 shadow-sm rounded-4 d-none" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-circle-exclamation me-2"></i>
                        <span id="statusErrorMessage">Unable to update status. Please check form inputs.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <!-- Appointment Summary Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-file-medical me-2 text-primary"></i>Appointment Summary</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Appointment ID</p>
                                    <p class="fw-semibold mb-0">SCH-<?= $data['schedule_id'] ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Parent Name</p>
                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($data['parent_name']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Child Name</p>
                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($data['child_name']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Vaccine Name</p>
                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($data['vaccine_name']) ?> - Dose <?= $data['dose_number'] ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Appointment Date & Time</p>
                                    <p class="fw-semibold mb-0"><?= date('F d, Y - h:i A', strtotime($data['scheduled_date'])) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-item">
                                    <p class="small text-uppercase text-muted fw-semibold mb-1">Current Status</p>
                                    <?php
                                        $statusClass = 'bg-warning-subtle text-warning-emphasis';
                                        if ($data['status'] == 'vaccinated') $statusClass = 'bg-success-subtle text-success-emphasis';
                                        elseif ($data['status'] == 'missed') $statusClass = 'bg-danger-subtle text-danger-emphasis';
                                    ?>
                                    <span id="currentStatusBadge" class="badge rounded-pill px-3 py-2 <?= $statusClass ?>"><?= ucfirst($data['status']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Status Form -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-pen-to-square me-2 text-success"></i>Update Status Form</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <form id="statusUpdateForm" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="appointmentStatus" class="form-label fw-semibold">Status</label>
                                    <select id="appointmentStatus" class="form-select" required>
                                        <option value="" disabled>Select status</option>
                                        <option value="pending" <?= $data['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="vaccinated" <?= $data['status'] == 'vaccinated' ? 'selected' : '' ?>>Vaccinated</option>
                                        <option value="missed" <?= $data['status'] == 'missed' ? 'selected' : '' ?>>Missed</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="vaccinationDate" class="form-label fw-semibold">Vaccination Date</label>
                                    <input type="date" id="vaccinationDate" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="doctorNotes" class="form-label fw-semibold">Remarks</label>
                                    <textarea id="doctorNotes" class="form-control" rows="4" placeholder="Enter notes about vaccination status..."></textarea>
                                </div>
                                <div class="col-12 d-flex flex-wrap gap-2 pt-1">
                                    <button type="submit" id="updateStatusBtn" class="btn btn-success px-4">
                                        <i class="fas fa-floppy-disk me-2"></i>Update Status
                                    </button>
                                    <button type="reset" id="resetStatusBtn" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-rotate-left me-2"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Panel -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body px-4 pb-4 d-grid gap-3">
                        <button type="button" onclick="window.print()" class="btn btn-outline-primary quick-action-btn text-start">
                            <i class="fas fa-print me-2"></i>Print Vaccination Record
                        </button>
                        <button type="button" class="btn btn-outline-success quick-action-btn text-start" data-bs-toggle="modal" data-bs-target="#viewChildModal">
                            <i class="fas fa-child-reaching me-2"></i>View Child Profile
                        </button>
                        <button type="button" onclick="window.location.href='appointments.php'" class="btn btn-outline-secondary quick-action-btn text-start">
                            <i class="fas fa-arrow-left me-2"></i>Back to Appointments
                        </button>
                    </div>
                </div>

                <!-- Status Guide -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-shield-heart me-2 text-success"></i>Status Guide</h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-success-subtle text-success-emphasis me-2">Vaccinated</span>
                            <small class="text-muted">Vaccination given successfully</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis me-2">Pending</span>
                            <small class="text-muted">Awaiting appointment completion</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis me-2">Missed</span>
                            <small class="text-muted">Child did not attend appointment</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Child Detail Modal -->
<div class="modal fade" id="viewChildModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Child Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-lg bg-soft-info text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-baby fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($data['child_name']) ?></h5>
                    <p class="text-muted small">Child ID: #<?= $data['child_id'] ?></p>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Date of Birth</label>
                        <div class="fw-medium"><?= date('M d, Y', strtotime($data['date_of_birth'])) ?></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Gender</label>
                        <div class="fw-medium"><?= ucfirst($data['gender']) ?></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Blood Group</label>
                        <div class="fw-medium"><?= $data['blood_group'] ? $data['blood_group'] : 'N/A' ?></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Parent/Guardian</label>
                        <div class="fw-medium"><?= htmlspecialchars($data['parent_name']) ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($data['parent_phone']) ?></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Address</label>
                        <div class="fw-medium"><?= htmlspecialchars($data['parent_address']) ?></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary rounded-pill px-4">View Full History</button>
            </div>
        </div>
    </div>
</div>

<!-- Shared frontend interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('statusUpdateForm');
    const updateBtn = document.getElementById('updateStatusBtn');
    const successAlert = document.getElementById('statusSuccessAlert');
    const errorAlert = document.getElementById('statusErrorAlert');
    const successMsg = document.getElementById('statusSuccessMessage');
    const errorMsg = document.getElementById('statusErrorMessage');
    const statusBadge = document.getElementById('currentStatusBadge');
    const statusSelect = document.getElementById('appointmentStatus');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const notesValue = document.getElementById('doctorNotes').value.trim();

        if (statusSelect.value === 'vaccinated' && !notesValue) {
            errorMsg.textContent = 'Remarks are required when marking as Vaccinated.';
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
            return;
        }

        // Disable button
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

        const payload = {
            id: <?= $schedule_id ?>,
            status: statusSelect.value,
            notes: notesValue,
            date: document.getElementById('vaccinationDate').value
        };

        fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            updateBtn.disabled = false;
            updateBtn.innerHTML = '<i class="fas fa-floppy-disk me-2"></i>Update Status';

            if (data.success) {
                successMsg.textContent = data.message;
                successAlert.classList.remove('d-none');
                errorAlert.classList.add('d-none');
                
                // Update badge visually
                statusBadge.textContent = statusSelect.options[statusSelect.selectedIndex].text;
                
                // Update badge color class dynamically
                const statusClasses = {
                    'pending': 'bg-warning-subtle text-warning-emphasis',
                    'vaccinated': 'bg-success-subtle text-success-emphasis',
                    'missed': 'bg-danger-subtle text-danger-emphasis'
                };
                statusBadge.className = `badge rounded-pill px-3 py-2 ${statusClasses[statusSelect.value] || ''}`;
            } else {
                errorMsg.textContent = data.message;
                errorAlert.classList.remove('d-none');
                successAlert.classList.add('d-none');
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            updateBtn.disabled = false;
            updateBtn.innerHTML = '<i class="fas fa-floppy-disk me-2"></i>Update Status';
            errorMsg.textContent = 'An error occurred. Please try again.';
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
