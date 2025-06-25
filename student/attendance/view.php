
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
    
    // Get attendance records
    $stmt = $pdo->prepare("
        SELECT a.*, s.subject_name, t.full_name as teacher_name, u.full_name as teacher_full_name
        FROM attendances_student a
        LEFT JOIN subjects s ON a.subject_id = s.id
        LEFT JOIN teachers t ON s.teacher_id = t.id
        LEFT JOIN users u ON t.user_id = u.id
        WHERE a.student_id = ?
        ORDER BY a.attendance_date DESC, a.created_at DESC
    ");
    $stmt->execute([$student_info['id']]);
    $attendances = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $attendances = [];
}

$page_title = 'Riwayat Absensi';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Riwayat Absensi</h1>
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

            <!-- Attendance Table -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-check me-2"></i>Daftar Absensi
                </div>
                <div class="card-body">
                    <?php if (empty($attendances)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Data Absensi</h5>
                            <p class="text-muted">Data absensi akan muncul setelah guru melakukan pencatatan</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($attendances as $attendance): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($attendance['attendance_date'])) ?></td>
                                        <td><?= htmlspecialchars($attendance['subject_name'] ?? 'Umum') ?></td>
                                        <td><?= htmlspecialchars($attendance['teacher_full_name'] ?? 'Belum ditentukan') ?></td>
                                        <td>
                                            <?php
                                            $status_class = 'secondary';
                                            if ($attendance['status'] === 'present') $status_class = 'success';
                                            elseif ($attendance['status'] === 'absent') $status_class = 'danger';
                                            elseif ($attendance['status'] === 'late') $status_class = 'warning';
                                            ?>
                                            <span class="badge bg-<?= $status_class ?>">
                                                <?= ucfirst($attendance['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($attendance['notes'] ?? '-') ?></td>
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
