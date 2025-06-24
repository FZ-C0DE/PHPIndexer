<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

checkLogin();
checkRole('teacher');

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';

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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $student_id = $_POST['student_id'];
        $subject_id = $_POST['subject_id'];
        $attendance_date = $_POST['attendance_date'];
        $status = $_POST['status'];
        $notes = sanitize($_POST['notes']);
        
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO attendances_student (student_id, subject_id, attendance_date, status, notes, recorded_by) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$student_id, $subject_id, $attendance_date, $status, $notes, $_SESSION['user_id']]);
                $success = 'Data absensi siswa berhasil ditambahkan';
            } else {
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE attendances_student SET student_id = ?, subject_id = ?, attendance_date = ?, status = ?, notes = ? WHERE id = ?");
                $stmt->execute([$student_id, $subject_id, $attendance_date, $status, $notes, $id]);
                $success = 'Data absensi siswa berhasil diperbarui';
            }
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM attendances_student WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Data absensi berhasil dihapus';
        $action = 'list';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

// Get attendance list for teacher's subjects and homeroom classes
if ($action === 'list') {
    try {
        $stmt = $pdo->prepare("
            SELECT asa.*, s.full_name as student_name, s.student_id as student_number,
                   subj.subject_name, c.class_name, c.class_level
            FROM attendances_student asa
            JOIN students s ON asa.student_id = s.id
            JOIN subjects subj ON asa.subject_id = subj.id
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE (subj.teacher_id = ? OR c.teacher_id = ?)
            ORDER BY asa.attendance_date DESC, s.full_name
        ");
        $stmt->execute([$teacher['id'], $teacher['id']]);
        $attendances = $stmt->fetchAll();
    } catch(PDOException $e) {
        $attendances = [];
        $error = 'Gagal mengambil data absensi';
    }
}

// Get subjects taught by teacher
try {
    $stmt = $pdo->prepare("
        SELECT s.*, c.class_name, c.class_level
        FROM subjects s 
        LEFT JOIN classes c ON s.class_id = c.id 
        WHERE s.teacher_id = ?
        ORDER BY s.subject_name
    ");
    $stmt->execute([$teacher['id']]);
    $subjects = $stmt->fetchAll();
} catch(PDOException $e) {
    $subjects = [];
}

// Get students from teacher's subjects and homeroom classes
try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT st.*, c.class_name, c.class_level
        FROM students st
        JOIN classes c ON st.class_id = c.id
        WHERE (c.teacher_id = ? OR st.class_id IN (
            SELECT DISTINCT s.class_id FROM subjects s WHERE s.teacher_id = ?
        )) AND st.status = 'active'
        ORDER BY c.class_level, c.class_name, st.full_name
    ");
    $stmt->execute([$teacher['id'], $teacher['id']]);
    $students = $stmt->fetchAll();
} catch(PDOException $e) {
    $students = [];
}

// Get attendance data for edit
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM attendances_student WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $attendance = $stmt->fetch();
        
        if (!$attendance) {
            $error = 'Data absensi tidak ditemukan';
            $action = 'list';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data absensi';
        $action = 'list';
    }
}

$page_title = 'Absensi Siswa';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($action === 'add'): ?>
                        Tambah Absensi Siswa
                    <?php elseif ($action === 'edit'): ?>
                        Edit Absensi Siswa
                    <?php else: ?>
                        Absensi Siswa
                    <?php endif; ?>
                </h1>
                
                <?php if ($action === 'list'): ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Absensi
                    </a>
                </div>
                <?php else: ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="attendance.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <?php endif; ?>
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

            <?php if ($action === 'list'): ?>
                <!-- Attendance List -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clock me-2"></i>Data Absensi Siswa
                    </div>
                    <div class="card-body">
                        <?php if (empty($attendances)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                <h5>Belum ada data absensi</h5>
                                <p class="text-muted">Mulai dengan menambahkan absensi siswa</p>
                                <a href="?action=add" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Tambah Absensi
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama Siswa</th>
                                            <th>ID Siswa</th>
                                            <th>Kelas</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($attendances as $att): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($att['attendance_date'])) ?></td>
                                            <td><?= htmlspecialchars($att['student_name']) ?></td>
                                            <td><?= htmlspecialchars($att['student_number']) ?></td>
                                            <td><?= htmlspecialchars($att['class_level'] . ' ' . $att['class_name']) ?></td>
                                            <td><?= htmlspecialchars($att['subject_name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $att['status'] == 'present' ? 'success' : ($att['status'] == 'absent' ? 'danger' : ($att['status'] == 'late' ? 'warning' : 'info')) ?>">
                                                    <?php
                                                    $status_map = [
                                                        'present' => 'Hadir',
                                                        'absent' => 'Tidak Hadir',
                                                        'late' => 'Terlambat',
                                                        'sick_leave' => 'Sakit',
                                                        'permission' => 'Izin'
                                                    ];
                                                    echo $status_map[$att['status']] ?? $att['status'];
                                                    ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($att['notes'] ?: '-') ?></td>
                                            <td>
                                                <a href="?action=edit&id=<?= $att['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?action=delete&id=<?= $att['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            
            <?php else: ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <?php if ($action === 'edit'): ?>
                                <input type="hidden" name="id" value="<?= $attendance['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_id" class="form-label">Siswa *</label>
                                    <select class="form-select select2" id="student_id" name="student_id" required>
                                        <option value="">Pilih Siswa</option>
                                        <?php foreach($students as $student): ?>
                                            <option value="<?= $student['id'] ?>" 
                                                    <?= ($attendance['student_id'] ?? '') == $student['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($student['full_name'] . ' (' . $student['student_id'] . ') - ' . $student['class_level'] . ' ' . $student['class_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="subject_id" class="form-label">Mata Pelajaran *</label>
                                    <select class="form-select select2" id="subject_id" name="subject_id" required>
                                        <option value="">Pilih Mata Pelajaran</option>
                                        <?php foreach($subjects as $subject): ?>
                                            <option value="<?= $subject['id'] ?>" 
                                                    <?= ($attendance['subject_id'] ?? '') == $subject['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($subject['subject_name'] . ' - ' . ($subject['class_name'] ? $subject['class_level'] . ' ' . $subject['class_name'] : 'Semua Kelas')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="attendance_date" class="form-label">Tanggal *</label>
                                    <input type="date" class="form-control" id="attendance_date" name="attendance_date" 
                                           value="<?= $attendance['attendance_date'] ?? date('Y-m-d') ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status Kehadiran *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="present" <?= ($attendance['status'] ?? '') == 'present' ? 'selected' : '' ?>>Hadir</option>
                                        <option value="absent" <?= ($attendance['status'] ?? '') == 'absent' ? 'selected' : '' ?>>Tidak Hadir</option>
                                        <option value="late" <?= ($attendance['status'] ?? '') == 'late' ? 'selected' : '' ?>>Terlambat</option>
                                        <option value="sick_leave" <?= ($attendance['status'] ?? '') == 'sick_leave' ? 'selected' : '' ?>>Sakit</option>
                                        <option value="permission" <?= ($attendance['status'] ?? '') == 'permission' ? 'selected' : '' ?>>Izin</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($attendance['notes'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="attendance.php" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>