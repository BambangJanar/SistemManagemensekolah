<?php
// PERBAIKAN: Cek dulu apakah session sudah ada? Kalau belum, baru start.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sekolah_db');

// Base URL Configuration
define('BASE_URL', 'http://localhost/sekolah_app/');

// Create connection
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($koneksi->connect_error) {
    die("Koneksi Gagal: " . $koneksi->connect_error);
}

// Set charset
$koneksi->set_charset("utf8mb4");

// Set Timezone
date_default_timezone_set('Asia/Jakarta');

// --- HELPER FUNCTIONS (CSRF) ---
function generateCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token)
{
    if (!empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}
