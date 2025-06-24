<?php
require_once 'config/database.php';
require_once 'config/constants.php';

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

// Get gallery images
try {
    $stmt = $pdo->query("SELECT * FROM gallery WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 6");
    $gallery = $stmt->fetchAll();
} catch(PDOException $e) {
    $gallery = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($school['school_name']) ?> - Beranda</title>
    <meta name="description" content="<?= htmlspecialchars($school['school_name']) ?> - Sekolah unggulan dengan pendidikan berkualitas dan fasilitas modern">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/public/style.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/navbar-public.php'; ?>

    <!-- Hero Section -->
    <section id="beranda" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="hero-title fade-in">Selamat Datang di <?= htmlspecialchars($school['school_name']) ?></h1>
                    <p class="hero-subtitle fade-in"><?= htmlspecialchars($school['vision']) ?></p>
                    <div class="fade-in">
                        <a href="#profil" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-info-circle me-2"></i>Pelajari Lebih Lanjut
                        </a>
                        <a href="auth/login.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Sistem
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="fade-in">
                        <i class="fas fa-school" style="font-size: 8rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="profil" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title">Profil Sekolah</h2>
                    <p class="lead text-muted">Mengenal lebih dekat sekolah kami</p>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Vision -->
                <div class="col-lg-4">
                    <div class="card h-100 slide-up">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-eye fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Visi</h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($school['vision'])) ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Mission -->
                <div class="col-lg-4">
                    <div class="card h-100 slide-up">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-bullseye fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Misi</h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($school['mission'])) ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- History -->
                <div class="col-lg-4">
                    <div class="card h-100 slide-up">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-history fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Sejarah</h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($school['history'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="galeri" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title">Galeri Foto</h2>
                    <p class="lead text-muted">Dokumentasi kegiatan sekolah</p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php if (empty($gallery)): ?>
                    <!-- Default gallery items if no data -->
                    <div class="col-lg-4 col-md-6">
                        <div class="gallery-item">
                            <img src="https://via.placeholder.com/400x250/dc2626/ffffff?text=Kegiatan+Sekolah" alt="Kegiatan Sekolah" class="img-fluid">
                            <div class="p-3">
                                <h6 class="mb-2">Kegiatan Pembelajaran</h6>
                                <p class="text-muted mb-0">Suasana pembelajaran yang aktif dan kondusif</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="gallery-item">
                            <img src="https://via.placeholder.com/400x250/dc2626/ffffff?text=Fasilitas+Sekolah" alt="Fasilitas Sekolah" class="img-fluid">
                            <div class="p-3">
                                <h6 class="mb-2">Fasilitas Modern</h6>
                                <p class="text-muted mb-0">Fasilitas lengkap untuk mendukung pembelajaran</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="gallery-item">
                            <img src="https://via.placeholder.com/400x250/dc2626/ffffff?text=Prestasi+Siswa" alt="Prestasi Siswa" class="img-fluid">
                            <div class="p-3">
                                <h6 class="mb-2">Prestasi Siswa</h6>
                                <p class="text-muted mb-0">Berbagai prestasi yang telah diraih siswa</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach($gallery as $item): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="gallery-item">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="img-fluid">
                            <div class="p-3">
                                <h6 class="mb-2"><?= htmlspecialchars($item['title']) ?></h6>
                                <p class="text-muted mb-0"><?= htmlspecialchars($item['description']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title">Hubungi Kami</h2>
                    <p class="lead text-muted">Informasi kontak dan lokasi sekolah</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card text-center h-100">
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
                    <div class="card text-center h-100">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-phone fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Telepon</h5>
                            <p class="card-text">
                                <a href="tel:<?= htmlspecialchars($school['phone']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($school['phone']) ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card text-center h-100">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-envelope fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Email</h5>
                            <p class="card-text">
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
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#profil">Profil</a></li>
                        <li><a href="#galeri">Galeri</a></li>
                        <li><a href="#kontak">Kontak</a></li>
                        <li><a href="login.php">Login</a></li>
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
    
    <!-- Smooth Scrolling -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.slide-up').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    </script>
</body>
</html>