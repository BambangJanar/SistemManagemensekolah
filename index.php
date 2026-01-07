<?php
// Panggil file konfigurasi (otomatis menjalankan session_start)
require_once 'config/database.php';

// Cek status login
if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Jika sudah login, gunakan BASE_URL untuk redirect ke Dashboard
    header('Location: ' . BASE_URL . 'pages/dashboard/index.php');
} else {
    // Jika belum login, redirect ke halaman Login
    header('Location: ' . BASE_URL . 'pages/auth/login.php');
}

exit();
