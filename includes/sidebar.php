<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'dashboard' ? 'active' : '' ?>"
                    href="../../pages/dashboard/index.php">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <!-- Data Guru -->
                <li class="nav-item">
                    <a class="nav-link <?= basename(dirname($_SERVER['PHP_SELF'])) == 'guru' ? 'active' : '' ?>"
                        href="../../pages/guru/index.php">
                        <i class="bi bi-person-badge me-2"></i> Data Guru
                    </a>
                </li>

                <!-- Data Siswa (commented out for future use) -->
                <!--
                <li class="nav-item">
                    <a class="nav-link" href="../../pages/siswa/index.php">
                        <i class="bi bi-people me-2"></i> Data Siswa
                    </a>
                </li>
                -->

                <!-- Other Admin Menu Items -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-book me-2"></i> Mata Pelajaran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-calendar-week me-2"></i> Jadwal
                    </a>
                </li>
            <?php else: ?>
                <!-- Menu untuk user non-admin -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-journal-check me-2"></i> Absensi Saya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-graph-up me-2"></i> Nilai Siswa
                    </a>
                </li>
            <?php endif; ?>

            <!-- Common Menu Items -->
            <li class="nav-item mt-3">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted">
                    <span>Akun</span>
                </h6>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../../pages/profile/index.php">
                    <i class="bi bi-person me-2"></i> Profil Saya
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../../actions/auth/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>