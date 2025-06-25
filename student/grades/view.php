
<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('student');

// Get student information
try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student_info = $stmt->fetch();
    
    if (!$student_info) {
        header("Location: ../../auth/login.php");
        exit();
    }
    
    // Get all grades for this student
    $stmt = $pdo->prepare("
        SELECT g.*, s.subject_name, s.credit_hours, t.full_name as teacher_name, u.full_name as teacher_full_name
        FROM grades g
        JOIN subjects s ON g.subject_id = s.id
        LEFT JOIN teachers t ON s.teacher_id = t.id
        LEFT JOIN users u ON t.user_id = u.id
        WHERE g.student_id = ?
        ORDER BY s.subject_name, g.grade_type, g.created_at DESC
    ");
    $stmt->execute([$student_info['id']]);
    $grades = $stmt->fetchAll();
    
    // Calculate average
    $total_grades = 0;
    $grade_count = 0;
    foreach ($grades as $grade) {
        if (is_numeric($grade['grade_value'])) {
            $total_grades += $grade['grade_value'];
            $grade_count++;
        }
    }
    $average = $grade_count > 0 ? round($total_grades / $grade_count, 2) : 0;
    
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $grades = [];
    $average = 0;
}

$page_title = 'Nilai Saya';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Nilai Saya</h1>
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

            <!-- Summary Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-2"></i>Ringkasan Nilai
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h3 class="text-primary"><?= count($grades) ?></h3>
                                    <p class="text-muted">Total Nilai</p>
                                </div>
                                <div class="col-md-3">
                                    <h3 class="text-success"><?= $average ?></h3>
                                    <p class="text-muted">Rata-rata</p>
                                </div>
                                <div class="col-md-3">
                                    <h3 class="text-info"><?= count(array_unique(array_column($grades, 'subject_id'))) ?></h3>
                                    <p class="text-muted">Mata Pelajaran</p>
                                </div>
                                <div class="col-md-3">
                                    <h3 class="<?= $average >= 75 ? 'text-success' : 'text-warning' ?>">
                                        <?= $average >= 75 ? 'Baik' : 'Perlu Perbaikan' ?>
                                    </h3>
                                    <p class="text-muted">Status</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grades Table -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list me-2"></i>Daftar Nilai
                </div>
                <div class="card-body">
                    <?php if (empty($grades)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Nilai</h5>
                            <p class="text-muted">Nilai akan muncul setelah guru memasukkan penilaian</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                        <th>Jenis Nilai</th>
                                        <th>Nilai</th>
                                        <th>Tanggal Input</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($grades as $grade): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($grade['subject_name']) ?></td>
                                        <td><?= htmlspecialchars($grade['teacher_full_name'] ?? 'Belum ditentukan') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $grade['grade_type'] == 'quiz' ? 'info' : ($grade['grade_type'] == 'assignment' ? 'warning' : 'primary') ?>">
                                                <?= ucfirst($grade['grade_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $grade['grade_value'] >= 75 ? 'success' : 'danger' ?> fs-6">
                                                <?= $grade['grade_value'] ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($grade['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($grade['notes'] ?? '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
