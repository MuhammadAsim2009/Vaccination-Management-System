<?php
// Include authentication and layout files
include '../../config/db.php';
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Fetch Parent ID
$parent_id = $_SESSION['user_id'];

// 1. Fetch Children for Filter
$children_list = [];
$stmt_ch = $conn->prepare("SELECT id, name FROM children WHERE parent_id = ?");
$stmt_ch->bind_param("i", $parent_id);
$stmt_ch->execute();
$res_ch = $stmt_ch->get_result();
while($row = $res_ch->fetch_assoc()) {
    $children_list[] = $row;
}

// 2. Fetch Upcoming Vaccinations
$upcoming_vaccines = [];
$stmt_vac = $conn->prepare("SELECT vs.id, vs.child_id, c.name as child_name, v.vaccine_name, vs.dose_number, vs.scheduled_date, h.hospital_name, vs.status 
                            FROM vaccination_schedule vs
                            JOIN children c ON vs.child_id = c.id
                            JOIN vaccines v ON vs.vaccine_id = v.id
                            LEFT JOIN hospitals h ON vs.hospital_id = h.id
                            WHERE c.parent_id = ? AND vs.status = 'pending'
                            ORDER BY vs.created_at ASC");
$stmt_vac->bind_param("i", $parent_id);
$stmt_vac->execute();
$res_vac = $stmt_vac->get_result();

$week_count = 0;
$next_due_text = "None";

while($row = $res_vac->fetch_assoc()) {
    // Calculate days left
    $due_date = new DateTime($row['scheduled_date']);
    $today = new DateTime();
    $today->setTime(0,0,0); // Reset time part for accurate day diff
    $due_date->setTime(0,0,0);
    
    $interval = $today->diff($due_date);
    $days_left = (int)$interval->format('%r%a');
    
    if($days_left >= 0 && $days_left <= 7) {
        $week_count++;
    }
    
    if($next_due_text === "None" && $days_left >= 0) {
        $next_due_text = $days_left . " days";
    }

    // Initials & Theme
    $initials = strtoupper(substr($row['child_name'], 0, 2));
    $themes = ['primary', 'success', 'danger', 'warning', 'info'];
    $theme = $themes[$row['child_id'] % 5];

    $upcoming_vaccines[] = [
        'id' => $row['id'],
        'child_id' => $row['child_id'],
        'child_name' => $row['child_name'],
        'child_initials' => $initials,
        'theme' => $theme,
        'vaccine' => $row['vaccine_name'],
        'dose' => 'Dose ' . $row['dose_number'],
        'due_date' => $row['scheduled_date'],
        'hospital' => $row['hospital_name'] ?? 'Not Assigned',
        'days_left' => $days_left,
        'reminder_status' => ($days_left <= 3) ? 'Sent' : 'Pending'
    ];
}
?>

<!-- Main Content Container -->
<div class="container-fluid px-4">

    <!-- 1️⃣ Page Header & Filters -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Upcoming Vaccination Dates</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item">Vaccination</li>
                    <li class="breadcrumb-item active" aria-current="page">Upcoming Dates</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <div class="d-flex gap-2 justify-content-md-end">
                <select class="form-select w-auto shadow-sm" id="filterChild">
                    <option value="all" selected>All Children</option>
                    <?php foreach($children_list as $child): ?>
                        <option value="<?= $child['id'] ?>"><?= htmlspecialchars($child['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-select w-auto shadow-sm" id="filterMonth">
                    <option value="all" selected>All Months</option>
                    <?php 
                    for($i=0; $i<6; $i++) {
                        $m_val = date('Y-m', strtotime("+$i months"));
                        $m_lbl = date('F Y', strtotime("+$i months"));
                        echo "<option value='$m_val'>$m_lbl</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <!-- 2️⃣ Reminder Alert Section -->
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center p-3 mb-4 rounded-4" role="alert" style="background: linear-gradient(to right, #fff3cd, #ffffff);">
        <div class="bg-warning bg-opacity-25 p-3 rounded-circle me-3 text-warning">
            <i class="fas fa-bell fa-lg"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="alert-heading fw-bold mb-1 text-dark">Upcoming Vaccinations This Week</h6>
            <p class="mb-0 text-muted small">You have <strong class="text-dark"><?= $week_count ?> vaccinations</strong> scheduled for this week. Please ensure you are prepared.</p>
        </div>
        <div class="text-end d-none d-sm-block">
            <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">
                <i class="fas fa-clock me-1"></i> Next in: <span id="mainCountdown"><?= $next_due_text ?></span>
            </span>
        </div>
    </div>

    <!-- 3️⃣ Upcoming Vaccination Cards (Grid) -->
    <div class="row g-4 mb-4">
        <?php if(empty($upcoming_vaccines)): ?>
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm rounded-4 text-center py-4">
                    <i class="fas fa-check-circle fa-2x mb-3 text-info"></i>
                    <h5 class="fw-bold">All Caught Up!</h5>
                    <p class="mb-0">No upcoming vaccinations found for your children.</p>
                </div>
            </div>
        <?php endif; ?>
        <?php foreach($upcoming_vaccines as $vaccine): ?>
        <div class="col-xl-4 col-md-6 filter-item" data-child-id="<?= $vaccine['child_id'] ?>" data-date="<?= date('Y-m-d', strtotime($vaccine['due_date'])) ?>">
            <div class="card border-0 shadow-sm h-100 hover-card rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-soft-<?= $vaccine['theme'] ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-weight: bold;">
                                <?= $vaccine['child_initials'] ?>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0"><?= $vaccine['child_name'] ?></h6>
                                <span class="text-muted small">Child ID: CH-00<?= $vaccine['id'] ?></span>
                            </div>
                        </div>
                        <span class="badge bg-soft-primary text-primary rounded-pill">Upcoming</span>
                    </div>
                    
                    <h5 class="fw-bold text-primary mb-1"><?= $vaccine['vaccine'] ?></h5>
                    <p class="text-muted small mb-3">
                        <span class="badge bg-light text-dark border me-1"><?= $vaccine['dose'] ?></span>
                    </p>

                    <div class="d-flex align-items-center text-muted small mb-2">
                        <i class="fas fa-calendar-alt me-2 text-secondary" style="width: 20px; text-align: center;"></i>
                        <span class="fw-medium text-dark"><?= date('l, M d, Y', strtotime($vaccine['due_date'])) ?></span>
                    </div>
                    <div class="d-flex align-items-center text-muted small mb-3">
                        <i class="fas fa-hospital me-2 text-secondary" style="width: 20px; text-align: center;"></i>
                        <span><?= $vaccine['hospital'] ?></span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <small class="<?= $vaccine['days_left'] < 0 ? 'text-danger' : 'text-warning' ?> fw-bold">
                            <?php if($vaccine['days_left'] < 0): ?>
                                <i class="fas fa-exclamation-circle me-1"></i> Overdue by <?= abs($vaccine['days_left']) ?> days
                            <?php elseif($vaccine['days_left'] == 0): ?>
                                <i class="fas fa-calendar-check me-1"></i> Due Today
                            <?php else: ?>
                                <i class="fas fa-hourglass-half me-1"></i> <?= $vaccine['days_left'] ?> days left
                            <?php endif; ?>
                        </small>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                            data-bs-toggle="modal" 
                            data-bs-target="#vaccineDetailsModal"
                            data-child="<?= $vaccine['child_name'] ?>"
                            data-vaccine="<?= $vaccine['vaccine'] ?>"
                            data-dose="<?= $vaccine['dose'] ?>"
                            data-date="<?= date('M d, Y', strtotime($vaccine['due_date'])) ?>"
                            data-hospital="<?= $vaccine['hospital'] ?>"
                            data-status="<?= $vaccine['reminder_status'] ?>"
                        >Details</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        
        <!-- 4️⃣ Upcoming Vaccination Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-list-ul me-2 text-primary"></i>Detailed Schedule
                    </h6>
                    <div class="input-group" style="max-width: 250px;">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 form-control-sm" id="filterSearch" placeholder="Search...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-secondary small text-uppercase">Child</th>
                                    <th class="px-4 py-3 text-secondary small text-uppercase">Vaccine</th>
                                    <th class="px-4 py-3 text-secondary small text-uppercase">Date</th>
                                    <th class="px-4 py-3 text-secondary small text-uppercase">Reminder</th>
                                    <th class="px-4 py-3 text-secondary small text-uppercase text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($upcoming_vaccines as $row): ?>
                                <tr class="filter-item" data-child-id="<?= $row['child_id'] ?>" data-date="<?= date('Y-m-d', strtotime($row['due_date'])) ?>">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-soft-<?= $row['theme'] ?> rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 10px;">
                                                <?= $row['child_initials'] ?>
                                            </div>
                                            <span class="fw-medium small"><?= $row['child_name'] ?></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="small fw-bold text-dark"><?= $row['vaccine'] ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= $row['dose'] ?></div>
                                    </td>
                                    <td class="px-4 py-3 small text-dark fw-medium">
                                        <?= date('M d, Y', strtotime($row['due_date'])) ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if($row['reminder_status'] == 'Sent'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill">Sent</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <button class="btn btn-sm btn-light text-primary border"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#vaccineDetailsModal"
                                            data-child="<?= $row['child_name'] ?>"
                                            data-vaccine="<?= $row['vaccine'] ?>"
                                            data-dose="<?= $row['dose'] ?>"
                                            data-date="<?= date('M d, Y', strtotime($row['due_date'])) ?>"
                                            data-hospital="<?= $row['hospital'] ?>"
                                            data-status="<?= $row['reminder_status'] ?>"
                                        ><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-end mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Right Column: Calendar & Settings -->
        <div class="col-lg-4">
            
            <!-- 5️⃣ Calendar View Section (UI Only) -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt me-2 text-info"></i><?= date('F Y') ?></h6>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-light border" disabled><i class="fas fa-chevron-left small"></i></button>
                            <button class="btn btn-light border" disabled><i class="fas fa-chevron-right small"></i></button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <!-- Simple Calendar Grid -->
                    <div class="d-grid text-center mb-2" style="grid-template-columns: repeat(7, 1fr); gap: 5px;">
                        <small class="text-muted fw-bold">S</small>
                        <small class="text-muted fw-bold">M</small>
                        <small class="text-muted fw-bold">T</small>
                        <small class="text-muted fw-bold">W</small>
                        <small class="text-muted fw-bold">T</small>
                        <small class="text-muted fw-bold">F</small>
                        <small class="text-muted fw-bold">S</small>
                    </div>
                    <div class="d-grid text-center" style="grid-template-columns: repeat(7, 1fr); gap: 5px;">
                        <?php
                        $days_in_month = date('t');
                        $start_offset = date('w', strtotime(date('Y-m-01')));
                        
                        // Empty slots
                        for($i=0; $i<$start_offset; $i++) echo '<div class="p-2"></div>';
                        
                        // Days
                        for($d=1; $d<=$days_in_month; $d++) {
                            $current_date = date('Y-m-') . str_pad($d, 2, '0', STR_PAD_LEFT);
                            $highlight_class = 'hover-bg-light';
                            $tooltip = '';
                            $has_event = false;
                            
                            foreach($upcoming_vaccines as $v) {
                                if(date('Y-m-d', strtotime($v['due_date'])) == $current_date) {
                                    $highlight_class = 'bg-' . $v['theme'] . ' text-white fw-bold shadow-sm';
                                    $tooltip = $v['vaccine'] . ' - ' . $v['child_name'];
                                    $has_event = true;
                                    break; // Only highlight first event for simplicity in this grid
                                }
                            }
                            
                            echo '<div class="p-2 small rounded '.$highlight_class.' position-relative" '.($has_event ? 'data-bs-toggle="tooltip" title="'.htmlspecialchars($tooltip).'"' : '').'>';
                            echo $d;
                            if($has_event) {
                                echo '<span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>';
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <div class="mt-3 d-flex justify-content-center gap-3 small">
                        <div class="text-muted fst-italic">Hover over highlighted dates for details</div>
                    </div>
                </div>
            </div>

            <!-- 6️⃣ Notification Settings Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-cog me-2 text-secondary"></i>Notification Settings</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-semibold text-dark" for="emailNotif">Email Notifications</label>
                                <div class="text-muted small">Receive updates via email</div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                            </div>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-semibold text-dark" for="smsNotif">SMS Alerts</label>
                                <div class="text-muted small">Receive updates via SMS</div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="smsNotif">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Remind me before</label>
                            <select class="form-select form-select-sm">
                                <option value="1">1 Day</option>
                                <option value="3" selected>3 Days</option>
                                <option value="7">1 Week</option>
                            </select>
                        </div>

                        <button type="button" class="btn btn-primary btn-sm w-100 rounded-pill">Save Preferences</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Vaccine Details Modal -->
<div class="modal fade" id="vaccineDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Vaccination Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="fas fa-syringe fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-1" id="modalVaccineName"></h5>
                    <span class="badge bg-light text-dark border" id="modalDose"></span>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Child Name</label>
                        <div class="fw-medium" id="modalChildName"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold text-uppercase">Due Date</label>
                        <div class="fw-medium text-primary" id="modalDate"></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Hospital</label>
                        <div class="fw-medium" id="modalHospital"></div>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold text-uppercase">Reminder Status</label>
                        <div id="modalStatus"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary rounded-pill px-4">Reschedule</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Dummy Countdown Logic for Alert Banner
    const countdownElement = document.getElementById('mainCountdown');
    let days = 2;
    
    setInterval(() => {
 
    }, 1000);

    // Settings Save Button Interaction
    const saveBtn = document.querySelector('.btn-primary.w-100');
    saveBtn.addEventListener('click', function() {
        const originalText = this.innerText;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        this.disabled = true;
        
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-check me-2"></i>Saved';
            this.classList.replace('btn-primary', 'btn-success');
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.replace('btn-success', 'btn-primary');
                this.disabled = false;
            }, 2000);
        }, 1000);
    });

    // Filter Functionality
    const filterChild = document.getElementById('filterChild');
    const filterMonth = document.getElementById('filterMonth');
    const filterSearch = document.getElementById('filterSearch');
    const filterItems = document.querySelectorAll('.filter-item');

    function applyFilters() {
        const childVal = filterChild.value;
        const monthVal = filterMonth.value;
        const searchVal = filterSearch.value.toLowerCase();

        filterItems.forEach(item => {
            const itemChildId = item.getAttribute('data-child-id');
            const itemDate = item.getAttribute('data-date'); // YYYY-MM-DD
            const itemText = item.innerText.toLowerCase();
            const isTableRow = item.tagName === 'TR';

            let isVisible = true;

            // Global Filters (Child & Month)
            if (childVal !== 'all' && itemChildId !== childVal) isVisible = false;
            if (isVisible && monthVal !== 'all' && !itemDate.startsWith(monthVal)) isVisible = false;

            // Table-Specific Search
            if (isVisible && isTableRow && searchVal !== '') {
                if (!itemText.includes(searchVal)) isVisible = false;
            }

            item.style.display = isVisible ? '' : 'none';
        });
    }

    // Event Listeners for Filters
    filterChild.addEventListener('change', applyFilters);
    filterMonth.addEventListener('change', applyFilters);
    filterSearch.addEventListener('keyup', applyFilters);

    // Modal Population Logic
    const vaccineModal = document.getElementById('vaccineDetailsModal');
    if (vaccineModal) {
        vaccineModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const child = button.getAttribute('data-child');
            const vaccine = button.getAttribute('data-vaccine');
            const dose = button.getAttribute('data-dose');
            const date = button.getAttribute('data-date');
            const hospital = button.getAttribute('data-hospital');
            const status = button.getAttribute('data-status');

            vaccineModal.querySelector('#modalChildName').textContent = child;
            vaccineModal.querySelector('#modalVaccineName').textContent = vaccine;
            vaccineModal.querySelector('#modalDose').textContent = dose;
            vaccineModal.querySelector('#modalDate').textContent = date;
            vaccineModal.querySelector('#modalHospital').textContent = hospital;
            
            const statusContainer = vaccineModal.querySelector('#modalStatus');
            if(status === 'Sent') {
                statusContainer.innerHTML = '<span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill">Reminder Sent</span>';
            } else {
                statusContainer.innerHTML = '<span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill">Pending</span>';
            }
        });
    }
});
</script>

<style>
    /* Local Styles for Calendar Hover */
    .hover-bg-light:hover {
        background-color: #f8f9fa;
        cursor: pointer;
        font-weight: bold;
        color: var(--primary-color);
    }
</style>

<?php include '../includes/footer.php'; ?>
