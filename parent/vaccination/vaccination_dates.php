<?php
/**
 * Upcoming Vaccination Dates
 * Parent Panel
 * 
 * Displays upcoming vaccination schedules, reminders, calendar view,
 * and notification settings for parents.
 */

// Include authentication and layout files
include '../includes/auth_check.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Dummy Data for UI Simulation
$upcoming_vaccines = [
    [
        'id' => 1,
        'child_id' => 1,
        'child_name' => 'Sarah Ahmed',
        'child_initials' => 'SA',
        'theme' => 'primary',
        'vaccine' => 'MMR Vaccine',
        'dose' => '2nd Dose',
        'due_date' => '2026-02-12',
        'hospital' => 'City General Hospital',
        'days_left' => 2,
        'reminder_status' => 'Sent'
    ],
    [
        'id' => 2,
        'child_id' => 2,
        'child_name' => 'Ahmed Ali',
        'child_initials' => 'AA',
        'theme' => 'success',
        'vaccine' => 'Polio (OPV)',
        'dose' => 'Booster',
        'due_date' => '2026-02-15',
        'hospital' => 'Metro Health Center',
        'days_left' => 5,
        'reminder_status' => 'Pending'
    ],
    [
        'id' => 3,
        'child_id' => 3,
        'child_name' => 'Fatima Hassan',
        'child_initials' => 'FH',
        'theme' => 'danger',
        'vaccine' => 'Hepatitis B',
        'dose' => '3rd Dose',
        'due_date' => '2026-02-20',
        'hospital' => 'Children\'s Hospital',
        'days_left' => 10,
        'reminder_status' => 'Pending'
    ]
];
?>

<!-- Main Content Container -->
<div class="container-fluid px-4 py-4">

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
                    <option value="1">Sarah Ahmed</option>
                    <option value="2">Ahmed Ali</option>
                    <option value="3">Fatima Hassan</option>
                </select>
                <select class="form-select w-auto shadow-sm" id="filterMonth">
                    <option value="all" selected>All Months</option>
                    <option value="2026-02">February 2026</option>
                    <option value="2026-03">March 2026</option>
                    <option value="2026-04">April 2026</option>
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
            <p class="mb-0 text-muted small">You have <strong class="text-dark">2 vaccinations</strong> scheduled for this week. Please ensure you are prepared.</p>
        </div>
        <div class="text-end d-none d-sm-block">
            <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">
                <i class="fas fa-clock me-1"></i> Next in: <span id="mainCountdown">2 days</span>
            </span>
        </div>
    </div>

    <!-- 3️⃣ Upcoming Vaccination Cards (Grid) -->
    <div class="row g-4 mb-4">
        <?php foreach($upcoming_vaccines as $vaccine): ?>
        <div class="col-xl-4 col-md-6 filter-item" data-child-id="<?= $vaccine['child_id'] ?>" data-date="<?= $vaccine['due_date'] ?>">
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
                        <small class="text-warning fw-bold">
                            <i class="fas fa-hourglass-half me-1"></i> <?= $vaccine['days_left'] ?> days left
                        </small>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Details</button>
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
                                <tr class="filter-item" data-child-id="<?= $row['child_id'] ?>" data-date="<?= $row['due_date'] ?>">
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
                                        <button class="btn btn-sm btn-light text-primary border"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <!-- Extra Dummy Row -->
                                <tr class="filter-item" data-child-id="1" data-date="2026-03-01">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-soft-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 10px;">SA</div>
                                            <span class="fw-medium small">Sarah Ahmed</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="small fw-bold text-dark">DPT Booster</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Booster</div>
                                    </td>
                                    <td class="px-4 py-3 small text-dark fw-medium">Mar 01, 2026</td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill">Scheduled</span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <button class="btn btn-sm btn-light text-primary border"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
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
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt me-2 text-info"></i>February 2026</h6>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-light border"><i class="fas fa-chevron-left small"></i></button>
                            <button class="btn btn-light border"><i class="fas fa-chevron-right small"></i></button>
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
                        <!-- Empty Days -->
                        <div class="p-2"></div><div class="p-2"></div><div class="p-2"></div>
                        
                        <!-- Days 1-28 (Simplified) -->
                        <div class="p-2 small rounded hover-bg-light">1</div>
                        <div class="p-2 small rounded hover-bg-light">2</div>
                        <div class="p-2 small rounded hover-bg-light">3</div>
                        <div class="p-2 small rounded hover-bg-light">4</div>
                        <div class="p-2 small rounded hover-bg-light">5</div>
                        <div class="p-2 small rounded hover-bg-light">6</div>
                        <div class="p-2 small rounded hover-bg-light">7</div>
                        <div class="p-2 small rounded hover-bg-light">8</div>
                        <div class="p-2 small rounded hover-bg-light">9</div>
                        <div class="p-2 small rounded hover-bg-light">10</div>
                        <div class="p-2 small rounded hover-bg-light">11</div>
                        
                        <!-- Highlighted Day 12 -->
                        <div class="p-2 small rounded bg-primary text-white fw-bold shadow-sm position-relative" data-bs-toggle="tooltip" title="MMR Vaccine - Sarah">
                            12
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        </div>
                        
                        <div class="p-2 small rounded hover-bg-light">13</div>
                        <div class="p-2 small rounded hover-bg-light">14</div>
                        
                        <!-- Highlighted Day 15 -->
                        <div class="p-2 small rounded bg-success text-white fw-bold shadow-sm position-relative" data-bs-toggle="tooltip" title="Polio - Ahmed">
                            15
                        </div>
                        
                        <div class="p-2 small rounded hover-bg-light">16</div>
                        <div class="p-2 small rounded hover-bg-light">17</div>
                        <div class="p-2 small rounded hover-bg-light">18</div>
                        <div class="p-2 small rounded hover-bg-light">19</div>
                        
                        <!-- Highlighted Day 20 -->
                        <div class="p-2 small rounded bg-warning text-dark fw-bold shadow-sm position-relative" data-bs-toggle="tooltip" title="Hep B - Fatima">
                            20
                        </div>
                        
                        <div class="p-2 small rounded hover-bg-light">21</div>
                        <div class="p-2 small rounded hover-bg-light">22</div>
                        <div class="p-2 small rounded hover-bg-light">23</div>
                        <div class="p-2 small rounded hover-bg-light">24</div>
                        <div class="p-2 small rounded hover-bg-light">25</div>
                        <div class="p-2 small rounded hover-bg-light">26</div>
                        <div class="p-2 small rounded hover-bg-light">27</div>
                        <div class="p-2 small rounded hover-bg-light">28</div>
                    </div>
                    <div class="mt-3 d-flex justify-content-center gap-3 small">
                        <div class="d-flex align-items-center"><span class="badge bg-primary rounded-circle p-1 me-1"> </span> Sarah</div>
                        <div class="d-flex align-items-center"><span class="badge bg-success rounded-circle p-1 me-1"> </span> Ahmed</div>
                        <div class="d-flex align-items-center"><span class="badge bg-warning rounded-circle p-1 me-1"> </span> Fatima</div>
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
    
    // Just a visual effect to show it's "live" (though static for this demo)
    setInterval(() => {
        // In a real app, this would calculate time difference
        // For UI demo, we keep it static or toggle slightly
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
