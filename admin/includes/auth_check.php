<?php
session_start(); 
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /vaccination_management_system/auth/login.php");
    exit();
}
?>