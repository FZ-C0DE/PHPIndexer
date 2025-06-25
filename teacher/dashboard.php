<?php
require_once '../includes/auth-check.php';
require_once '../config/functions.php';

requireRole('teacher');

// Get teacher information
try {
    $stmt = $pdo->prepare("SELECT t.*, u.full_name FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $teacher_info = $stmt->fetch();
    
    if (!$teacher_info) {
        $error = 'Data guru tidak ditemukan';
        header("Location: ../login.php");
        exit();
    }
    
    // Get classes where teacher is homeroom teacher
    $stmt = $pdo->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM students WHERE class_id = c.id AND status = 'active') as student_count
        FROM classes c 
        WHERE c.teacher_id = ?
    ");
    $stmt->execute([$teacher_info['id']]);
    $homeroom_classes = $stmt->fetchAll();
    
    // Get subjects taught by teacher
    $stmt = $pdo->prepare("
        SELECT s.*, c.class_name, c.class_level
        FROM subjects s 
        LEFT JOIN classes c ON s.class_id = c.id 
        WHERE s.teacher_id = ?
        ORDER BY s.subject_name
    ");
    $stmt->execute([$teacher_info['id']]);
    $subjects = $stmt->fetchAll();
    
    // Get recent student attendance (if homeroom teacher)
    $recent_attendance = [];
    if (!empty($homeroom_classes)) {
        $class_ids = array_column($homeroom_classes, 'id');
        $placeholders = str_repeat('?,', count($class_ids) - 1) . '?';
        
        $stmt = $pdo->prepare("
            SELECT s.full_name, asa.attendance_date, asa.status
            FROM attendances_student asa
            JOIN students s ON asa.student_id = s.id
            WHERE s.class_id IN ($placeholders)
            ORDER BY asa.attendance_date DESC, s.full_name
            LIMIT 10
        ");
        $stmt->execute($class_ids);
        $recent_attendance = $stmt->fetchAll();
    }
    
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $teacher_info = null;
    $homeroom_classes = [];
    $subjects = [];
    $recent_attendance = [];
}

$page_title = 'Dashboard Guru';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="badge bg-primary fs-6">
                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($teacher_info['full_name'] ?? 'Guru') ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="stats-label">Kelas Diampu</div>
                                    <div class="stats-number"><?= count($homeroom_classes) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-school fa-2x text-primary"></i>
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
                                    <div class="stats-label">Total Siswa</div>
                                    <div class="stats-number"><?= array_sum(array_column($homeroom_classes, 'student_count')) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-graduate fa-2x text-primary"></i>
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
                                    <div class="stats-label">Status</div>
                                    <div class="stats-number" style="font-size: 1.5rem;">
                                        <span class="badge bg-success">Aktif</span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Homeroom Classes -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-school me-2"></i>Kelas Wali
                        </div>
                        <div class="card-body">
                            <?php if (empty($homeroom_classes)): ?>
                                <p class="text-muted text-center py-3">Anda belum ditugaskan sebagai wali kelas</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($homeroom_classes as $class): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($class['class_level'] . ' ' . $class['class_name']) ?></h6>
                                                <small class="text-muted">
                                                    Tahun Ajaran: <?= htmlspecialchars($class['academic_year']) ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill"><?= $class['student_count'] ?> siswa</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="students.php" class="btn btn-outline-primary btn-sm">
                                        Kelola Siswa
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Subjects -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-book me-2"></i>Mata Pelajaran
                        </div>
                        <div class="card-body">
                            <?php if (empty($subjects)): ?>
                                <p class="text-muted text-center py-3">Belum ada mata pelajaran yang diampu</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($subjects as $subject): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($subject['subject_name']) ?></h6>
                                                <small class="text-muted">
                                                    <?= $subject['class_name'] ? htmlspecialchars($subject['class_level'] . ' ' . $subject['class_name']) : 'Semua Kelas' ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-info rounded-pill"><?= $subject['credit_hours'] ?> jam</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="grades.php" class="btn btn-outline-primary btn-sm">
                                        Input Nilai
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Attendance -->
            <?php if (!empty($recent_attendance)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-clock me-2"></i>Absensi Siswa Terbaru
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama Siswa</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($recent_attendance as $att): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($att['full_name']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($att['attendance_date'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $att['status'] == 'present' ? 'success' : ($att['status'] == 'absent' ? 'danger' : 'warning') ?>">
                                                    <?= ucfirst($att['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="attendance.php" class="btn btn-outline-primary btn-sm">
                                    Kelola Absensi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-bolt me-2"></i>Aksi Cepat
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="attendance.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-clock fa-2x mb-2"></i><br>
                                        Absensi Siswa
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="grades.php" class="btn btn-outline-success w-100">
                                        <i class="fas fa-star fa-2x mb-2"></i><br>
                                        Input Nilai
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="students.php" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-users fa-2x mb-2"></i><br>
                                        Data Siswa
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