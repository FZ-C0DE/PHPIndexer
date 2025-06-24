<?php
require_once '../config/functions.php';
require_once '../config/database.php';

$error = '';
$success = '';
$step = $_GET['step'] ?? 'request';

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'request') {
    $email = sanitize($_POST['email']);
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id, username, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token (you would typically create a password_resets table)
            // For demo purposes, we'll just show a success message
            $success = 'Jika email terdaftar, link reset password telah dikirim ke email Anda.';
            
            // In real implementation, you would:
            // 1. Store token in database with expiration
            // 2. Send email with reset link
            // 3. Handle the reset process when user clicks the link
            
        } else {
            // Don't reveal if email exists or not for security
            $success = 'Jika email terdaftar, link reset password telah dikirim ke email Anda.';
        }
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    }
}

$page_title = 'Reset Password';
$custom_css = '../assets/css/admin/dashboard.css';
include '../includes/header.php';
?>

<div class="login-container">
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
            <h4><?= APP_NAME ?></h4>
            <p class="text-muted">Reset Password</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                <div class="mt-3">
                    <a href="login.php" class="btn btn-primary btn-sm">Kembali ke Login</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-1"></i>Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" required 
                           placeholder="Masukkan email Anda">
                    <div class="form-text">
                        Masukkan email yang terdaftar untuk menerima link reset password
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-paper-plane me-1"></i>Kirim Link Reset
                </button>
            </form>
        <?php endif; ?>

        <div class="text-center">
            <small class="text-muted">
                <a href="login.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
                </a>
            </small>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-muted">
                <a href="../public/index.php" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i>Kembali ke Beranda
                </a>
            </small>
        </div>
    </div>
</div>

<style>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
    padding: 2rem;
}

.login-card {
    background: white;
    padding: 3rem;
    border-radius: 1rem;
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
    width: 100%;
    max-width: 400px;
}

.form-control {
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    padding: 0.75rem 1rem;
}

.form-control:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.15);
}

.btn-primary {
    background: var(--gradient-primary);
    border: none;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    font-weight: 500;
}
</style>

<?php 
$custom_js = '../assets/js/admin/dashboard.js';
include '../includes/footer.php'; 
?>