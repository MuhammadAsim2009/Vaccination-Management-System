<?php
// Include DB and Auth
include '../../config/db.php';
include '../includes/auth_check.php';

// --- 1. Fetch Overview Stats ---
$stats = [];
$res = $conn->query("SELECT COUNT(*) as count FROM children");
$stats['Total Children'] = ($res->fetch_assoc())['count'];

$res = $conn->query("SELECT COUNT(*) as count FROM vaccination_schedule WHERE status = 'vaccinated'");
$stats['Total Vaccinations'] = ($res->fetch_assoc())['count'];

$res = $conn->query("SELECT COUNT(*) as count FROM vaccination_schedule WHERE status = 'pending' AND scheduled_date >= CURDATE()");
$stats['Upcoming Appointments'] = ($res->fetch_assoc())['count'];

$res = $conn->query("SELECT COUNT(*) as count FROM hospitals");
$stats['Registered Hospitals'] = ($res->fetch_assoc())['count'];

// --- 2. Fetch Children Data ---
$children_data = [];
$sql = "SELECT c.id, c.name, c.date_of_birth, c.gender, c.blood_group, u.name as parent_name, p.phone 
        FROM children c 
        LEFT JOIN users u ON c.parent_id = u.id 
        LEFT JOIN parents p ON u.id = p.user_id";
$res = $conn->query($sql);
if($res) {
    while($row = $res->fetch_assoc()) $children_data[] = $row;
}

// --- 3. Fetch Vaccines Data ---
$vaccines_data = [];
$res = $conn->query("SELECT * FROM vaccines");
if($res) {
    while($row = $res->fetch_assoc()) $vaccines_data[] = $row;
}

// --- 4. Fetch Hospitals Data ---
$hospitals_data = [];
$res = $conn->query("SELECT h.*, u.email FROM hospitals h LEFT JOIN users u ON h.user_id = u.id");
if($res) {
    while($row = $res->fetch_assoc()) $hospitals_data[] = $row;
}

// --- 5. Fetch Requests Data ---
$requests_data = [];
$sql = "SELECT r.id, c.name AS child_name, u.name AS parent_name, v.vaccine_name, r.appointment_date, h.hospital_name, r.status 
        FROM appointments r 
        JOIN children c ON r.child_id = c.id 
        JOIN parents p ON r.parent_id = p.id 
        JOIN users u ON p.user_id = u.id 
        JOIN vaccines v ON r.vaccine_id = v.id 
        JOIN hospitals h ON r.hospital_id = h.id 
        ORDER BY r.created_at DESC";
$res = $conn->query($sql);
if($res) {
    while($row = $res->fetch_assoc()) $requests_data[] = $row;
}

// --- 6. Fetch Bookings Data ---
$bookings_data = [];
$sql = "SELECT vs.id, c.name as child_name, v.vaccine_name, vs.dose_number, vs.scheduled_date, h.hospital_name, vs.status 
        FROM vaccination_schedule vs JOIN children c ON vs.child_id = c.id JOIN vaccines v ON vs.vaccine_id = v.id LEFT JOIN hospitals h ON vs.hospital_id = h.id ORDER BY vs.scheduled_date DESC";
$res = $conn->query($sql);
if($res) {
    while($row = $res->fetch_assoc()) $bookings_data[] = $row;
}

// --- Generate Excel XML (SpreadsheetML) ---
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="VMS_Dashboard_Report_' . date('Y-m-d') . '.xls"');

echo '<?xml version="1.0"?>';
echo '<?mso-application progid="Excel.Sheet"?>';
echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="Header">
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#404040" ss:Pattern="Solid"/>
  </Style>
 </Styles>';
