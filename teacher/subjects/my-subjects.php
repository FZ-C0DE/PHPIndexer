<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('teacher');

$error = '';
$success = '';

// Get teacher information
try {
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $teacher = $stmt->fetch();
    
    if (!$teacher) {
        $error = 'Data guru tidak ditemukan';
        header("Location: ../dashboard.php");
        exit();
    }
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $teacher = null;
}

// Get subjects taught by teacher
try {
    $stmt = $pdo->prepare("
        SELECT s.*, c.class_name, c.class_level, c.academic_year,
               (SELECT COUNT(*) FROM students WHERE class_id = c.id AND status = 'active') as student_count
        FROM subjects s 
        LEFT JOIN classes c ON s.class_id = c.id 
        WHERE s.teacher_id = ?
        ORDER BY c.class_level, c.class_name, s.subject_name
    ");
    $stmt->execute([$teacher['id']]);
    $subjects = $stmt->fetchAll();
} catch(PDOException $e) {
    $subjects = [];
    $error = 'Gagal mengambil data mata pelajaran';
}

// Get teaching statistics
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_subjects FROM subjects WHERE teacher_id = ?");
    $stmt->execute([$teacher['id']]);
    $total_subjects = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT SUM(credit_hours) as total_hours FROM subjects WHERE teacher_id = ?");
    $stmt->execute([$teacher['id']]);
    $total_hours = $stmt->fetchColumn() ?: 0;
    
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT c.id) as total_classes 
        FROM subjects s 
        JOIN classes c ON s.class_id = c.id 
        WHERE s.teacher_id = ?
    ");
    $stmt->execute([$teacher['id']]);
    $total_classes = $stmt->fetchColumn();
} catch(PDOException $e) {
    $total_subjects = $total_hours = $total_classes = 0;
}

$page_title = 'Mata Pelajaran Saya';
$custom_css = '../../assets/css/admin/dashboard.css';
include '../../includes/header.php';
?>

<?php include '../includes/navbar-teacher.php'; ?>

<div class="container-fluid" style="margin-top: 80px;">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Mata Pelajaran Saya</h2>
                <a href="../dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card stats-card text-center">
                        <div class="card-body">
                            <i class="fas fa-book fa-2x text-primary mb-2"></i>
                            <h4 class="stats-number"><?= $total_subjects ?></h4>
                            <p class="mb-0">Total Mata Pelajaran</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card stats-card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-success mb-2"></i>
                            <h4 class="stats-number"><?= $total_hours ?></h4>
                            <p class="mb-0">Total Jam Mengajar</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card stats-card text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-2x text-warning mb-2"></i>
                            <h4 class="stats-number"><?= $total_classes ?></h4>
                            <p class="mb-0">Kelas Diajar</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects List -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list me-2"></i>Daftar Mata Pelajaran
                </div>
                <div class="card-body">
                    <?php if (empty($subjects)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5>Belum Ada Mata Pelajaran</h5>
                            <p class="text-muted">Anda belum ditugaskan untuk mengajar mata pelajaran apapun.<br>Hubungi admin untuk mendapatkan penugasan mengajar.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Tahun Ajaran</th>
                                        <th>SKS</th>
                                        <th>Jumlah Siswa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($subjects as $subject): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($subject['subject_code']) ?></code></td>
                                        <td>
                                            <strong><?= htmlspecialchars($subject['subject_name']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?= htmlspecialchars($subject['class_level'] . ' ' . $subject['class_name']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($subject['academic_year']) ?></td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?= $subject['credit_hours'] ?> JP
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $subject['student_count'] ?> siswa
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="../attendance/student.php?subject_id=<?= $subject['id'] ?>" class="btn btn-outline-primary" title="Absensi">
                                                    <i class="fas fa-clock"></i>
                                                </a>
                                                <a href="../grades/manage.php?subject_id=<?= $subject['id'] ?>" class="btn btn-outline-success" title="Nilai">
                                                    <i class="fas fa-star"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <?php if (!empty($subjects)): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-2">
                            <a href="../attendance/student.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-clock fa-lg mb-1"></i><br>
                                <small>Input Absensi</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../grades/manage.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-star fa-lg mb-1"></i><br>
                                <small>Input Nilai</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../classes/my-class.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-users fa-lg mb-1"></i><br>
                                <small>Data Siswa</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../profile.php" class="btn btn-outline-warning w-100">
                                <i class="fas fa-user fa-lg mb-1"></i><br>
                                <small>Profil Saya</small>
                            </a>
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