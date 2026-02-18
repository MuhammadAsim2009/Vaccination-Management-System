<?php
// Include authentication and layout files
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Check if hospital_id is provided in the URL
if (!isset($_GET['hospital_id'])) {
    echo "<div class='alert alert-danger'>No hospital selected. Please go back and select a hospital.</div>";
    include '../includes/footer.php';
    exit();
}

// Fetch user ID from session
$parent_id = $_SESSION['user_id'];

// Get hospital ID from URL
$hospital_id = $_GET['hospital_id'];

// Fetch hospital details
$stmt_hospital = $conn->prepare("SELECT h.id, h.hospital_name, h.address, h.phone FROM hospitals h WHERE h.id = ?");
$stmt_hospital->bind_param("i", $hospital_id);
$stmt_hospital->execute();
$hospital_results = $stmt_hospital->get_result();

// Check if hospital exists
if ($hospital_results->num_rows === 0) {
    echo "<div class='alert alert-danger'>Hospital not found. Please go back and select a valid hospital.</div>";
    include '../includes/footer.php';
    exit();
}

// Hospital info array
$hospital_info = $hospital_results->fetch_assoc();

$hospital_info = [
    'hospital_id' => $hospital_info['id'],
    'name' => $hospital_info['hospital_name'],
    'address' => $hospital_info['address'],
    'contact' => $hospital_info['phone'],
];


// Fetch children data
$stmt_children = $conn->prepare("SELECT c.id, c.name, c.date_of_birth, c.gender, c.blood_group FROM children c WHERE c.parent_id = ?");
$stmt_children->bind_param("i", $parent_id);
$stmt_children->execute();
$children_results = $stmt_children->get_result();

