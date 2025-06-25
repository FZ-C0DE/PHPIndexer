
<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('student');

// Get student information
try {
    $stmt = $pdo->prepare("SELECT s.*, c.class_name, c.class_level FROM students s LEFT JOIN classes c ON s.class_id = c.id WHERE s.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student_info = $stmt->fetch();
    
    if (!$student_info) {
        header("Location: ../../auth/login.php");
        exit();
    }
    
    // Get subjects for this student's class
    $stmt = $pdo->prepare("
        SELECT s.*, t.full_name as teacher_name, u.full_name as teacher_full_name, c.class_name, c.class_level
        FROM subjects s 
        LEFT JOIN teachers t ON s.teacher_id = t.id
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE s.class_id = ?
        ORDER BY s.subject_name
    ");
    $stmt->execute([$student_info['class_id']]);
    $subjects = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $subjects = [];
}

$page_title = 'Mata Pelajaran';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Mata Pelajaran</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Class Info -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-school me-2"></i>Informasi Kelas
                        </div>
                        <div class="card-body">
                            <h5><?= htmlspecialchars(($student_info['class_level'] ?? '') . ' ' . ($student_info['class_name'] ?? 'Belum ditentukan')) ?></h5>
                            <p class="text-muted">Total Mata Pelajaran: <?= count($subjects) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects Grid -->
            <div class="row">
                <?php if (empty($subjects)): ?>
                    <div class="col-12">
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Mata Pelajaran</h5>
                            <p class="text-muted">Mata pelajaran akan muncul setelah admin mengatur kurikulum kelas</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach($subjects as $subject): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-book text-primary me-2"></i>
                                    <?= htmlspecialchars($subject['subject_name']) ?>
                                </h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        Guru: <?= htmlspecialchars($subject['teacher_full_name'] ?? 'Belum ditentukan') ?>
                                    </small>
                                </p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?= $subject['credit_hours'] ?> jam pelajaran
                                    </small>
                                </p>
                                <?php if (!empty($subject['description'])): ?>
                                    <p class="card-text"><?= htmlspecialchars($subject['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between">
                                    <a href="../grades/view.php?subject=<?= $subject['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-star me-1"></i>Lihat Nilai
                                    </a>
                                    <span class="badge bg-info"><?= $subject['credit_hours'] ?>h</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
