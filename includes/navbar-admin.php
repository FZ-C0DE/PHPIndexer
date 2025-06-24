<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="../admin/dashboard.php">
            <i class="fas fa-graduation-cap me-2"></i>
            <?= APP_NAME ?> - Admin
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../admin/profile.php">
                            <i class="fas fa-user me-2"></i>Profil
                        </a></li>
                        <li><a class="dropdown-item" href="../admin/settings.php">
                            <i class="fas fa-cog me-2"></i>Pengaturan
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../public/index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Lihat Website
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>