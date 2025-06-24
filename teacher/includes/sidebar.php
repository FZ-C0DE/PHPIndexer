<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Pembelajaran</span>
                </h6>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : '' ?>" href="students.php">
                    <i class="fas fa-user-graduate"></i>
                    Data Siswa
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : '' ?>" href="attendance.php">
                    <i class="fas fa-clock"></i>
                    Absensi Siswa
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'grades.php' ? 'active' : '' ?>" href="grades.php">
                    <i class="fas fa-star"></i>
                    Nilai Siswa
                </a>
            </li>
            
            <li class="nav-item">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Profil</span>
                </h6>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>" href="profile.php">
                    <i class="fas fa-user"></i>
                    Profil Saya
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    background: linear-gradient(180deg, var(--white) 0%, var(--gray-50) 100%);
    border-right: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
    min-height: calc(100vh - 76px);
}

.sidebar .nav-link {
    color: var(--gray-700);
    padding: 0.75rem 1.25rem;
    border-radius: 0.5rem;
    margin: 0.25rem 0.75rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
    color: white;
}

.sidebar .nav-link i {
    margin-right: 0.75rem;
    width: 20px;
    text-align: center;
}

.sidebar-heading {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.1em;
}
</style>