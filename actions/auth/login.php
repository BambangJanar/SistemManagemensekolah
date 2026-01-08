<?php
require_once '../../config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/auth/login.php?error=invalid_request');
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    header('Location: ../../pages/auth/login.php?error=invalid_csrf');
    exit();
}

// Get form data
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
    header('Location: ../../pages/auth/login.php?error=invalid_credentials');
    exit();
}

try {
    // REVISI: Menggunakan variabel $koneksi (sesuai config/database.php)
    $stmt = $koneksi->prepare("SELECT * FROM USERS WHERE USERNAME = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['PASSWORD'])) {
            // Check if account is active
            if ($user['STATUS'] !== 'aktif') {
                header('Location: ../../pages/auth/login.php?error=inactive');
                exit();
            }

            // Set session variables
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['username'] = $user['USERNAME'];
            $_SESSION['nama_lengkap'] = $user['NAMA_LENGKAP'];
            $_SESSION['role'] = $user['ROLE'];
            $_SESSION['logged_in'] = true;

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Update last login
            // REVISI: Menggunakan variabel $koneksi
            $updateStmt = $koneksi->prepare("UPDATE USERS SET LAST_LOGIN = NOW() WHERE ID = ?");
            $updateStmt->bind_param("i", $user['ID']);
            $updateStmt->execute();

            // Redirect based on role
            header('Location: ../../pages/dashboard/index.php');
            exit();
        }
    }

    // If we get here, login failed
    header('Location: ../../pages/auth/login.php?error=invalid_credentials');
    exit();
} catch (Exception $e) {
    // Log the error
    error_log('Login error: ' . $e->getMessage());

    // Don't show detailed errors to users
    header('Location: ../../pages/auth/login.php?error=server_error');
    exit();
}
