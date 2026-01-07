<?php
require_once '../../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'pages/auth/login.php');
    exit();
}

$nama_user = $_SESSION['nama_lengkap'];
$role_user = ucfirst($_SESSION['role']);

// --- LOGIKA HITUNG DATA (STATISTIK) ---
// Kita gunakan $koneksi (bukan $conn)
$total_siswa = 0;
$total_guru = 0;
$total_kelas = 0;

// 1. Hitung Siswa Aktif
$query_siswa = $koneksi->query("SELECT COUNT(*) as total FROM siswa WHERE status = 'aktif'");
if ($query_siswa) {
    $row = $query_siswa->fetch_assoc();
    $total_siswa = $row['total'];
}

// 2. Hitung Guru Aktif
$query_guru = $koneksi->query("SELECT COUNT(*) as total FROM users WHERE role = 'guru' AND status = 'aktif'");
if ($query_guru) {
    $row = $query_guru->fetch_assoc();
    $total_guru = $row['total'];
}

// 3. Hitung Kelas Aktif
$query_kelas = $koneksi->query("SELECT COUNT(*) as total FROM kelas WHERE status = 'aktif'");
if ($query_kelas) {
    $row = $query_kelas->fetch_assoc();
    $total_kelas = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sekolah App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f5f6fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #2c3e50;
            color: white;
        }

        .sidebar a {
            color: #bdc3c7;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            border-bottom: 1px solid #34495e;
        }

        .sidebar a:hover {
            background-color: #34495e;
            color: white;
        }

        .sidebar .active {
            background-color: #0d6efd;
            color: white;
        }

        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card-custom:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar p-0 d-none d-md-block">
                <div class="p-3 text-center border-bottom border-secondary">
                    <h4 class="m-0">Sekolah App</h4>
                    <small>Sistem Manajemen</small>
                </div>

                <div class="mt-3">
                    <a href="#" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>

                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="#"><i class="bi bi-people me-2"></i> Data Siswa</a>
                        <a href="#"><i class="bi bi-person-badge me-2"></i> Data Guru</a>
                        <a href="#"><i class="bi bi-book me-2"></i> Mata Pelajaran</a>
                        <a href="#"><i class="bi bi-calendar-week me-2"></i> Jadwal</a>
                    <?php else: ?>
                        <a href="#"><i class="bi bi-journal-check me-2"></i> Absensi Saya</a>
                        <a href="#"><i class="bi bi-graph-up me-2"></i> Nilai Siswa</a>
                    <?php endif; ?>

                    <a href="#"><i class="bi bi-gear me-2"></i> Pengaturan</a>

                    <a href="../../actions/auth/logout.php" class="text-danger mt-5 border-top border-secondary">
                        <i class="bi bi-box-arrow-left me-2"></i> Logout
                    </a>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-4 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <div class="alert alert-primary d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        Selamat Datang, <strong><?php echo htmlspecialchars($nama_user); ?></strong>!
                        Anda login sebagai <span class="badge bg-primary"><?php echo $role_user; ?></span>.
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card card-custom bg-primary text-white h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Siswa</h5>
                                <h2 class="display-4 fw-bold"><?php echo $total_siswa; ?></h2>
                                <p class="card-text">Siswa Aktif</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card card-custom bg-success text-white h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Guru</h5>
                                <h2 class="display-4 fw-bold"><?php echo $total_guru; ?></h2>
                                <p class="card-text">Guru Terdaftar</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card card-custom bg-warning text-dark h-100">
                            <div class="card-body">
                                <h5 class="card-title">Kelas</h5>
                                <h2 class="display-4 fw-bold"><?php echo $total_kelas; ?></h2>
                                <p class="card-text">Rombongan Belajar</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>