<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $website = sanitize($_POST['website']);
    
    try {
        $stmt = $pdo->prepare("UPDATE school_profile SET address = ?, phone = ?, email = ?, website = ? WHERE id = 1");
        $stmt->execute([$address, $phone, $email, $website]);
        $success = 'Informasi kontak berhasil diperbarui';
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get current data
try {
    $stmt = $pdo->query("SELECT * FROM school_profile LIMIT 1");
    $school = $stmt->fetch();
} catch(PDOException $e) {
    $school = null;
    $error = 'Gagal mengambil data profil sekolah';
}

$page_title = 'Kontak Sekolah';
$custom_css = '../../assets/css/admin/dashboard.css';
include '../../includes/header.php';
?>

<?php include '../includes/navbar-admin.php'; ?>

<div class="container-fluid" style="margin-top: 80px;">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Kontak Sekolah</h2>
                <a href="../dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?= $success ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-address-book me-2"></i>Edit Kontak Sekolah
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-4">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Alamat Lengkap *
                            </label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($school['address'] ?? '') ?></textarea>
                            <div class="form-text">Alamat lengkap sekolah termasuk kode pos</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Nomor Telepon *
                                </label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($school['phone'] ?? '') ?>" required>
                                <div class="form-text">Format: (021) 1234567</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email Sekolah *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($school['email'] ?? '') ?>" required>
                                <div class="form-text">Email resmi sekolah</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="website" class="form-label">
                                <i class="fas fa-globe me-1"></i>Website Sekolah
                            </label>
                            <input type="url" class="form-control" id="website" name="website" 
                                   value="<?= htmlspecialchars($school['website'] ?? '') ?>" 
                                   placeholder="https://www.namasekolah.sch.id">
                            <div class="form-text">URL website resmi sekolah (opsional)</div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview -->
            <?php if ($school): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-preview me-2"></i>Preview Kontak
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>Alamat
                            </h6>
                            <p><?= nl2br(htmlspecialchars($school['address'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-phone me-2"></i>Telepon
                            </h6>
                            <p><?= htmlspecialchars($school['phone']) ?></p>
                            
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-envelope me-2"></i>Email
                            </h6>
                            <p><?= htmlspecialchars($school['email']) ?></p>
                            
                            <?php if ($school['website']): ?>
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-globe me-2"></i>Website
                            </h6>
                            <p><a href="<?= htmlspecialchars($school['website']) ?>" target="_blank"><?= htmlspecialchars($school['website']) ?></a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$custom_js = '../../assets/js/admin/dashboard.js';
include '../../includes/footer.php'; 
?>