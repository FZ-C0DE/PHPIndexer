
<?php
require_once '../includes/auth-check.php';
require_once '../config/database.php';

requireRole('student');

// Get student information
try {
    $stmt = $pdo->prepare("SELECT s.*, c.class_name, c.class_level FROM students s LEFT JOIN classes c ON s.class_id = c.id WHERE s.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student_info = $stmt->fetch();
    
    if (!$student_info) {
        $error = 'Data siswa tidak ditemukan';
        header("Location: ../auth/login.php");
        exit();
    }
    
    // Get subjects for this student's class
    $stmt = $pdo->prepare("
        SELECT s.*, t.full_name as teacher_name, u.full_name as teacher_full_name
        FROM subjects s 
        LEFT JOIN teachers t ON s.teacher_id = t.id
        LEFT JOIN users u ON t.user_id = u.id
        WHERE s.class_id = ?
        ORDER BY s.subject_name
    ");
    $stmt->execute([$student_info['class_id']]);
    $subjects = $stmt->fetchAll();
    
    // Get recent grades
    $stmt = $pdo->prepare("
        SELECT g.*, s.subject_name, g.grade_value, g.grade_type
        FROM grades g
        JOIN subjects s ON g.subject_id = s.id
        WHERE g.student_id = ?
        ORDER BY g.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$student_info['id']]);
    $recent_grades = $stmt->fetchAll();
    
    // Get attendance summary
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_attendance,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
            SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count
        FROM attendances_student 
        WHERE student_id = ?
    ");
    $stmt->execute([$student_info['id']]);
    $attendance_summary = $stmt->fetch();
    
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $student_info = null;
    $subjects = [];
    $recent_grades = [];
    $attendance_summary = ['total_attendance' => 0, 'present_count' => 0, 'absent_count' => 0, 'late_count' => 0];
}

$page_title = 'Dashboard Siswa';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Siswa</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="badge bg-primary fs-6">
                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($student_info['full_name'] ?? 'Siswa') ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Student Info Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-id-card me-2"></i>Informasi Siswa
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>NIS:</strong> <?= htmlspecialchars($student_info['student_id'] ?? '-') ?></p>
                                    <p><strong>Nama:</strong> <?= htmlspecialchars($student_info['full_name'] ?? '-') ?></p>
                                    <p><strong>Kelas:</strong> <?= htmlspecialchars(($student_info['class_level'] ?? '') . ' ' . ($student_info['class_name'] ?? '-')) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Email:</strong> <?= htmlspecialchars($student_info['email'] ?? '-') ?></p>
                                    <p><strong>No. HP:</strong> <?= htmlspecialchars($student_info['phone'] ?? '-') ?></p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-success"><?= ucfirst($student_info['status'] ?? 'active') ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="stats-label">Mata Pelajaran</div>
                                    <div class="stats-number"><?= count($subjects) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-book fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="stats-label">Total Kehadiran</div>
                                    <div class="stats-number"><?= $attendance_summary['total_attendance'] ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-check fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="stats-label">Hadir</div>
                                    <div class="stats-number"><?= $attendance_summary['present_count'] ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="stats-label">Tidak Hadir</div>
                                    <div class="stats-number"><?= $attendance_summary['absent_count'] ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Subjects -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-book me-2"></i>Mata Pelajaran
                        </div>
                        <div class="card-body">
                            <?php if (empty($subjects)): ?>
                                <p class="text-muted text-center py-3">Belum ada mata pelajaran</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($subjects as $subject): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($subject['subject_name']) ?></h6>
                                                <small class="text-muted">
                                                    Guru: <?= htmlspecialchars($subject['teacher_full_name'] ?? 'Belum ditentukan') ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-info rounded-pill"><?= $subject['credit_hours'] ?> jam</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Grades -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-star me-2"></i>Nilai Terbaru
                        </div>
                        <div class="card-body">
                            <?php if (empty($recent_grades)): ?>
                                <p class="text-muted text-center py-3">Belum ada nilai</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($recent_grades as $grade): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($grade['subject_name']) ?></h6>
                                                <small class="text-muted">
                                                    <?= ucfirst($grade['grade_type']) ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill"><?= $grade['grade_value'] ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="grades/view.php" class="btn btn-outline-primary btn-sm">
                                        Lihat Semua Nilai
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-bolt me-2"></i>Menu Siswa
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="grades/view.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-star fa-2x mb-2"></i><br>
                                        Lihat Nilai
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="attendance/view.php" class="btn btn-outline-success w-100">
                                        <i class="fas fa-calendar-check fa-2x mb-2"></i><br>
                                        Riwayat Absensi
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="subjects/view.php" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-book fa-2x mb-2"></i><br>
                                        Mata Pelajaran
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="profile.php" class="btn btn-outline-info w-100">
                                        <i class="fas fa-user fa-2x mb-2"></i><br>
                                        Profil Saya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
