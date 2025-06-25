
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/auth-check.php';
require_once '../config/database.php';

// Check if constants are defined
if (!defined('SITE_NAME')) {
    require_once '../config/constants.php';
}

requireRole('admin');

// Check database connection
if (!isset($pdo)) {
    die('Database connection not available. Please check database configuration.');
}

// Get statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM teachers WHERE status = 'active'");
    $total_teachers = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM students WHERE status = 'active'");
    $total_students = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM classes");
    $total_classes = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM subjects");
    $total_subjects = $stmt->fetch()['total'];
    
    // Recent activities
    $stmt = $pdo->query("
        SELECT 'student' as type, full_name as name, created_at 
        FROM students 
        WHERE status = 'active' 
        ORDER BY created_at DESC 
        LIMIT 3
    ");
    $recent_students = $stmt->fetchAll();
    
    $stmt = $pdo->query("
        SELECT 'teacher' as type, u.full_name as name, t.created_at 
        FROM teachers t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.status = 'active' 
        ORDER BY t.created_at DESC 
        LIMIT 3
    ");
    $recent_teachers = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $total_teachers = $total_students = $total_classes = $total_subjects = 0;
    $recent_students = $recent_teachers = [];
}

$page_title = 'Dashboard Admin';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-download me-1"></i>Export
                </button>
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
                            <div class="stats-label">Total Guru</div>
                            <div class="stats-number"><?= $total_teachers ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
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
                            <div class="stats-number"><?= $total_students ?></div>
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
                            <div class="stats-label">Total Kelas</div>
                            <div class="stats-number"><?= $total_classes ?></div>
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
                            <div class="stats-number"><?= $total_subjects ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Students -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-graduate me-2"></i>Siswa Terbaru
                </div>
                <div class="card-body">
                    <?php if (empty($recent_students)): ?>
                        <p class="text-muted text-center py-3">Belum ada data siswa</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($recent_students as $student): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($student['name']) ?></h6>
                                        <small class="text-muted">
                                            Terdaftar: <?= date('d/m/Y H:i', strtotime($student['created_at'])) ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">Baru</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="students/manage.php" class="btn btn-outline-primary btn-sm">
                                Lihat Semua Siswa
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Teachers -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Guru Terbaru
                </div>
                <div class="card-body">
                    <?php if (empty($recent_teachers)): ?>
                        <p class="text-muted text-center py-3">Belum ada data guru</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($recent_teachers as $teacher): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($teacher['name']) ?></h6>
                                        <small class="text-muted">
                                            Terdaftar: <?= date('d/m/Y H:i', strtotime($teacher['created_at'])) ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-success rounded-pill">Aktif</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="teachers/manage.php" class="btn btn-outline-primary btn-sm">
                                Lihat Semua Guru
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
                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="teachers/add-edit.php?action=add" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                Tambah Guru
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="students/add-edit.php?action=add" class="btn btn-outline-success w-100">
                                <i class="fas fa-user-graduate fa-2x mb-2"></i><br>
                                Tambah Siswa
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="classes/add-edit.php?action=add" class="btn btn-outline-warning w-100">
                                <i class="fas fa-plus-square fa-2x mb-2"></i><br>
                                Tambah Kelas
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="subjects/add-edit.php?action=add" class="btn btn-outline-info w-100">
                                <i class="fas fa-book-open fa-2x mb-2"></i><br>
                                Tambah Mata Pelajaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
