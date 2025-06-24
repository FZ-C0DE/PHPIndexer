<?php
require_once '../config/database.php';
require_once '../config/constants.php';

// Get gallery images
try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY is_featured DESC, created_at DESC");
    $gallery = $stmt->fetchAll();
} catch(PDOException $e) {
    $gallery = [];
}

// Get school profile for footer
try {
    $stmt = $pdo->query("SELECT * FROM school_profile LIMIT 1");
    $school = $stmt->fetch();
} catch(PDOException $e) {
    $school = [
        'school_name' => 'SMA Negeri 1 Jakarta',
        'vision' => 'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas',
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
    <title>Galeri - <?= htmlspecialchars($school['school_name']) ?></title>
    <meta name="description" content="Galeri foto kegiatan dan fasilitas <?= htmlspecialchars($school['school_name']) ?>">
    
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
                    <h1 class="hero-title fade-in">Galeri Foto</h1>
                    <p class="hero-subtitle fade-in">Dokumentasi kegiatan dan fasilitas sekolah</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Content -->
    <section class="py-5">
        <div class="container">
            <?php if (empty($gallery)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <h5>Galeri Belum Tersedia</h5>
                                <p class="text-muted">Foto-foto kegiatan sekolah akan segera ditampilkan di sini.</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Featured Images -->
                <?php
                $featured = array_filter($gallery, function($item) {
                    return $item['is_featured'] == 1;
                });
                ?>
                
                <?php if (!empty($featured)): ?>
                <div class="mb-5">
                    <h2 class="section-title mb-4">Foto Unggulan</h2>
                    <div class="row g-4">
                        <?php foreach($featured as $item): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="gallery-item slide-up">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($item['title']) ?>" 
                                     class="img-fluid" style="cursor: pointer;">
                                <div class="p-3 bg-white">
                                    <h6 class="mb-2 d-flex justify-content-between">
                                        <?= htmlspecialchars($item['title']) ?>
                                        <span class="badge bg-primary">Unggulan</span>
                                    </h6>
                                    <p class="text-muted mb-2"><?= htmlspecialchars($item['description']) ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>
                                        <?= ucfirst(htmlspecialchars($item['category'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- All Images -->
                <div class="mb-5">
                    <h2 class="section-title mb-4">Semua Foto</h2>
                    
                    <!-- Category Filter -->
                    <div class="mb-4 text-center">
                        <button class="btn btn-outline-primary me-2 mb-2 filter-btn active" data-filter="all">
                            Semua
                        </button>
                        <?php
                        $categories = array_unique(array_column($gallery, 'category'));
                        $category_labels = [
                            'general' => 'Umum',
                            'activities' => 'Kegiatan',
                            'facilities' => 'Fasilitas',
                            'achievements' => 'Prestasi',
                            'events' => 'Acara'
                        ];
                        ?>
                        <?php foreach($categories as $category): ?>
                        <button class="btn btn-outline-primary me-2 mb-2 filter-btn" data-filter="<?= $category ?>">
                            <?= $category_labels[$category] ?? ucfirst($category) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="row g-4" id="gallery-container">
                        <?php foreach($gallery as $item): ?>
                        <div class="col-lg-4 col-md-6 mb-4 gallery-item-container slide-up" data-category="<?= $item['category'] ?>">
                            <div class="gallery-item">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($item['title']) ?>" 
                                     class="img-fluid" style="cursor: pointer;">
                                <div class="p-3 bg-white">
                                    <h6 class="mb-2 d-flex justify-content-between">
                                        <?= htmlspecialchars($item['title']) ?>
                                        <?php if ($item['is_featured']): ?>
                                            <span class="badge bg-primary">Unggulan</span>
                                        <?php endif; ?>
                                    </h6>
                                    <p class="text-muted mb-2"><?= htmlspecialchars($item['description']) ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>
                                        <?= $category_labels[$item['category']] ?? ucfirst($item['category']) ?>
                                        <span class="float-end">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d/m/Y', strtotime($item['created_at'])) ?>
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
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
                        <li><a href="index.php">Beranda</a></li>
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
    
    <script>
        // Gallery filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const galleryItems = document.querySelectorAll('.gallery-item-container');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    
                    // Update active button
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter items
                    galleryItems.forEach(item => {
                        if (filter === 'all' || item.dataset.category === filter) {
                            item.style.display = 'block';
                            item.style.animation = 'fadeIn 0.5s ease-in';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>