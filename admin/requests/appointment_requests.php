<?php
include '../includes/header.php';
include '../includes/sidebar.php';
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

        <!-- Alerts Placeholder (UI Only) -->
        <div id="alertPlaceholder"></div>

        <!-- Requests Statistics (Cards) -->
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
                        <h2 class="fw-bold mb-2">12</h2>
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
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Approved Today</h6>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-check-circle fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2">45</h2>
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
                        <h2 class="fw-bold mb-2">156</h2>
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
                            <!-- Dummy Row 1 -->
                            <tr class="request-row" data-status="pending">
                                <td class="ps-4 fw-medium">#REQ-8291</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">AM</div>
                                        <span class="search-target">Ali Muhammad</span>
                                    </div>
                                </td>
                                <td class="search-target">Muhammad Asif</td>
                                <td class="search-target">Polio (OPV-1)</td>
                                <td>Oct 12, 2024</td>
                                <td class="search-target">Mayo Hospital</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2 fw-medium">Pending</span>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-success rounded-3 px-3 shadow-none border-0" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-danger rounded-3 px-3 shadow-none border-0" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="fas fa-times me-1"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Dummy Row 2 -->
                            <tr class="request-row" data-status="approved">
                                <td class="ps-4 fw-medium">#REQ-8292</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">SK</div>
                                        <span class="search-target">Sara Khan</span>
                                    </div>
                                </td>
                                <td class="search-target">Ahmed Khan</td>
                                <td class="search-target">BCG</td>
                                <td>Oct 14, 2024</td>
                                <td class="search-target">Jinnah Hospital</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-medium">Approved</span>
                                </td>
                                <td class="pe-4 text-center">
                                    <button class="btn btn-sm btn-light text-muted rounded-3 px-3" disabled>
                                        <i class="fas fa-check-double me-1"></i> Processed
                                    </button>
                                </td>
                            </tr>
                            <!-- Dummy Row 3 -->
                            <tr class="request-row" data-status="rejected">
                                <td class="ps-4 fw-medium">#REQ-8293</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">ZA</div>
                                        <span class="search-target">Zainab Abbas</span>
                                    </div>
                                </td>
                                <td class="search-target">Abbas Ali</td>
                                <td class="search-target">Hepatitis B</td>
                                <td>Oct 15, 2024</td>
                                <td class="search-target">General Hospital</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 fw-medium">Rejected</span>
                                </td>
                                <td class="pe-4 text-center">
                                    <button class="btn btn-sm btn-light text-muted rounded-3 px-3" disabled>
                                        <i class="fas fa-ban me-1"></i> Cancelled
                                    </button>
                                </td>
                            </tr>
                            <!-- Dummy Row 4 (New) -->
                            <tr class="request-row" data-status="pending">
                                <td class="ps-4 fw-medium">#REQ-8294</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">HA</div>
                                        <span class="search-target">Hamza Amin</span>
                                    </div>
                                </td>
                                <td class="search-target">Amin Butt</td>
                                <td class="search-target">Pentavalent-1</td>
                                <td>Oct 16, 2024</td>
                                <td class="search-target">Services Hospital</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2 fw-medium">Pending</span>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-success rounded-3 px-3 shadow-none border-0" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-danger rounded-3 px-3 shadow-none border-0" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="fas fa-times me-1"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Dummy Row 5 (New) -->
                            <tr class="request-row" data-status="pending">
                                <td class="ps-4 fw-medium">#REQ-8295</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">FA</div>
                                        <span class="search-target">Fatima Akram</span>
                                    </div>
                                </td>
                                <td class="search-target">Muhammad Akram</td>
                                <td class="search-target">Measles-1</td>
                                <td>Oct 18, 2024</td>
                                <td class="search-target">Fatima Memorial</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2 fw-medium">Pending</span>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-success rounded-3 px-3 shadow-none border-0" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-danger rounded-3 px-3 shadow-none border-0" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="fas fa-times me-1"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination UI -->
            <div class="card-footer bg-white border-0 py-4 px-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-between align-items-center mb-0">
                        <li class="page-item disabled">
                            <span class="text-muted small">Showing 1 to 5 of 12 requests</span>
                        </li>
                        <div class="d-flex gap-2">
                            <li class="page-item"><a class="page-link border-0 bg-light rounded-3 px-3 text-dark" href="#"><i class="fas fa-chevron-left me-1"></i> Previous</a></li>
                            <li class="page-item active"><a class="page-link border-0 rounded-3 px-3" href="#">1</a></li>
                            <li class="page-item"><a class="page-link border-0 bg-light rounded-3 px-3 text-dark" href="#">2</a></li>
                            <li class="page-item"><a class="page-link border-0 bg-light rounded-3 px-3 text-dark" href="#">Next <i class="fas fa-chevron-right ms-1"></i></a></li>
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
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success px-4 py-2 rounded-3" data-bs-dismiss="modal" onclick="showAlert('success', 'Request approved successfully!')">Confirm Approval</button>
                </div>
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
                <div class="mb-4 text-start">
                    <label class="form-label small fw-semibold text-muted">Reason for Rejection</label>
                    <textarea class="form-control bg-light border-0 shadow-none" rows="3" placeholder="Enter reason..."></textarea>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4 py-2 rounded-3" data-bs-dismiss="modal" onclick="showAlert('danger', 'Request rejected.')">Confirm Rejection</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
