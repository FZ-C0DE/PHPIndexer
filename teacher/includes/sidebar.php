<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-white sidebar shadow-sm">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>" 
                   href="../dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../classes/my-class.php">
                    <i class="fas fa-door-open me-2"></i>Kelas Saya
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../subjects/my-subjects.php">
                    <i class="fas fa-book me-2"></i>Mata Pelajaran
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#gradesCollapse" role="button">
                    <i class="fas fa-chart-line me-2"></i>Nilai
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="gradesCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="../grades/input.php">
                                <i class="fas fa-edit me-2"></i>Input Nilai
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../grades/manage.php">
                                <i class="fas fa-list me-2"></i>Kelola Nilai
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../attendance/student.php">
                    <i class="fas fa-calendar-check me-2"></i>Absensi Siswa
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../profile.php">
                    <i class="fas fa-user me-2"></i>Profil
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