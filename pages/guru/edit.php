<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini';
    header('Location: ../../index.php');
    exit();
}

require_once('../../config/database.php');

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'ID guru tidak valid';
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);

// Get teacher data
$query = "SELECT * FROM USERS WHERE ID = $id AND ROLE = 'guru'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = 'Data guru tidak ditemukan';
    header('Location: index.php');
    exit();
}

$guru = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guru - Sistem Manajemen Sekolah</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .profile-placeholder {
            width: 150px;
            height: 150px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            cursor: pointer;
        }

        .profile-placeholder i {
            font-size: 3rem;
        }

        #currentPhoto {
            max-width: 150px;
            max-height: 150px;
            border-radius: 5px;
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
                    <h1 class="h2">Edit Data Guru</h1>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form action="../../actions/guru/update.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $guru['ID']; ?>">

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <h5 class="mb-3">Foto Profil</h5>
                                    <div class="mb-3 text-center">
                                        <?php if (!empty($guru['FOTO'])): ?>
                                            <img id="currentPhoto" src="../../<?= htmlspecialchars($guru['FOTO']); ?>" alt="Foto Profil" class="mb-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="removePhoto" name="remove_photo" value="1">
                                                <label class="form-check-label" for="removePhoto">Hapus foto</label>
                                            </div>
                                        <?php else: ?>
                                            <div id="imagePlaceholder" class="profile-placeholder mb-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>

                                        <img id="imagePreview" class="profile-preview mb-2 d-none" alt="Preview">
                                        <input type="file" class="form-control d-none" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                        <label for="foto" class="btn btn-sm btn-outline-secondary w-100">
                                            <i class="fas fa-upload"></i> Ganti Foto
                                        </label>
                                        <div class="form-text">Format: JPG, PNG, maks 2MB</div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <h5 class="mb-3">Informasi Pribadi</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($guru['NAMA_LENGKAP']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="nip" class="form-label">NIP</label>
                                            <input type="text" class="form-control" id="nip" name="nip" value="<?= htmlspecialchars($guru['NIP'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                                <option value="L" <?= $guru['JENIS_KELAMIN'] === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                                <option value="P" <?= $guru['JENIS_KELAMIN'] === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="<?= htmlspecialchars($guru['TEMPAT_LAHIR'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= $guru['TANGGAL_LAHIR'] ?? ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="aktif" <?= $guru['STATUS'] === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                                <option value="nonaktif" <?= $guru['STATUS'] === 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                                                <option value="pensiun" <?= $guru['STATUS'] === 'pensiun' ? 'selected' : ''; ?>>Pensiun</option>
                                                <option value="pindah" <?= $guru['STATUS'] === 'pindah' ? 'selected' : ''; ?>>Pindah</option>
                                            </select>
                                        </div>
                                    </div>

                                    <h5 class="mt-4 mb-3">Informasi Akun</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($guru['USERNAME']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Password Baru</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah password</div>
                                        </div>
                                    </div>

                                    <h5 class="mt-4 mb-3">Informasi Kontak</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= htmlspecialchars($guru['ALAMAT'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="no_telepon" class="form-label">No. Telepon</label>
                                            <input type="tel" class="form-control" id="no_telepon" name="no_telepon" value="<?= htmlspecialchars($guru['NO_TELEPON'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($guru['EMAIL'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <h5 class="mt-4 mb-3">Informasi Jabatan</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="jabatan" class="form-label">Jabatan</label>
                                            <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?= htmlspecialchars($guru['JABATAN'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="bidang_studi" class="form-label">Bidang Studi</label>
                                            <input type="text" class="form-control" id="bidang_studi" name="bidang_studi" value="<?= htmlspecialchars($guru['BIDANG_STUDI'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                        <a href="index.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Batal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const currentPhoto = document.getElementById('currentPhoto');
            const placeholder = document.getElementById('imagePlaceholder');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                    }
                    if (currentPhoto) currentPhoto.style.display = 'none';
                    if (placeholder) placeholder.style.display = 'none';
                }

                reader.readAsDataURL(file);
            } else {
                if (preview) {
                    preview.src = '#';
                    preview.classList.add('d-none');
                }
                if (currentPhoto) currentPhoto.style.display = 'block';
                if (placeholder) placeholder.style.display = 'flex';
            }
        }

        // Handle remove photo checkbox
        const removePhotoCheckbox = document.getElementById('removePhoto');
        const currentPhoto = document.getElementById('currentPhoto');

        if (removePhotoCheckbox && currentPhoto) {
            removePhotoCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    currentPhoto.style.display = 'none';
                } else {
                    currentPhoto.style.display = 'block';
                }
            });
        }

        // Auto-format NIP input
        document.getElementById('nip').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Auto-format phone number
        document.getElementById('no_telepon').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Set max date for date of birth (today)
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dobInput = document.getElementById('tanggal_lahir');
            if (dobInput) {
                dobInput.max = today;
            }
        });
    </script>
</body>

</html>