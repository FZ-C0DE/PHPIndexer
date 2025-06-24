<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $history = sanitize($_POST['history']);
    $principal_name = sanitize($_POST['principal_name']);
    
    try {
        $stmt = $pdo->prepare("UPDATE school_profile SET history = ?, principal_name = ? WHERE id = 1");
        $stmt->execute([$history, $principal_name]);
        $success = 'Sejarah sekolah berhasil diperbarui';
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

$page_title = 'Sejarah Sekolah';
$custom_css = '../../assets/css/admin/dashboard.css';
include '../../includes/header.php';
?>

<?php include '../includes/navbar-admin.php'; ?>

<div class="container-fluid" style="margin-top: 80px;">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Sejarah Sekolah</h2>
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
                    <i class="fas fa-history me-2"></i>Edit Sejarah Sekolah
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-4">
                            <label for="history" class="form-label">
                                <i class="fas fa-history me-1"></i>Sejarah Sekolah *
                            </label>
                            <textarea class="form-control" id="history" name="history" rows="8" required><?= htmlspecialchars($school['history'] ?? '') ?></textarea>
                            <div class="form-text">Tuliskan sejarah lengkap pendirian dan perkembangan sekolah</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="principal_name" class="form-label">
                                <i class="fas fa-user-tie me-1"></i>Nama Kepala Sekolah *
                            </label>
                            <input type="text" class="form-control" id="principal_name" name="principal_name" 
                                   value="<?= htmlspecialchars($school['principal_name'] ?? '') ?>" required>
                            <div class="form-text">Nama lengkap kepala sekolah beserta gelar</div>
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
                    <i class="fas fa-preview me-2"></i>Preview
                </div>
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-history me-2"></i>Sejarah
                    </h5>
                    <div class="mb-4"><?= nl2br(htmlspecialchars($school['history'])) ?></div>
                    
                    <h6 class="text-primary mb-2">
                        <i class="fas fa-user-tie me-2"></i>Kepala Sekolah
                    </h6>
                    <p class="mb-0"><?= htmlspecialchars($school['principal_name']) ?></p>
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