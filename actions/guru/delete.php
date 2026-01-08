<?php
session_start();
require_once('../../config/database.php');
$koneksi = $conn; // Alias for consistency

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini';
    header('Location: ../../index.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get teacher data to delete photo if exists
    $result = mysqli_query($koneksi, "SELECT FOTO FROM USERS WHERE ID = $id AND ROLE = 'guru'");
    if (mysqli_num_rows($result) === 0) {
        $_SESSION['error'] = 'Data guru tidak ditemukan';
        header('Location: ../../pages/guru/index.php');
        exit();
    }

    $data = mysqli_fetch_assoc($result);

    // Delete photo if exists
    if (!empty($data['FOTO']) && file_exists('../../' . $data['FOTO'])) {
        unlink('../../' . $data['FOTO']);
    }

    // Delete from database
    $query = "DELETE FROM USERS WHERE ID = $id AND ROLE = 'guru'";
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = 'Data guru berhasil dihapus';
    } else {
        $_SESSION['error'] = 'Gagal menghapus data guru: ' . mysqli_error($koneksi);
    }
} else {
    $_SESSION['error'] = 'ID guru tidak valid';
}

header('Location: ../../pages/guru/index.php');
exit();
