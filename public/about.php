<?php
require_once '../config/database.php';
require_once '../config/constants.php';

// Get school profile
try {
    $stmt = $pdo->query("SELECT * FROM school_profile LIMIT 1");
    $school = $stmt->fetch();
} catch(PDOException $e) {
    $school = [
        'school_name' => 'SMA Negeri 1 Jakarta',
        'vision' => 'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas',
        'mission' => 'Menyelenggarakan pendidikan yang berkualitas',
        'history' => 'Sekolah dengan sejarah panjang dalam dunia pendidikan',
        'address' => 'Jakarta, Indonesia',
        'phone' => '(021) 1234567',
        'email' => 'info@school.edu'
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Sekolah - <?= htmlspecialchars($school['school_name']) ?></title>
    <meta name="description" content="Profil lengkap <?= htmlspecialchars($school['school_name']) ?> - visi, misi, sejarah, dan informasi sekolah">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/public/style.css">
</head>
<body>
    <!-- Header -->
    <?php include '../includes/navbar-public.php'; ?>

    <!-- Page Header -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="hero-title fade-in">Profil Sekolah</h1>
                    <p class="hero-subtitle fade-in">Mengenal lebih dekat <?= htmlspecialchars($school['school_name']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Vision & Mission -->
                <div class="col-lg-6">
                    <div class="card h-100 slide-up">
                        <div class="card-body p-4">
                            <h3 class="mb-4 text-primary">
                                <i class="fas fa-eye me-2"></i>Visi
                            </h3>
                            <p class="lead"><?= nl2br(htmlspecialchars($school['vision'])) ?></p>
                            
                            <h3 class="mb-4 text-primary mt-5">
                                <i class="fas fa-bullseye me-2"></i>Misi
                            </h3>
                            <div class="text-justify"><?= nl2br(htmlspecialchars($school['mission'])) ?></div>
                        </div>
                    </div>
                </div>

                <!-- History -->
                <div class="col-lg-6">
                    <div class="card h-100 slide-up">
                        <div class="card-body p-4">
                            <h3 class="mb-4 text-primary">
                                <i class="fas fa-history me-2"></i>Sejarah
                            </h3>
                            <div class="text-justify"><?= nl2br(htmlspecialchars($school['history'])) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- School Information -->
            <div class="row g-4 mt-5">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Informasi Sekolah</h2>
                </div>
                
                <div class="col-lg-4">
                    <div class="card text-center h-100 slide-up">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-user-tie fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Kepala Sekolah</h5>
                            <p class="card-text"><?= htmlspecialchars($school['principal_name'] ?? 'Belum diisi') ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card text-center h-100 slide-up">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-map-marker-alt fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Alamat</h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($school['address'])) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card text-center h-100 slide-up">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-phone fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Kontak</h5>
                            <p class="card-text">
                                <strong>Telepon:</strong><br>
                                <a href="tel:<?= htmlspecialchars($school['phone']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($school['phone']) ?>
                                </a><br><br>
                                <strong>Email:</strong><br>
                                <a href="mailto:<?= htmlspecialchars($school['email']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($school['email']) ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-graduation-cap me-2"></i><?= htmlspecialchars($school['school_name']) ?></h5>
                    <p class="text-light"><?= htmlspecialchars($school['vision']) ?></p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Kontak</h5>
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($school['address']) ?></p>
                    <p class="mb-2"><i class="fas fa-phone me-2"></i><?= htmlspecialchars($school['phone']) ?></p>
                    <p class="mb-2"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($school['email']) ?></p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="../index.php">Beranda</a></li>
                        <li><a href="about.php">Profil</a></li>
                        <li><a href="gallery.php">Galeri</a></li>
                        <li><a href="contact.php">Kontak</a></li>
                        <li><a href="../auth/login.php">Login</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($school['school_name']) ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/public/main.js"></script>
</body>
</html>
<?php
require_once '../config/constants.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/public/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i><?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gallery.php">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="mb-4">Tentang <?= APP_NAME ?></h1>
                <p class="lead">Sistem Manajemen Sekolah yang modern dan terintegrasi.</p>
                
                <div class="card">
                    <div class="card-body">
                        <h3>Visi</h3>
                        <p>Menjadi sistem manajemen sekolah terdepan yang mendukung digitalisasi pendidikan.</p>
                        
                        <h3>Misi</h3>
                        <ul>
                            <li>Menyediakan platform terintegrasi untuk manajemen sekolah</li>
                            <li>Meningkatkan efisiensi administrasi sekolah</li>
                            <li>Mendukung proses pembelajaran yang modern</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
