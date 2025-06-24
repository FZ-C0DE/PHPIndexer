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
        'address' => 'Jakarta, Indonesia',
        'phone' => '(021) 1234567',
        'email' => 'info@school.edu',
        'website' => 'https://www.sman1jakarta.sch.id'
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - <?= htmlspecialchars($school['school_name']) ?></title>
    <meta name="description" content="Hubungi <?= htmlspecialchars($school['school_name']) ?> untuk informasi lebih lanjut tentang sekolah">
    
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
                    <h1 class="hero-title fade-in">Hubungi Kami</h1>
                    <p class="hero-subtitle fade-in">Informasi kontak dan lokasi <?= htmlspecialchars($school['school_name']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Contact Information -->
                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 slide-up">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-map-marker-alt fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Alamat</h5>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($school['address'])) ?></p>
                                    <a href="https://maps.google.com/?q=<?= urlencode($school['address']) ?>" 
                                       target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i>Lihat di Map
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 slide-up">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-phone fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Telepon</h5>
                                    <p class="card-text">
                                        <a href="tel:<?= htmlspecialchars($school['phone']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($school['phone']) ?>
                                        </a>
                                    </p>
                                    <a href="tel:<?= htmlspecialchars($school['phone']) ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-phone me-1"></i>Telepon Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 slide-up">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-envelope fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Email</h5>
                                    <p class="card-text">
                                        <a href="mailto:<?= htmlspecialchars($school['email']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($school['email']) ?>
                                        </a>
                                    </p>
                                    <a href="mailto:<?= htmlspecialchars($school['email']) ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-envelope me-1"></i>Kirim Email
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 slide-up">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-globe fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Website</h5>
                                    <p class="card-text">
                                        <a href="<?= htmlspecialchars($school['website'] ?? '#') ?>" 
                                           class="text-decoration-none" target="_blank">
                                            <?= htmlspecialchars($school['website'] ?? 'Coming Soon') ?>
                                        </a>
                                    </p>
                                    <?php if ($school['website'] ?? ''): ?>
                                    <a href="<?= htmlspecialchars($school['website']) ?>" 
                                       target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i>Kunjungi Website
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="card mt-5 slide-up">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Kirim Pesan</h5>
                        </div>
                        <div class="card-body">
                            <form id="contactForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nama Lengkap *</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Telepon</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="subject" class="form-label">Subjek *</label>
                                        <select class="form-select" id="subject" name="subject" required>
                                            <option value="">Pilih Subjek</option>
                                            <option value="informasi">Informasi Umum</option>
                                            <option value="pendaftaran">Pendaftaran Siswa</option>
                                            <option value="akademik">Informasi Akademik</option>
                                            <option value="fasilitas">Fasilitas Sekolah</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Pesan *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i>Kirim Pesan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- School Info Sidebar -->
                <div class="col-lg-4">
                    <div class="card slide-up">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Sekolah</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold"><?= htmlspecialchars($school['school_name']) ?></h6>
                            
                            <hr>
                            
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">Jam Operasional</h6>
                                <small class="text-muted">
                                    <strong>Senin - Jumat:</strong> 07:00 - 15:30<br>
                                    <strong>Sabtu:</strong> 07:00 - 12:00<br>
                                    <strong>Minggu:</strong> Tutup
                                </small>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">Fasilitas</h6>
                                <ul class="list-unstyled small text-muted">
                                    <li><i class="fas fa-check text-success me-2"></i>Laboratorium Komputer</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Laboratorium IPA</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Perpustakaan</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Lapangan Olahraga</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Kantin</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Masjid</li>
                                </ul>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center">
                                <h6 class="text-primary mb-2">Akses Login</h6>
                                <p class="small text-muted mb-3">Untuk guru dan admin sekolah</p>
                                <a href="../auth/login.php" class="btn btn-primary w-100">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login Sistem
                                </a>
                            </div>
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
    
    <script>
        // Contact form handling
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            submitBtn.disabled = true;
            
            // Simulate form submission (replace with actual form handling)
            setTimeout(function() {
                alert('Terima kasih! Pesan Anda telah dikirim. Kami akan menghubungi Anda segera.');
                document.getElementById('contactForm').reset();
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
    </script>
</body>
</html>