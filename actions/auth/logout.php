<?php
session_start();
require_once "../../config/database.php";

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with logout message
header("Location: ../../pages/auth/login.php?status=logout");
exit();
