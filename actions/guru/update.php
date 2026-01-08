<?php
session_start();
require_once('../../config/database.php');

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini';
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Get current data
    $current = mysqli_query($koneksi, "SELECT * FROM USERS WHERE ID = $id AND ROLE = 'guru'");
    if (mysqli_num_rows($current) === 0) {
        $_SESSION['error'] = 'Data guru tidak ditemukan';
        header('Location: ../../pages/guru/index.php');
        exit();
    }
    $currentData = mysqli_fetch_assoc($current);

    // Sanitize input
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
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
    $status = !empty($_POST['status']) ? mysqli_real_escape_string($koneksi, $_POST['status']) : 'aktif';

    // Check if username is changed and already exists
    if ($username !== $currentData['USERNAME']) {
        $check_username = mysqli_query($koneksi, "SELECT ID FROM USERS WHERE USERNAME = '$username' AND ID != $id");
        if (mysqli_num_rows($check_username) > 0) {
            $_SESSION['error'] = 'Username sudah digunakan';
            header("Location: ../../pages/guru/edit.php?id=$id");
            exit();
        }
    }

    // Check if NIP is changed and already exists
    if ($nip && $nip !== $currentData['NIP']) {
        $check_nip = mysqli_query($koneksi, "SELECT ID FROM USERS WHERE NIP = '$nip' AND ID != $id");
        if (mysqli_num_rows($check_nip) > 0) {
            $_SESSION['error'] = 'NIP sudah digunakan';
            header("Location: ../../pages/guru/edit.php?id=$id");
            exit();
        }
    }

    // Handle file upload (foto)
    $foto = $currentData['FOTO'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/guru/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Delete old photo if exists
        if ($foto && file_exists('../../' . $foto)) {
            unlink('../../' . $foto);
        }

        $fileExt = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $fileName = uniqid('guru_') . '.' . $fileExt;
        $targetPath = $uploadDir . $fileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExt, $allowedTypes)) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
                $foto = 'uploads/guru/' . $fileName;
            }
        }
    }

    // Handle password update (only if new password is provided)
    $passwordUpdate = '';
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $passwordUpdate = "PASSWORD = '$password', ";
    }

    // Update query
    $query = "UPDATE USERS SET 
        USERNAME = '$username', 
        $passwordUpdate
        NAMA_LENGKAP = '$nama_lengkap', 
        JENIS_KELAMIN = '$jenis_kelamin', 
        NIP = " . ($nip ? "'$nip'" : 'NULL') . ", 
        TEMPAT_LAHIR = " . ($tempat_lahir ? "'$tempat_lahir'" : 'NULL') . ", 
        TANGGAL_LAHIR = " . ($tanggal_lahir ? "'$tanggal_lahir'" : 'NULL') . ", 
        ALAMAT = " . ($alamat ? "'$alamat'" : 'NULL') . ", 
        NO_TELEPON = " . ($no_telepon ? "'$no_telepon'" : 'NULL') . ", 
        EMAIL = " . ($email ? "'$email'" : 'NULL') . ", 
        JABATAN = " . ($jabatan ? "'$jabatan'" : 'NULL') . ", 
        BIDANG_STUDI = " . ($bidang_studi ? "'$bidang_studi'" : 'NULL') . ", 
        FOTO = " . ($foto ? "'$foto'" : 'NULL') . ", 
        STATUS = '$status',
        UPDATED_AT = CURRENT_TIMESTAMP
        WHERE ID = $id AND ROLE = 'guru'";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = 'Data guru berhasil diperbarui';
    } else {
        $_SESSION['error'] = 'Gagal memperbarui data guru: ' . mysqli_error($koneksi);
    }
} else {
    $_SESSION['error'] = 'Permintaan tidak valid';
}

header('Location: ' . ($_SESSION['error'] ? "../../pages/guru/edit.php?id=$id" : '../../pages/guru/index.php'));
exit();
