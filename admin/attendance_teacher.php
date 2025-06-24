<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

checkLogin();
checkRole('admin');

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $teacher_id = $_POST['teacher_id'];
        $attendance_date = $_POST['attendance_date'];
        $status = $_POST['status'];
        $notes = sanitize($_POST['notes']);
        
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO attendances_teacher (teacher_id, attendance_date, status, notes, recorded_by) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$teacher_id, $attendance_date, $status, $notes, $_SESSION['user_id']]);
                $success = 'Data absensi guru berhasil ditambahkan';
            } else {
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE attendances_teacher SET teacher_id = ?, attendance_date = ?, status = ?, notes = ? WHERE id = ?");
                $stmt->execute([$teacher_id, $attendance_date, $status, $notes, $id]);
                $success = 'Data absensi guru berhasil diperbarui';
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
        $stmt = $pdo->prepare("DELETE FROM attendances_teacher WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Data absensi berhasil dihapus';
        $action = 'list';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

// Get attendance list
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT at.*, u.full_name as teacher_name, t.teacher_id 
            FROM attendances_teacher at 
            JOIN teachers t ON at.teacher_id = t.id 
            JOIN users u ON t.user_id = u.id 
            ORDER BY at.attendance_date DESC, u.full_name
        ");
        $attendances = $stmt->fetchAll();
    } catch(PDOException $e) {
        $attendances = [];
        $error = 'Gagal mengambil data absensi';
    }
}

// Get teachers for dropdown
try {
    $stmt = $pdo->query("SELECT t.id, u.full_name, t.teacher_id FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.status = 'active' ORDER BY u.full_name");
    $teachers = $stmt->fetchAll();
} catch(PDOException $e) {
    $teachers = [];
}

// Get attendance data for edit
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM attendances_teacher WHERE id = ?");
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

$page_title = 'Absensi Guru';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($action === 'add'): ?>
                        Tambah Absensi Guru
                    <?php elseif ($action === 'edit'): ?>
                        Edit Absensi Guru
                    <?php else: ?>
                        Absensi Guru
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
                    <a href="attendance_teacher.php" class="btn btn-outline-secondary">
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
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama Guru</th>
                                        <th>ID Guru</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($attendances as $att): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($att['attendance_date'])) ?></td>
                                        <td><?= htmlspecialchars($att['teacher_name']) ?></td>
                                        <td><?= htmlspecialchars($att['teacher_id']) ?></td>
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
                                    <label for="teacher_id" class="form-label">Guru *</label>
                                    <select class="form-select select2" id="teacher_id" name="teacher_id" required>
                                        <option value="">Pilih Guru</option>
                                        <?php foreach($teachers as $teacher): ?>
                                            <option value="<?= $teacher['id'] ?>" 
                                                    <?= ($attendance['teacher_id'] ?? '') == $teacher['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($teacher['full_name'] . ' (' . $teacher['teacher_id'] . ')') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="attendance_date" class="form-label">Tanggal *</label>
                                    <input type="date" class="form-control" id="attendance_date" name="attendance_date" 
                                           value="<?= $attendance['attendance_date'] ?? date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
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
                                <a href="attendance_teacher.php" class="btn btn-outline-secondary">Batal</a>
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