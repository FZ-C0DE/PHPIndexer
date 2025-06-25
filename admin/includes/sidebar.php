<?php
// Ensure constants are loaded
if (!defined('BASE_URL')) {
    require_once '../config/constants.php';
}
// Sidebar navigation for admin
?>
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-white sidebar shadow-sm">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>" 
                   href="<?= ($_SERVER['REQUEST_URI'] == '/projectAi/PHPIndexer/admin/dashboard.php') ? 'dashboard.php' : '../dashboard.php' ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#managementCollapse" role="button">
                    <i class="fas fa-cogs me-2"></i>Manajemen
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="managementCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="../teachers/manage.php">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Guru
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../students/manage.php">
                                <i class="fas fa-user-graduate me-2"></i>Siswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../classes/manage.php">
                                <i class="fas fa-door-open me-2"></i>Kelas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../subjects/manage.php">
                                <i class="fas fa-book me-2"></i>Mata Pelajaran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../users/manage.php">
                                <i class="fas fa-users me-2"></i>User
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#schoolProfileCollapse" role="button">
                    <i class="fas fa-school me-2"></i>Profil Sekolah
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="schoolProfileCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="../school-profile/visi-misi.php">
                                <i class="fas fa-eye me-2"></i>Visi & Misi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../school-profile/sejarah.php">
                                <i class="fas fa-history me-2"></i>Sejarah
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../school-profile/kontak.php">
                                <i class="fas fa-phone me-2"></i>Kontak
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../gallery/manage.php">
                    <i class="fas fa-images me-2"></i>Galeri
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../attendance/teacher.php">
                    <i class="fas fa-calendar-check me-2"></i>Absensi
                </a>
            </li>
        </ul>

        <hr>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="../../auth/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>