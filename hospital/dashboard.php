<?php
include '../config/db.php';
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Fetch Hospital ID
$user_id = $_SESSION['user_id'];
$hospital_id = 0;
$hospital_name = "Hospital";

$stmt = $conn->prepare("SELECT id, hospital_name FROM hospitals WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $hospital_id = $row['id'];
    $hospital_name = $row['hospital_name'];
}

// 1. Statistics
$total_appointments = 0;
$completed_vaccinations = 0;
$pending_appointments = 0;
$available_vaccines = 0;

if ($hospital_id) {
    // Total
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vaccination_schedule WHERE hospital_id = ?");
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $total_appointments = $stmt->get_result()->fetch_assoc()['count'];

    // Completed
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vaccination_schedule WHERE hospital_id = ? AND status = 'vaccinated'");
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $completed_vaccinations = $stmt->get_result()->fetch_assoc()['count'];

    // Pending
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vaccination_schedule WHERE hospital_id = ? AND status = 'pending'");
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $pending_appointments = $stmt->get_result()->fetch_assoc()['count'];
}

// Available Vaccines (Global)
$res = $conn->query("SELECT COUNT(*) as count FROM vaccines WHERE availability_status = 'available'");
if ($res) {
    $available_vaccines = $res->fetch_assoc()['count'];
}

// 2. Chart Data (Last 7 Days)
$chart_labels = [];
$chart_data_total = [];
$chart_data_completed = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('D', strtotime($date)); // Mon, Tue...
    
    if ($hospital_id) {
        // Total Scheduled for this date
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vaccination_schedule WHERE hospital_id = ? AND DATE(scheduled_date) = ?");
        $stmt->bind_param("is", $hospital_id, $date);
        $stmt->execute();
        $chart_data_total[] = $stmt->get_result()->fetch_assoc()['count'];

        // Completed for this date
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vaccination_schedule WHERE hospital_id = ? AND DATE(scheduled_date) = ? AND status = 'vaccinated'");
        $stmt->bind_param("is", $hospital_id, $date);
        $stmt->execute();
        $chart_data_completed[] = $stmt->get_result()->fetch_assoc()['count'];
    } else {
        $chart_data_total[] = 0;
        $chart_data_completed[] = 0;
    }
}

// 3. Upcoming Appointments (Next 5)
$upcoming_appts = [];
if ($hospital_id) {
    $sql = "SELECT vs.scheduled_date, c.name as child_name, v.vaccine_name, vs.dose_number 
            FROM vaccination_schedule vs 
            JOIN children c ON vs.child_id = c.id 
            JOIN vaccines v ON vs.vaccine_id = v.id 
            WHERE vs.hospital_id = ? AND vs.scheduled_date >= NOW() AND vs.status = 'pending' 
            ORDER BY vs.scheduled_date ASC LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $upcoming_appts = $stmt->get_result();
}

// 4. Today's Appointments
$todays_appts = [];
if ($hospital_id) {
    $sql = "SELECT vs.id, c.name as child_name, u.name as parent_name, v.vaccine_name, vs.scheduled_date, vs.status 
            FROM vaccination_schedule vs 
            JOIN children c ON vs.child_id = c.id 
            JOIN users u ON c.parent_id = u.id 
            JOIN vaccines v ON vs.vaccine_id = v.id 
            WHERE vs.hospital_id = ? AND DATE(vs.scheduled_date) = CURDATE() 
            ORDER BY vs.scheduled_date ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $todays_appts = $stmt->get_result();
}

// 5. Notifications (Recent Requests)
$notifications = [];
if ($hospital_id) {
    // Fetch pending appointment requests
    $sql = "SELECT a.created_at, c.name as child_name, 'request' as type 
            FROM appointments a 
            JOIN children c ON a.child_id = c.id 
            WHERE a.hospital_id = ? AND a.status = 'approved' 
            ORDER BY a.created_at DESC LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $notifications = $stmt->get_result();
}
?>

