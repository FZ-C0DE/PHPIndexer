
<?php
require_once '../includes/auth-check.php';
require_once '../config/database.php';

requireRole('student');

$success = '';
$error = '';

// Get student information
try {
    $stmt = $pdo->prepare("
        SELECT s.*, u.username, u.email, u.full_name, c.class_name, c.class_level 
        FROM students s 
        JOIN users u ON s.user_id = u.id
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE s.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $student_info = $stmt->fetch();
    
    if (!$student_info) {
        header("Location: ../auth/login.php");
        exit();
    }
    
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $student_info = null;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    try {
        $stmt = $pdo->prepare("UPDATE students SET phone = ?, address = ? WHERE user_id = ?");
        $stmt->execute([$phone, $address, $_SESSION['user_id']]);
        
        $success = 'Profil berhasil diperbarui';
        
        // Refresh data
        $stmt = $pdo->prepare("
            SELECT s.*, u.username, u.email, u.full_name, c.class_name, c.class_level 
            FROM students s 
            JOIN users u ON s.user_id = u.id
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $student_info = $stmt->fetch();
        
    } catch(PDOException $e) {
        $error = 'Gagal memperbarui profil';
    }
}

$page_title = 'Profil Saya';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Profil Saya</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?= $success ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Profile Information -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-user me-2"></i>Informasi Profil
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIS</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($student_info['student_id'] ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($student_info['full_name'] ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($student_info['username'] ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="<?= htmlspecialchars($student_info['email'] ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kelas</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars(($student_info['class_level'] ?? '') . ' ' . ($student_info['class_name'] ?? 'Belum ditentukan')) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No. HP</label>
                                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($student_info['phone'] ?? '') ?>" placeholder="Masukkan nomor HP">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" name="address" rows="3" placeholder="Masukkan alamat"><?= htmlspecialchars($student_info['address'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-bolt me-2"></i>Menu Cepat
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="grades/view.php" class="btn btn-outline-primary">
                                    <i class="fas fa-star me-2"></i>Lihat Nilai
                                </a>
                                <a href="attendance/view.php" class="btn btn-outline-success">
                                    <i class="fas fa-calendar-check me-2"></i>Riwayat Absensi
                                </a>
                                <a href="subjects/view.php" class="btn btn-outline-warning">
                                    <i class="fas fa-book me-2"></i>Mata Pelajaran
                                </a>
                                <a href="../auth/logout.php" class="btn btn-outline-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