?>

 <!-- Sheet 1: Overview -->
 <Worksheet ss:Name="Overview">
  <Table>
   <Column ss:Width="150"/>
   <Column ss:Width="100"/>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">Metric</Data></Cell>
    <Cell><Data ss:Type="String">Value</Data></Cell>
   </Row>
   <?php foreach($stats as $key => $val): ?>
   <Row>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($key) ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $val ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Sheet 2: Children -->
 <Worksheet ss:Name="Children">
  <Table>
   <Column ss:Width="50"/>
   <Column ss:Width="120"/>
   <Column ss:Width="80"/>
   <Column ss:Width="60"/>
   <Column ss:Width="60"/>
   <Column ss:Width="120"/>
   <Column ss:Width="100"/>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">ID</Data></Cell>
    <Cell><Data ss:Type="String">Name</Data></Cell>
    <Cell><Data ss:Type="String">DOB</Data></Cell>
    <Cell><Data ss:Type="String">Gender</Data></Cell>
    <Cell><Data ss:Type="String">Blood</Data></Cell>
    <Cell><Data ss:Type="String">Parent Name</Data></Cell>
    <Cell><Data ss:Type="String">Parent Phone</Data></Cell>
   </Row>
   <?php foreach($children_data as $row): ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $row['id'] ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['name']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $row['date_of_birth'] ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $row['gender'] ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $row['blood_group'] ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['parent_name']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $row['phone'] ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Sheet 3: Vaccines -->
 <Worksheet ss:Name="Vaccines">
  <Table>
   <Column ss:Width="150"/>
   <Column ss:Width="100"/>
   <Column ss:Width="60"/>
   <Column ss:Width="80"/>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">Vaccine Name</Data></Cell>
    <Cell><Data ss:Type="String">Target Age</Data></Cell>
    <Cell><Data ss:Type="String">Doses</Data></Cell>
    <Cell><Data ss:Type="String">Status</Data></Cell>
   </Row>
   <?php foreach($vaccines_data as $row): ?>
   <Row>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['vaccine_name']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['target_age_group']) ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $row['total_dose'] ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= ucfirst($row['availability_status']) ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Sheet 4: Hospitals -->
 <Worksheet ss:Name="Hospitals">
  <Table>
   <Column ss:Width="150"/>
   <Column ss:Width="100"/>
   <Column ss:Width="150"/>
   <Column ss:Width="100"/>
   <Column ss:Width="200"/>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">Hospital Name</Data></Cell>
    <Cell><Data ss:Type="String">Reg No</Data></Cell>
    <Cell><Data ss:Type="String">Email</Data></Cell>
    <Cell><Data ss:Type="String">Phone</Data></Cell>
    <Cell><Data ss:Type="String">Address</Data></Cell>
   </Row>
   <?php foreach($hospitals_data as $row): ?>
   <Row>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['hospital_name']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['registration_no']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['email']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['phone']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['address']) ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Sheet 5: Requests -->
 <Worksheet ss:Name="Requests">
  <Table>
   <Column ss:Width="50"/>
   <Column ss:Width="120"/>
   <Column ss:Width="120"/>
   <Column ss:Width="120"/>
   <Column ss:Width="100"/>
   <Column ss:Width="150"/>
   <Column ss:Width="80"/>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">ID</Data></Cell>
    <Cell><Data ss:Type="String">Child Name</Data></Cell>
    <Cell><Data ss:Type="String">Parent Name</Data></Cell>
    <Cell><Data ss:Type="String">Vaccine</Data></Cell>
    <Cell><Data ss:Type="String">Date</Data></Cell>
    <Cell><Data ss:Type="String">Hospital</Data></Cell>
    <Cell><Data ss:Type="String">Status</Data></Cell>
   </Row>
   <?php foreach($requests_data as $row): ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $row['id'] ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['child_name']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['parent_name']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['vaccine_name']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $row['appointment_date'] ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['hospital_name']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= ucfirst($row['status']) ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Sheet 6: Bookings -->
 <Worksheet ss:Name="Bookings">
  <Table>
   <Column ss:Width="120"/>
   <Column ss:Width="120"/>
   <Column ss:Width="60"/>
   <Column ss:Width="100"/>
   <Column ss:Width="150"/>
   <Column ss:Width="80"/>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">Child Name</Data></Cell>
    <Cell><Data ss:Type="String">Vaccine</Data></Cell>
    <Cell><Data ss:Type="String">Dose</Data></Cell>
    <Cell><Data ss:Type="String">Date</Data></Cell>
    <Cell><Data ss:Type="String">Hospital</Data></Cell>
    <Cell><Data ss:Type="String">Status</Data></Cell>
   </Row>
   <?php foreach($bookings_data as $row): ?>
   <Row>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['child_name']) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['vaccine_name']) ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $row['dose_number'] ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $row['scheduled_date'] ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($row['hospital_name'] ?? 'N/A') ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= ucfirst($row['status']) ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>
</Workbook>
