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
                <a class="nav-link" href="../grades/view.php">
                    <i class="fas fa-chart-line me-2"></i>Nilai
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../attendance/view.php">
                    <i class="fas fa-calendar-check me-2"></i>Absensi
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../subjects/view.php">
                    <i class="fas fa-book me-2"></i>Mata Pelajaran
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