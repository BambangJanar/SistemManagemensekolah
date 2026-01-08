<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini';
    header('Location: ../../index.php');
    exit();
}

require_once('../../config/database.php');

// Get all teachers
$query = "SELECT * FROM USERS WHERE ROLE = 'guru' ORDER BY NAMA_LENGKAP ASC";
$result = mysqli_query(
    $koneksi,
    $query
);
$guru = [];
if ($result) {
    $guru = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Guru - Sistem Manajemen Sekolah</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        .status-badge {
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <?php include('../../includes/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <?php include('../../includes/sidebar.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manajemen Guru</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="tambah.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Tambah Guru
                        </a>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Foto</th>
                                        <th>Nama Lengkap</th>
                                        <th>NIP</th>
                                        <th>Jenis Kelamin</th>
                                        <th>No. Telepon</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($guru) > 0): ?>
                                        <?php foreach ($guru as $key => $g): ?>
                                            <tr>
                                                <td><?= $key + 1; ?></td>
                                                <td>
                                                    <?php if (!empty($g['FOTO'])): ?>
                                                        <img src="../../<?= htmlspecialchars($g['FOTO']); ?>" alt="Foto Profil" class="profile-img">
                                                    <?php else: ?>
                                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($g['NAMA_LENGKAP']); ?></td>
                                                <td><?= $g['NIP'] ? htmlspecialchars($g['NIP']) : '-'; ?></td>
                                                <td><?= $g['JENIS_KELAMIN'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                                <td><?= $g['NO_TELEPON'] ? htmlspecialchars($g['NO_TELEPON']) : '-'; ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = [
                                                        'aktif' => 'success',
                                                        'nonaktif' => 'secondary',
                                                        'pensiun' => 'info',
                                                        'pindah' => 'warning'
                                                    ][$g['STATUS']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass; ?> status-badge">
                                                        <?= ucfirst($g['STATUS']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="edit.php?id=<?= $g['ID']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" onclick="confirmDelete(<?= $g['ID']; ?>)" class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data guru</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data guru ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id) {
            const deleteBtn = document.getElementById('confirmDeleteBtn');
            deleteBtn.href = `actions/guru/delete.php?id=${id}`;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>

</html>