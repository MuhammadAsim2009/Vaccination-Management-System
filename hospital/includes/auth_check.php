<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hospital' || $_SESSION['hospital_status'] !== 'approved' ) {
    header("Location: /vaccination_management_system/auth/logout.php");
    exit();
}
?>