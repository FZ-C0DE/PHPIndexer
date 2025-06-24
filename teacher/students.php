<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

checkLogin();
checkRole('teacher');

$error = '';
$success = '';

// Get teacher information
try {
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $teacher = $stmt->fetch();
    
    if (!$teacher) {
        $error = 'Data guru tidak ditemukan';
        header("Location: dashboard.php");
        exit();
    }
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $teacher = null;
}

// Get students from homeroom classes only
try {
    $stmt = $pdo->prepare("
        SELECT s.*, c.class_name, c.class_level 
        FROM students s 
        JOIN classes c ON s.class_id = c.id 
        WHERE c.teacher_id = ? AND s.status = 'active'
        ORDER BY c.class_level, c.class_name, s.full_name
    ");
    $stmt->execute([$teacher['id']]);
    $students = $stmt->fetchAll();
} catch(PDOException $e) {
    $students = [];
    $error = 'Gagal mengambil data siswa';
}

// Get classes where teacher is homeroom teacher
try {
    $stmt = $pdo->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM students WHERE class_id = c.id AND status = 'active') as student_count
        FROM classes c 
        WHERE c.teacher_id = ?
        ORDER BY c.class_level, c.class_name
    ");
    $stmt->execute([$teacher['id']]);
    $homeroom_classes = $stmt->fetchAll();
} catch(PDOException $e) {
    $homeroom_classes = [];
}

$page_title = 'Data Siswa';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Data Siswa</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-info fs-6">
                        <i class="fas fa-info-circle me-1"></i>Siswa dari kelas wali Anda
                    </span>
                </div>
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

            <?php if (empty($homeroom_classes)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <h5>Anda belum ditugaskan sebagai wali kelas</h5>
                        <p class="text-muted">Hubungi admin untuk mendapatkan penugasan sebagai wali kelas</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Homeroom Classes Summary -->
                <div class="row mb-4">
                    <?php foreach($homeroom_classes as $class): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="stats-label"><?= htmlspecialchars($class['class_level'] . ' ' . $class['class_name']) ?></div>
                                        <div class="stats-number"><?= $class['student_count'] ?> <small>siswa</small></div>
                                        <small class="text-muted">Tahun: <?= htmlspecialchars($class['academic_year']) ?></small>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Students List -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-graduate me-2"></i>Daftar Siswa
                    </div>
                    <div class="card-body">
                        <?php if (empty($students)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                <h5>Belum ada siswa</h5>
                                <p class="text-muted">Siswa akan muncul setelah admin menambahkan siswa ke kelas Anda</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>ID Siswa</th>
                                            <th>Nama Lengkap</th>
                                            <th>Kelas</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Telepon</th>
                                            <th>Orang Tua</th>
                                            <th>Telepon Ortu</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($students as $student): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['student_id']) ?></td>
                                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                                            <td><?= htmlspecialchars($student['class_level'] . ' ' . $student['class_name']) ?></td>
                                            <td><?= $student['gender'] == 'male' ? 'Laki-laki' : 'Perempuan' ?></td>
                                            <td><?= htmlspecialchars($student['phone'] ?: '-') ?></td>
                                            <td><?= htmlspecialchars($student['parent_name'] ?: '-') ?></td>
                                            <td><?= htmlspecialchars($student['parent_phone'] ?: '-') ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#studentModal<?= $student['id'] ?>">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Student Detail Modal -->
                                        <div class="modal fade" id="studentModal<?= $student['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detail Siswa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <table class="table table-borderless">
                                                                    <tr>
                                                                        <td><strong>ID Siswa:</strong></td>
                                                                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Nama Lengkap:</strong></td>
                                                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Kelas:</strong></td>
                                                                        <td><?= htmlspecialchars($student['class_level'] . ' ' . $student['class_name']) ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Jenis Kelamin:</strong></td>
                                                                        <td><?= $student['gender'] == 'male' ? 'Laki-laki' : 'Perempuan' ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Tanggal Lahir:</strong></td>
                                                                        <td><?= $student['birth_date'] ? date('d/m/Y', strtotime($student['birth_date'])) : '-' ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Telepon:</strong></td>
                                                                        <td><?= htmlspecialchars($student['phone'] ?: '-') ?></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <table class="table table-borderless">
                                                                    <tr>
                                                                        <td><strong>Nama Orang Tua:</strong></td>
                                                                        <td><?= htmlspecialchars($student['parent_name'] ?: '-') ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Telepon Orang Tua:</strong></td>
                                                                        <td><?= htmlspecialchars($student['parent_phone'] ?: '-') ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Tanggal Masuk:</strong></td>
                                                                        <td><?= date('d/m/Y', strtotime($student['enrollment_date'])) ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Status:</strong></td>
                                                                        <td>
                                                                            <span class="badge bg-success">
                                                                                <?= ucfirst($student['status']) ?>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Alamat:</strong></td>
                                                                        <td><?= nl2br(htmlspecialchars($student['address'] ?: '-')) ?></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>