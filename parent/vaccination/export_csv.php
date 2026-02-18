<?php
// Requied Includes
include '../../config/db.php';
include '../includes/auth_check.php';

// Fetch Parent ID
$parent_user_id = $_SESSION['user_id'];

// Get filter parameters from URL
$child_name_filter = isset($_GET['child_name']) && $_GET['child_name'] !== 'all' ? $_GET['child_name'] : null;
$start_date_filter = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date_filter = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

// Base Query
$query = "SELECT 
            vs.scheduled_date, 
            c.name as child_name, 
            v.vaccine_name, 
            vs.dose_number, 
            h.hospital_name,
            vs.status
          FROM vaccination_schedule vs
          JOIN children c ON vs.child_id = c.id
          JOIN vaccines v ON vs.vaccine_id = v.id
          LEFT JOIN hospitals h ON vs.hospital_id = h.id
          WHERE c.parent_id = ?";

$params = [$parent_user_id];
$types = 'i';

// Append filters to query
if ($child_name_filter) {
    $query .= " AND c.name = ?";
    $params[] = $child_name_filter;
    $types .= 's';
}
if ($start_date_filter) {
    $query .= " AND DATE(vs.scheduled_date) >= ?";
    $params[] = $start_date_filter;
    $types .= 's';
}
if ($end_date_filter) {
    $query .= " AND DATE(vs.scheduled_date) <= ?";
    $params[] = $end_date_filter;
    $types .= 's';
}

$query .= " ORDER BY vs.scheduled_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Set headers for CSV download
$filename = "vaccination_report_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header
fputcsv($output, ['Date', 'Child Name', 'Vaccine', 'Dose', 'Hospital', 'Status']);

// Write data rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $status_display = ($row['status'] == 'vaccinated') ? 'Completed' : ucfirst($row['status']);
        
        $csv_row = [
            date('M d, Y', strtotime($row['scheduled_date'])),
            $row['child_name'],
            $row['vaccine_name'],
            $row['dose_number'],
            $row['hospital_name'] ?? 'Not Assigned',
            $status_display
        ];
        fputcsv($output, $csv_row);
    }
}

fclose($output);
exit();