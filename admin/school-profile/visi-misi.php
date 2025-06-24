<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vision = sanitize($_POST['vision']);
    $mission = sanitize($_POST['mission']);
    
    try {
        $stmt = $pdo->prepare("UPDATE school_profile SET vision = ?, mission = ? WHERE id = 1");
        $stmt->execute([$vision, $mission]);
        $success = 'Visi dan misi berhasil diperbarui';
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

$page_title = 'Visi & Misi Sekolah';
$custom_css = '../../assets/css/admin/dashboard.css';
include '../../includes/header.php';
?>

<?php include '../includes/navbar-admin.php'; ?>

<div class="container-fluid" style="margin-top: 80px;">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Visi & Misi Sekolah</h2>
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
                    <i class="fas fa-eye me-2"></i>Edit Visi & Misi
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-4">
                            <label for="vision" class="form-label">
                                <i class="fas fa-eye me-1"></i>Visi Sekolah *
                            </label>
                            <textarea class="form-control" id="vision" name="vision" rows="4" required><?= htmlspecialchars($school['vision'] ?? '') ?></textarea>
                            <div class="form-text">Tuliskan visi sekolah yang menggambarkan cita-cita masa depan</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="mission" class="form-label">
                                <i class="fas fa-bullseye me-1"></i>Misi Sekolah *
                            </label>
                            <textarea class="form-control" id="mission" name="mission" rows="6" required><?= htmlspecialchars($school['mission'] ?? '') ?></textarea>
                            <div class="form-text">Tuliskan misi sekolah dalam bentuk poin-poin (gunakan enter untuk baris baru)</div>
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
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-eye me-2"></i>Visi
                            </h5>
                            <p class="lead"><?= nl2br(htmlspecialchars($school['vision'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-bullseye me-2"></i>Misi
                            </h5>
                            <div><?= nl2br(htmlspecialchars($school['mission'])) ?></div>
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