// Children array
$children_list = [];
while ($child = $children_results->fetch_assoc()) {
    // Calculate age from date_of_birth
    $dob = new DateTime($child['date_of_birth']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    $children_list[] = [
        'id' => $child['id'],
        'name' => $child['name'],
        'age' => $age,
        'gender' => $child['gender'],
        'blood_group' => $child['blood_group'],
        'avatar' => "https://ui-avatars.com/api/?name=" . urlencode($child['name']) . "&background=" . ($child['gender'] === 'Male' ? 'e3f2fd' : 'fce4ec') . "&color=" . ($child['gender'] === 'Male' ? '1a237e' : 'ad1457') . "&bold=true"
    ];
}


// Fetch vaccine data
$stmt_vaccines = $conn->prepare("SELECT id, vaccine_name, total_dose FROM vaccines");
$stmt_vaccines->execute();
$vaccines_results = $stmt_vaccines->get_result();

// Vaccines array
$vaccines = [];
while ($vaccine = $vaccines_results->fetch_assoc()) {
    $vaccines[$vaccine['id']] = [
        'name' => $vaccine['vaccine_name'],
        'total_dose' => $vaccine['total_dose']
    ];
}


// Handle form submission
$booking_success = false;
if(isset($_POST['confirm_btn'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch parent_id from parents table
    $stmt_pid = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
    $stmt_pid->bind_param("i", $user_id);
    $stmt_pid->execute();
    $res_pid = $stmt_pid->get_result();
    $row_pid = $res_pid->fetch_assoc();
    $parent_id = $row_pid['id'];

    // Get form data
    $child_id = $_POST['child_id'];
    $hospital_id = $_POST['hospital_id'];
    $vaccine_id = $_POST['vaccine_id'];
    $dose_number = $_POST['dose_number'];
    $appt_date = $_POST['appt_date'];
    $appt_time = $_POST['appt_time'];

    // Combine date and time into a variable (required for bind_param reference)
    $appointment_datetime = date('Y-m-d H:i:s', strtotime("$appt_date $appt_time"));

    // Insert appointment into database
    $stmt_insert = $conn->prepare("INSERT INTO appointments (parent_id, hospital_id, child_id, vaccine_id, dose_number, appointment_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("iiiiis", $parent_id, $hospital_id, $child_id, $vaccine_id, $dose_number, $appointment_datetime);
    if ($stmt_insert->execute()) {
        $booking_success = true;
    } else {
        echo "<div class='alert alert-danger'>Error booking appointment: " . $conn->error . "</div>";
    }
}

?>

<!-- Main Content Container -->
<div class="container-fluid px-4">

    <!-- 1️⃣ Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Book Vaccination</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item">Booking</li>
                    <li class="breadcrumb-item active" aria-current="page">Book Vaccination</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="search_hospital.php" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Search
            </a>
        </div>
    </div>

    <div class="row g-4">
        
        <!-- Left Column: Booking Form -->
        <div class="col-lg-8">
            <form id="bookingForm" method="POST" class="needs-validation" novalidate>
                
                <!-- 2️⃣ Child Selection Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-child me-2"></i>Select Child
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="childSelect" class="form-label fw-semibold">Choose Child for Vaccination <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" name="child_id" id="childSelect" required>
                                <option value="" selected disabled>Select a child...</option>
                                <?php foreach($children_list as $child): ?>
                                    <option value="<?= $child['id'] ?>" 
                                        data-age="<?= $child['age'] ?>" 
                                        data-gender="<?= $child['gender'] ?>" 
                                        data-blood="<?= $child['blood_group'] ?>"
                                        data-avatar="<?= $child['avatar'] ?>">
                                        <?= $child['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a child.</div>
                        </div>

                        <!-- Dynamic Child Info Card (Hidden initially) -->
                        <div id="childInfoCard" class="d-none bg-light p-3 rounded-3 border">
                            <div class="d-flex align-items-center">
                                <img src="" id="childAvatar" class="rounded-circle me-3 border border-white shadow-sm" width="60" height="60" alt="Child">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" id="childNameDisplay">Child Name</h6>
                                    <div class="d-flex gap-3 text-muted small">
                                        <span><i class="fas fa-birthday-cake me-1"></i> <span id="childAge"></span></span>
                                        <span><i class="fas fa-venus-mars me-1"></i> <span id="childGender"></span></span>
                                        <span><i class="fas fa-tint me-1 text-danger"></i> <span id="childBlood"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3️⃣ Hospital & Vaccine Info -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-success">
                            <i class="fas fa-hospital-alt me-2"></i>Hospital & Vaccine Details
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Hospital Details (Readonly) -->
                        <div class="d-flex align-items-start mb-4 p-3 bg-soft-success rounded-3 border border-success border-opacity-10">
                            <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-success">
                                <i class="fas fa-hospital fa-lg"></i>
                            </div>
                            <div>
                                <input type="hidden" name="hospital_id" value="<?= $hospital_info['hospital_id'] ?>">
                                <h6 class="fw-bold text-dark mb-1"><?= $hospital_info['name'] ?></h6>
                                <p class="mb-1 small text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?= $hospital_info['address'] ?></p>
                                <p class="mb-0 small text-muted"><i class="fas fa-phone-alt me-1"></i> <?= $hospital_info['contact'] ?></p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="vaccineSelect" class="form-label fw-semibold">Select Vaccine <span class="text-danger">*</span></label>
                                <select class="form-select" name="vaccine_id" id="vaccineSelect" required>
                                    <option value="" selected disabled>Choose vaccine...</option>
                                    <?php foreach($vaccines as $id => $info): ?>
                                        <option value="<?= $id ?>" data-dose="<?= $info['total_dose'] ?>"><?= $info['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a vaccine.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="doseSelect" class="form-label fw-semibold">Dose Number <span class="text-danger">*</span></label>
                                <select class="form-select" name="dose_number" id="doseSelect" required disabled>
                                    <option value="" selected disabled>Select vaccine first</option>
                                </select>
                                <div class="invalid-feedback">Please select the dose number.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4️⃣ Appointment Schedule -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-warning">
                            <i class="fas fa-calendar-alt me-2"></i>Schedule Appointment
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="apptDate" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="appt_date" id="apptDate" required min="<?= date('Y-m-d') ?>">
                                <div class="invalid-feedback">Please select a valid date.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="apptTime" class="form-label fw-semibold">Time Slot <span class="text-danger">*</span></label>
                                <select class="form-select" name="appt_time" id="apptTime" required>
                                    <option value="" selected disabled>Select time...</option>
                                    <option value="09:00 AM">09:00 AM - 10:00 AM</option>
                                    <option value="10:00 AM">10:00 AM - 11:00 AM</option>
                                    <option value="11:00 AM">11:00 AM - 12:00 PM</option>
                                    <option value="02:00 PM">02:00 PM - 03:00 PM</option>
                                    <option value="03:00 PM">03:00 PM - 04:00 PM</option>
                                    <option value="04:00 PM">04:00 PM - 05:00 PM</option>
                                </select>
                                <div class="invalid-feedback">Please select a time slot.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="notes" rows="2" placeholder="Any allergies or special requests..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-3 mb-5">
                    <a href="search_hospital.php" class="btn btn-light border btn-lg flex-fill rounded-pill">Cancel</a>
                    <button type="submit" name="confirm_btn" class="btn btn-primary btn-lg flex-fill rounded-pill shadow-sm" id="btnConfirm">
                        Confirm Booking
                    </button>
                </div>

            </form>
        </div>

        <!-- Right Column: 5️⃣ Booking Summary Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 900;">
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-clipboard-list me-2"></i>Booking Summary</h6>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Child Summary -->
                    <div class="mb-3 border-bottom pb-3">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Child</small>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="fw-bold text-dark" id="summaryChild">Not Selected</span>
                            <i class="fas fa-check-circle text-muted" id="iconChild"></i>
                        </div>
                    </div>

                    <!-- Hospital Summary -->
                    <div class="mb-3 border-bottom pb-3">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Hospital</small>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="fw-bold text-dark"><?= $hospital_info['name'] ?></span>
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>

                    <!-- Vaccine Summary -->
                    <div class="mb-3 border-bottom pb-3">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Vaccine</small>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="fw-bold text-dark" id="summaryVaccine">Not Selected</span>
                            <i class="fas fa-check-circle text-muted" id="iconVaccine"></i>
                        </div>
                        <small class="text-muted d-block mt-1" id="summaryDose">-</small>
                    </div>

                    <!-- Date & Time Summary -->
                    <div class="mb-4">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Date & Time</small>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="fw-bold text-dark" id="summaryDateTime">Not Selected</span>
                            <i class="fas fa-check-circle text-muted" id="iconTime"></i>
                        </div>
                    </div>

                    <!-- Total Cost -->
                    <div class="alert alert-success border-0 bg-soft-success d-flex justify-content-between align-items-center mb-0">
                        <span class="fw-bold small">Total Cost</span>
                        <span class="fw-bold">Free</span>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Success Modal (Hidden by default) -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-check fa-3x"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-2">Booking Confirmed!</h4>
                <p class="text-muted mb-4">Your vaccination appointment has been successfully scheduled. You will receive a confirmation SMS shortly.</p>
                <div class="d-grid gap-2">
                    <a href="../dashboard.php" class="btn btn-primary rounded-pill">Go to Dashboard</a>
                    <a href="search_hospital.php" class="btn btn-light rounded-pill">Book Another</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Elements
    const childSelect = document.getElementById('childSelect');
    const childInfoCard = document.getElementById('childInfoCard');
    const vaccineSelect = document.getElementById('vaccineSelect');
    const doseSelect = document.getElementById('doseSelect');
    const apptDate = document.getElementById('apptDate');
    const apptTime = document.getElementById('apptTime');
    const form = document.getElementById('bookingForm');
    const btnConfirm = document.getElementById('btnConfirm');

    // Summary Elements
    const summaryChild = document.getElementById('summaryChild');
    const summaryVaccine = document.getElementById('summaryVaccine');
    const summaryDose = document.getElementById('summaryDose');
    const summaryDateTime = document.getElementById('summaryDateTime');
    
    // Icons
    const iconChild = document.getElementById('iconChild');
    const iconVaccine = document.getElementById('iconVaccine');
    const iconTime = document.getElementById('iconTime');

    // 1. Handle Child Selection
    childSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            // Update Info Card
            document.getElementById('childNameDisplay').textContent = selectedOption.text;
            document.getElementById('childAge').textContent = selectedOption.getAttribute('data-age');
            document.getElementById('childGender').textContent = selectedOption.getAttribute('data-gender');
            document.getElementById('childBlood').textContent = selectedOption.getAttribute('data-blood');
            document.getElementById('childAvatar').src = selectedOption.getAttribute('data-avatar');
            
            childInfoCard.classList.remove('d-none');

            // Update Summary
            summaryChild.textContent = selectedOption.text;
            iconChild.classList.replace('text-muted', 'text-success');
        }
    });

    // 2. Handle Vaccine Selection
    vaccineSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        // Reset and disable dose dropdown
        doseSelect.innerHTML = '<option value="" selected disabled>Select vaccine first</option>';
        doseSelect.disabled = true;
        summaryDose.textContent = '-'; // Reset summary text

        if (selectedOption.value) {
            // Update Summary
            summaryVaccine.textContent = selectedOption.text;
            iconVaccine.classList.replace('text-muted', 'text-success');

            // Populate Dose Dropdown
            const totalDoses = parseInt(selectedOption.getAttribute('data-dose'), 10);
            if (totalDoses > 0) {
                doseSelect.innerHTML = '<option value="" selected disabled>Select dose...</option>';
                for (let i = 1; i <= totalDoses; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = `Dose ${i}`;
                    doseSelect.appendChild(option);
                }
                doseSelect.disabled = false;
            } else {
                doseSelect.innerHTML = '<option value="" selected disabled>Not applicable</option>';
            }
        }
    });

    // Handle Dose Selection
    doseSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        summaryDose.textContent = selectedOption.value ? selectedOption.text : '-';
    });

    // 3. Handle Date/Time Change
    function updateDateTimeSummary() {
        if (apptDate.value && apptTime.value) {
            const dateObj = new Date(apptDate.value);
            const dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            summaryDateTime.textContent = `${dateStr} at ${apptTime.value}`;
            iconTime.classList.replace('text-muted', 'text-success');
        }
    }
    apptDate.addEventListener('change', updateDateTimeSummary);
    apptTime.addEventListener('change', updateDateTimeSummary);

    // 4. Handle Form Submission
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        }

        form.classList.add('was-validated');
    });

    <?php if($booking_success): ?>
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
    <?php endif; ?>
});
</script>

<?php include '../includes/footer.php'; ?>
