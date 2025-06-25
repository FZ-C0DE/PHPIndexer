
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Sistem Manajemen Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/public/style.css">
    <style>
        :root {
            --primary-red: #dc2626;
            --primary-red-dark: #b91c1c;
            --primary-red-light: #fef2f2;
            --accent-gold: #f59e0b;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
            --gradient-primary: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Modern Navigation */
        .navbar-modern {
            background: rgba(220, 38, 38, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            padding: 1rem 0;
        }

        .navbar-scrolled {
            background: var(--primary-red);
            padding: 0.5rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.75rem;
            color: white !important;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* Hero Section with Parallax Effect */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc2626 0%, #7c2d12 50%, #991b1b 100%);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a"><stop offset="0" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="1" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><g><circle cx="200" cy="200" r="180" fill="url(%23a)"/><circle cx="800" cy="300" r="120" fill="url(%23a)"/><circle cx="300" cy="700" r="150" fill="url(%23a)"/><circle cx="700" cy="800" r="100" fill="url(%23a)"/></g></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            font-weight: 400;
        }

        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-hero-primary {
            background: white;
            color: var(--primary-red);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
            color: var(--primary-red);
        }

        .btn-hero-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-hero-outline:hover {
            background: white;
            color: var(--primary-red);
            transform: translateY(-3px);
        }

        /* Features Section */
        .features-section {
            padding: 6rem 0;
            background: var(--gray-50);
        }

        .section-title {
            font-size: 3rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 1rem;
            position: relative;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #6b7280;
            margin-bottom: 4rem;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2rem;
            color: white;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1rem;
        }

        .feature-text {
            color: #6b7280;
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            background: var(--gradient-primary);
            padding: 4rem 0;
            color: white;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* CTA Section */
        .cta-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .cta-text {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        .footer-modern {
            background: #111827;
            color: white;
            padding: 3rem 0 1.5rem;
        }

        .footer-brand {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer-text {
            opacity: 0.8;
            margin-bottom: 2rem;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        /* Animations */
        .fade-in {
            opacity: 0;
            animation: fadeIn 1s ease forwards;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        .slide-up {
            opacity: 0;
            transform: translateY(30px);
            animation: slideUp 0.8s ease forwards;
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .btn-hero {
                padding: 0.8rem 2rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Navigation -->
    <nav class="navbar navbar-expand-lg navbar-modern fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>EduManage Pro
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="public/about.php">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="public/gallery.php">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="public/contact.php">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content fade-in">
                        <h1 class="hero-title">
                            Revolusi Digital<br>
                            <span style="color: #fbbf24;">Pendidikan Modern</span>
                        </h1>
                        <p class="hero-subtitle">
                            Platform manajemen sekolah terdepan yang mengintegrasikan teknologi canggih untuk menciptakan ekosistem pendidikan yang efisien, transparan, dan berkelanjutan.
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3">
                            <a href="auth/login.php" class="btn-hero btn-hero-primary">
                                <i class="fas fa-rocket"></i>
                                Mulai Sekarang
                            </a>
                            <a href="public/about.php" class="btn-hero btn-hero-outline">
                                <i class="fas fa-play-circle"></i>
                                Pelajari Lebih Lanjut
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image slide-up" style="animation-delay: 0.3s;">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Modern Education" 
                             class="img-fluid rounded-4 shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item slide-up" style="animation-delay: 0.1s;">
                        <span class="stat-number" data-count="500">0</span>
                        <div class="stat-label">Siswa Aktif</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item slide-up" style="animation-delay: 0.2s;">
                        <span class="stat-number" data-count="50">0</span>
                        <div class="stat-label">Guru Profesional</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item slide-up" style="animation-delay: 0.3s;">
                        <span class="stat-number" data-count="25">0</span>
                        <div class="stat-label">Mata Pelajaran</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item slide-up" style="animation-delay: 0.4s;">
                        <span class="stat-number" data-count="98">0</span>
                        <div class="stat-label">Kepuasan (%)</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title slide-up">Fitur Unggulan</h2>
                <p class="section-subtitle slide-up" style="animation-delay: 0.1s;">
                    Solusi komprehensif untuk transformasi digital sekolah modern
                </p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card slide-up" style="animation-delay: 0.2s;">
                        <div class="feature-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3 class="feature-title">Manajemen Terintegrasi</h3>
                        <p class="feature-text">
                            Kelola seluruh aspek sekolah dalam satu platform terpadu dengan dashboard yang intuitif dan real-time analytics.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card slide-up" style="animation-delay: 0.3s;">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Analytics Cerdas</h3>
                        <p class="feature-text">
                            Pantau progres akademik dengan visualisasi data yang mudah dipahami dan laporan komprehensif.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card slide-up" style="animation-delay: 0.4s;">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Keamanan Terjamin</h3>
                        <p class="feature-text">
                            Perlindungan data tingkat enterprise dengan enkripsi SSL dan backup otomatis untuk keamanan maksimal.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card slide-up" style="animation-delay: 0.5s;">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-title">Mobile Responsive</h3>
                        <p class="feature-text">
                            Akses sistem dari perangkat apapun dengan desain responsive yang optimal di desktop, tablet, dan mobile.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card slide-up" style="animation-delay: 0.6s;">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="feature-title">Real-time Updates</h3>
                        <p class="feature-text">
                            Notifikasi instan untuk absensi, nilai, dan pengumuman penting dengan sistem real-time yang handal.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card slide-up" style="animation-delay: 0.7s;">
                        <div class="feature-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="feature-title">Customizable</h3>
                        <p class="feature-text">
                            Sesuaikan sistem dengan kebutuhan spesifik sekolah Anda dengan modul yang fleksibel dan scalable.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="slide-up">
                <h2 class="cta-title">Siap Mentransformasi Sekolah Anda?</h2>
                <p class="cta-text">
                    Bergabunglah dengan ratusan sekolah yang telah merasakan manfaat sistem manajemen modern kami. 
                    Mulai perjalanan digital Anda hari ini.
                </p>
                <a href="auth/login.php" class="btn-hero btn-hero-primary">
                    <i class="fas fa-arrow-right"></i>
                    Mulai Gratis Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="footer-brand">
                        <i class="fas fa-graduation-cap me-2"></i>EduManage Pro
                    </div>
                    <p class="footer-text">
                        Platform manajemen sekolah terdepan untuk pendidikan modern yang efisien dan berkelanjutan.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <p class="mb-0">&copy; 2024 EduManage Pro. Semua hak dilindungi.</p>
                    <p class="mb-0 mt-1" style="opacity: 0.7;">Dibuat dengan ❤️ untuk pendidikan Indonesia</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Counter animation
        function animateCounter(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.innerText = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    
                    // Counter animation for stats
                    if (entry.target.classList.contains('stat-item')) {
                        const counter = entry.target.querySelector('[data-count]');
                        if (counter && !counter.hasAttribute('data-animated')) {
                            const target = parseInt(counter.getAttribute('data-count'));
                            animateCounter(counter, 0, target, 2000);
                            counter.setAttribute('data-animated', 'true');
                        }
                    }
                }
            });
        }, observerOptions);

        // Observe all slide-up elements
        document.querySelectorAll('.slide-up').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });

        // Observe stat items
        document.querySelectorAll('.stat-item').forEach(el => {
            observer.observe(el);
        });

        // Smooth scrolling for navigation links
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
    </script>
</body>
</html>
