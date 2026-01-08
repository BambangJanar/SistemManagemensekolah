<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini';
    header('Location: ../../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Guru - Sistem Manajemen Sekolah</title>
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
            display: none;
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
    </style>
</head>

<body>
    <?php include('../../includes/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <?php include('../../includes/sidebar.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tambah Data Guru</h1>
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
                        <form action="../../actions/guru/store.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <h5 class="mb-3">Foto Profil</h5>
                                    <div class="mb-3 text-center">
                                        <img id="imagePreview" class="profile-preview mb-2" alt="Preview">
                                        <div id="imagePlaceholder" class="profile-placeholder mb-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <input type="file" class="form-control d-none" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                        <label for="foto" class="btn btn-sm btn-outline-secondary w-100">
                                            <i class="fas fa-upload"></i> Pilih Foto
                                        </label>
                                        <div class="form-text">Format: JPG, PNG, maks 2MB</div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="mb-3">Informasi Pribadi</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required value="<?= isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="username" name="username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="nip" class="form-label">NIP</label>
                                            <input type="text" class="form-control" id="nip" name="nip" value="<?= isset($_POST['nip']) ? htmlspecialchars($_POST['nip']) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                                <option value="L" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                                <option value="P" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="<?= isset($_POST['tempat_lahir']) ? htmlspecialchars($_POST['tempat_lahir']) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= isset($_POST['tanggal_lahir']) ? htmlspecialchars($_POST['tanggal_lahir']) : ''; ?>">
                                        </div>
                                    </div>

                                    <h5 class="mt-4 mb-3">Informasi Kontak</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="no_telepon" class="form-label">No. Telepon</label>
                                            <input type="tel" class="form-control" id="no_telepon" name="no_telepon" value="<?= isset($_POST['no_telepon']) ? htmlspecialchars($_POST['no_telepon']) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                        </div>
                                    </div>

                                    <h5 class="mt-4 mb-3">Informasi Jabatan</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="aktif" <?= (isset($_POST['status']) && $_POST['status'] === 'aktif') ? 'selected' : 'selected'; ?>>Aktif</option>
                                                <option value="nonaktif" <?= (isset($_POST['status']) && $_POST['status'] === 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                                                <option value="pensiun" <?= (isset($_POST['status']) && $_POST['status'] === 'pensiun') ? 'selected' : ''; ?>>Pensiun</option>
                                                <option value="pindah" <?= (isset($_POST['status']) && $_POST['status'] === 'pindah') ? 'selected' : ''; ?>>Pindah</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="jabatan" class="form-label">Jabatan</label>
                                            <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?= isset($_POST['jabatan']) ? htmlspecialchars($_POST['jabatan']) : ''; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="bidang_studi" class="form-label">Bidang Studi</label>
                                            <input type="text" class="form-control" id="bidang_studi" name="bidang_studi" value="<?= isset($_POST['bidang_studi']) ? htmlspecialchars($_POST['bidang_studi']) : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                        <button type="reset" class="btn btn-secondary">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
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
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');

            form.addEventListener('submit', function(e) {
                let valid = true;

                // Validate required fields
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        valid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Validate email format if provided
                const emailField = document.getElementById('email');
                if (emailField.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value)) {
                    valid = false;
                    emailField.classList.add('is-invalid');
                    const feedback = emailField.nextElementSibling || document.createElement('div');
                    if (!emailField.nextElementSibling) {
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'Format email tidak valid';
                        emailField.parentNode.insertBefore(feedback, emailField.nextSibling);
                    }
                } else if (emailField.nextElementSibling?.classList.contains('invalid-feedback')) {
                    emailField.classList.remove('is-invalid');
                    emailField.nextElementSibling.remove();
                }

                if (!valid) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Scroll to first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });

            // Remove invalid class when user starts typing
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.checkValidity()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });

        // Image preview
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }

                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
                placeholder.style.display = 'flex';
            }
        }

        // Trigger file input when clicking on the placeholder
        document.getElementById('imagePlaceholder').addEventListener('click', function() {
            document.getElementById('foto').click();
        });

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
            document.getElementById('tanggal_lahir').max = today;
        });
    </script>
</body>

</html>