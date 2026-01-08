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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $nip = !empty($_POST['nip']) ? mysqli_real_escape_string($koneksi, $_POST['nip']) : null;
    $tempat_lahir = !empty($_POST['tempat_lahir']) ? mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']) : null;
    $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null;
    $alamat = !empty($_POST['alamat']) ? mysqli_real_escape_string($koneksi, $_POST['alamat']) : null;
    $no_telepon = !empty($_POST['no_telepon']) ? mysqli_real_escape_string($koneksi, $_POST['no_telepon']) : null;
    $email = !empty($_POST['email']) ? mysqli_real_escape_string($koneksi, $_POST['email']) : null;
    $jabatan = !empty($_POST['jabatan']) ? mysqli_real_escape_string($koneksi, $_POST['jabatan']) : null;
    $bidang_studi = !empty($_POST['bidang_studi']) ? mysqli_real_escape_string($koneksi, $_POST['bidang_studi']) : null;
    $status = !empty($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : 'aktif';

    // Check if username already exists
    $check_username = mysqli_query($koneksi, "SELECT ID FROM USERS WHERE USERNAME = '$username'");
    if (mysqli_num_rows($check_username) > 0) {
        $_SESSION['error'] = 'Username sudah digunakan';
        header('Location: ../../pages/guru/tambah.php');
        exit();
    }

    // Check if NIP already exists if provided
    if ($nip) {
        $check_nip = mysqli_query($koneksi, "SELECT ID FROM USERS WHERE NIP = '$nip'");
        if (mysqli_num_rows($check_nip) > 0) {
            $_SESSION['error'] = 'NIP sudah digunakan';
            header('Location: ../../pages/guru/tambah.php');
            exit();
        }
    }

    // Handle file upload (foto)
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/guru/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExt = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $fileName = uniqid('guru_') . '.' . $fileExt;
        $targetPath = $uploadDir . $fileName;

        // Check file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExt, $allowedTypes)) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
                $foto = 'uploads/guru/' . $fileName;
            }
        }
    }

    // Insert data
    $query = "INSERT INTO USERS (
        USERNAME, PASSWORD, NAMA_LENGKAP, JENIS_KELAMIN, NIP, 
        TEMPAT_LAHIR, TANGGAL_LAHIR, ALAMAT, NO_TELEPON, 
        EMAIL, ROLE, JABATAN, BIDANG_STUDI, FOTO, STATUS
    ) VALUES (
        '$username', 
        " . ($password ? "'$password'" : "NULL") . ", 
        '$nama_lengkap', 
        '$jenis_kelamin', 
        " . ($nip ? "'$nip'" : "NULL") . ", 
        " . ($tempat_lahir ? "'$tempat_lahir'" : "NULL") . ", 
        " . ($tanggal_lahir ? "'$tanggal_lahir'" : "NULL") . ", 
        " . ($alamat ? "'$alamat'" : "NULL") . ", 
        " . ($no_telepon ? "'$no_telepon'" : "NULL") . ", 
        " . ($email ? "'$email'" : "NULL") . ", 
        'guru', 
        " . ($jabatan ? "'$jabatan'" : "NULL") . ", 
        " . ($bidang_studi ? "'$bidang_studi'" : "NULL") . ", 
        " . ($foto ? "'$foto'" : "NULL") . ", 
        '$status'
    )";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = 'Data guru berhasil ditambahkan';
        header('Location: ../../pages/guru/index.php');
    } else {
        $_SESSION['error'] = 'Gagal menambahkan data guru: ' . mysqli_error($koneksi);
        header('Location: ../../pages/guru/tambah.php');
    }
} else {
    header('Location: ../../pages/guru/tambah.php');
}

exit();
