<?php
// Panggil konfigurasi database
require_once 'config/database.php';

// 1. Siapkan data Admin
$username = 'admin';
$password_plain = 'admin123';
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT); // Enkripsi password
$nama = 'Administrator Utama';
$role = 'admin';
$status = 'aktif';

// 2. Cek dulu, apakah admin sudah ada?
$cek = $koneksi->query("SELECT id FROM users WHERE username = 'admin'");

if ($cek->num_rows > 0) {
    // Jika sudah ada, kita Update saja passwordnya (biar tidak error duplikat)
    $stmt = $koneksi->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->bind_param("s", $password_hash);
    if ($stmt->execute()) {
        echo "<h1>Akun Diperbarui!</h1>";
        echo "User 'admin' sudah ada, password berhasil di-reset menjadi: <b>admin123</b>";
    }
} else {
    // Jika BELUM ADA (Kosong), kita Buat Baru (INSERT)
    $stmt = $koneksi->prepare("INSERT INTO users (username, password, nama_lengkap, role, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $password_hash, $nama, $role, $status);

    if ($stmt->execute()) {
        echo "<h1>BERHASIL! 🎉</h1>";
        echo "Akun Admin berhasil dibuat!<br>";
        echo "Username: <b>admin</b><br>";
        echo "Password: <b>admin123</b><br><br>";
        echo "Silakan <a href='pages/auth/login.php'>Login Di Sini</a>";
    } else {
        echo "Gagal membuat akun: " . $koneksi->error;
    }
}