<!-- Main Content -->
<main class="mt-5 pt-3">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Home</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary">Hospital Dashboard</h2>
                <p class="text-muted">Welcome, <?= htmlspecialchars($hospital_name) ?></p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Appointments -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Total Appointments</h6>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-calendar-check fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= number_format($total_appointments) ?></h2>
                    </div>
                </div>
            </div>

            <!-- Completed Vaccinations -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Completed Vaccinations</h6>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-syringe fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= number_format($completed_vaccinations) ?></h2>
                    </div>
                </div>
            </div>

            <!-- Pending Appointments -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Pending Appointments</h6>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-clock fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= number_format($pending_appointments) ?></h2>
                    </div>
                </div>
            </div>

            <!-- Available Vaccines -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title text-muted text-uppercase mb-0 small fw-bold">Available Vaccines</h6>
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-boxes fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-2"><?= number_format($available_vaccines) ?> Types</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Appointment Analytics Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Weekly Appointment Analytics</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="appointmentChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments Widget -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Upcoming</h5>
                        <a href="vaccination/appointments.php" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php if ($upcoming_appts && $upcoming_appts->num_rows > 0): ?>
                                <?php while ($row = $upcoming_appts->fetch_assoc()): 
                                    $appt_time = strtotime($row['scheduled_date']);
                                    $time_diff = $appt_time - time();
                                    $days_diff = floor($time_diff / (60 * 60 * 24));
                                    
                                    $badge_class = 'bg-light text-dark';
                                    $badge_text = 'In ' . ($days_diff + 1) . ' Days';
                                    
                                    if ($days_diff < 0) { $badge_text = 'Overdue'; $badge_class = 'bg-soft-danger text-danger'; }
                                    elseif ($days_diff == 0) { $badge_text = 'Today'; $badge_class = 'bg-soft-primary text-primary'; }
                                    elseif ($days_diff == 1) { $badge_text = 'Tomorrow'; $badge_class = 'bg-soft-warning text-warning'; }
                                ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($row['child_name']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($row['vaccine_name']) ?> - Dose <?= $row['dose_number'] ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge <?= $badge_class ?> mb-1"><?= $badge_text ?></span>
                                    <div class="small fw-bold text-muted"><?= date('h:i A', $appt_time) ?></div>
                                </div>
                            </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="list-group-item text-center py-3 text-muted">No upcoming appointments.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Today's Appointments Table -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                     <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-list-ul me-2 text-primary"></i>Today's Appointments</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">Appt ID</th>
                                        <th>Child Name</th>
                                        <th>Parent</th>
                                        <th>Vaccine</th>
                                        <th>Time</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($todays_appts && $todays_appts->num_rows > 0): ?>
                                    <?php while ($row = $todays_appts->fetch_assoc()): 
                                        $status_class = 'bg-soft-warning text-warning';
                                        if ($row['status'] == 'vaccinated') $status_class = 'bg-soft-success text-success';
                                        elseif ($row['status'] == 'missed') $status_class = 'bg-soft-danger text-danger';
                                    ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">#<?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['child_name']) ?></td>
                                        <td><?= htmlspecialchars($row['parent_name']) ?></td>
                                        <td><?= htmlspecialchars($row['vaccine_name']) ?></td>
                                        <td><?= date('h:i A', strtotime($row['scheduled_date'])) ?></td>
                                        <td class="text-center"><span class="badge <?= $status_class ?>"><?= ucfirst($row['status']) ?></span></td>
                                        <td class="text-end pe-3">
                                            <?php if ($row['status'] == 'pending'): ?>
                                            <a href="vaccination/update_status.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary rounded-pill px-3">Update</a>
                                            <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled>Update</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center py-3 text-muted">No appointments for today.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                     <div class="card-footer bg-white text-center py-2">
                         <a href="vaccination/appointments.php" class="text-decoration-none small fw-bold">View All Appointments</a>
                     </div>
                </div>
            </div>

            <!-- Notifications Panel -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-bell me-2 text-primary"></i>Recent Notifications</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php if ($notifications && $notifications->num_rows > 0): ?>
                            <?php while ($notif = $notifications->fetch_assoc()): ?>
                            <a href="#" class="list-group-item list-group-item-action py-3 border-bottom">
                                <div class="d-flex w-100 justify-content-between mb-1">
                                    <h6 class="mb-1 fw-bold text-dark">New Appointment Request</h6>
                                    <small class="text-muted"><?= date('M d', strtotime($notif['created_at'])) ?></small>
                                </div>
                                <p class="mb-1 small text-secondary">New appointment request received for Child: <strong><?= htmlspecialchars($notif['child_name']) ?></strong>.</p>
                            </a>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">No new notifications.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                     <div class="card-footer bg-white text-center py-2">
                         <a href="notifications/notifications.php" class="text-decoration-none small fw-bold">View All Notifications</a>
                     </div>
                </div>
            </div>
        </div>


    
    <!-- Initialize Chart -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('appointmentChart').getContext('2d');
            var appointmentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($chart_labels) ?>,
                    datasets: [{
                        label: 'Appointments Scheduled',
                        data: <?= json_encode($chart_data_total) ?>,
                        backgroundColor: 'rgba(13, 110, 253, 0.7)', // Primary color
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    },
                    {
                        label: 'Vaccinations Completed',
                        data: <?= json_encode($chart_data_completed) ?>,
                        backgroundColor: 'rgba(25, 135, 84, 0.7)', // Success color
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: '#f0f0f0' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
    


<!-- Footer include closes the div and main tags opened here -->
<?php include 'includes/footer.php'; ?>
