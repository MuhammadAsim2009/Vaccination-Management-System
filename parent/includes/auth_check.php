<?php
session_start(); 
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: /vaccination_management_system/auth/logout.php");
    exit();
}
?>