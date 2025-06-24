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
                    <span>Manajemen Pengguna</span>
                </h6>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#teachersMenu">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Data Guru
                </a>
                <div class="collapse" id="teachersMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="teachers/manage.php">
                                <i class="fas fa-list"></i> Kelola Guru
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="teachers/add-edit.php?action=add">
                                <i class="fas fa-plus"></i> Tambah Guru
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#studentsMenu">
                    <i class="fas fa-user-graduate"></i>
                    Data Siswa
                </a>
                <div class="collapse" id="studentsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="students/manage.php">
                                <i class="fas fa-list"></i> Kelola Siswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="students/add-edit.php?action=add">
                                <i class="fas fa-plus"></i> Tambah Siswa
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <li class="nav-item">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Akademik</span>
                </h6>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#classesMenu">
                    <i class="fas fa-school"></i>
                    Data Kelas
                </a>
                <div class="collapse" id="classesMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="classes/manage.php">
                                <i class="fas fa-list"></i> Kelola Kelas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="classes/add-edit.php?action=add">
                                <i class="fas fa-plus"></i> Tambah Kelas
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#subjectsMenu">
                    <i class="fas fa-book"></i>
                    Mata Pelajaran
                </a>
                <div class="collapse" id="subjectsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="subjects/manage.php">
                                <i class="fas fa-list"></i> Kelola Mata Pelajaran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="subjects/add-edit.php?action=add">
                                <i class="fas fa-plus"></i> Tambah Mata Pelajaran
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'teacher.php' ? 'active' : '' ?>" href="attendance/teacher.php">
                    <i class="fas fa-clock"></i>
                    Absensi Guru
                </a>
            </li>
            
            <li class="nav-item">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Website</span>
                </h6>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'school_profile.php' ? 'active' : '' ?>" href="school_profile.php">
                    <i class="fas fa-info-circle"></i>
                    Profil Sekolah
                </a>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#galleryMenu">
                    <i class="fas fa-images"></i>
                    Galeri
                </a>
                <div class="collapse" id="galleryMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="gallery/manage.php">
                                <i class="fas fa-list"></i> Kelola Galeri
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gallery/upload.php">
                                <i class="fas fa-cloud-upload-alt"></i> Upload Foto
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <li class="nav-item">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Sistem</span>
                </h6>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage.php' && strpos($_SERVER['REQUEST_URI'], 'users') !== false ? 'active' : '' ?>" href="users/manage.php">
                    <i class="fas fa-users"></i>
                    Manajemen User
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