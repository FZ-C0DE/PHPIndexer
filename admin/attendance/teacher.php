<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle form submission for attendance input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance_data'])) {
    $date = $_POST['attendance_date'];
    $notes = sanitize($_POST['notes']);
    
    try {
        $pdo->beginTransaction();
        
        foreach ($_POST['attendance_data'] as $teacher_id => $status) {
            // Check if attendance already exists
            $stmt = $pdo->prepare("
                SELECT id FROM teacher_attendance 
                WHERE teacher_id = ? AND attendance_date = ?
            ");
            $stmt->execute([$teacher_id, $date]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing attendance
                $stmt = $pdo->prepare("
                    UPDATE teacher_attendance 
                    SET status = ?, notes = ?, recorded_by = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$status, $notes, $_SESSION['user_id'], $existing['id']]);
            } else {
                // Insert new attendance
                $stmt = $pdo->prepare("
                    INSERT INTO teacher_attendance (teacher_id, attendance_date, status, notes, recorded_by) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$teacher_id, $date, $status, $notes, $_SESSION['user_id']]);
            }
        }
        
        $pdo->commit();
        $success = 'Absensi guru berhasil disimpan';
    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = 'Gagal menyimpan absensi: ' . $e->getMessage();
    }
}

// Get current date for default
$selected_date = $_GET['date'] ?? date('Y-m-d');

// Get teachers data
try {
    $stmt = $pdo->query("SELECT * FROM teachers WHERE status = 'active' ORDER BY full_name");
    $teachers = $stmt->fetchAll();
} catch(PDOException $e) {
    $teachers = [];
    $error = 'Gagal mengambil data guru';
}

// Get existing attendance for selected date
$attendance_data = [];
if (!empty($teachers)) {
    try {
        $teacher_ids = array_column($teachers, 'id');
        $placeholders = str_repeat('?,', count($teacher_ids) - 1) . '?';
        
        $stmt = $pdo->prepare("
            SELECT teacher_id, status, notes 
            FROM teacher_attendance 
            WHERE teacher_id IN ($placeholders) AND attendance_date = ?
        ");
        $stmt->execute(array_merge($teacher_ids, [$selected_date]));
        
        while ($row = $stmt->fetch()) {
            $attendance_data[$row['teacher_id']] = $row;
        }
    } catch(PDOException $e) {
        // Continue without existing data
    }
}

$page_title = 'Absensi Guru';
include '../../includes/header.php';
?>

<style>
body {
    background-color: #f8f9fa;
    font-family: 'Inter', sans-serif;
}

.main-content {
    margin-left: 250px;
    padding: 2rem;
    margin-top: 60px;
    min-height: calc(100vh - 60px);
}

.page-header {
    background: linear-gradient(135deg, #ffc107 0%, #ff6b35 100%);
    color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.attendance-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.teacher-row {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    transition: background-color 0.2s;
}

.teacher-row:hover {
    background-color: #f8f9fa;
}

.teacher-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(45deg, #ffc107, #ff6b35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
}

.status-radio {
    margin: 0 0.5rem;
}

.status-radio input[type="radio"] {
    margin-right: 0.25rem;
}

.status-present { color: #28a745; }
.status-absent { color: #dc3545; }
.status-late { color: #ffc107; }
.status-sick { color: #6c757d; }
.status-permit { color: #17a2b8; }
</style>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-clock me-2"></i>Absensi Guru
                </h2>
                <p class="mb-0 opacity-75">Kelola kehadiran guru harian</p>
            </div>
            <div class="d-flex gap-2">
                <a href="report.php" class="btn btn-light">
                    <i class="fas fa-chart-bar me-1"></i>Laporan
                </a>
                <a href="history.php" class="btn btn-light">
                    <i class="fas fa-history me-1"></i>Riwayat
                </a>
            </div>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Date Selection -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row align-items-center">
                <div class="col-md-4">
                    <label for="date" class="form-label">
                        <i class="fas fa-calendar me-1"></i>Pilih Tanggal Absensi
                    </label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="<?= htmlspecialchars($selected_date) ?>" onchange="this.form.submit()">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Tanggal: <?= date('l, d F Y', strtotime($selected_date)) ?>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <span class="badge bg-info">
                            <i class="fas fa-users me-1"></i><?= count($teachers) ?> Guru Aktif
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Form -->
    <div class="attendance-card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list-check me-2"></i>Daftar Absensi Guru
            </h5>
        </div>
        
        <form method="POST">
            <input type="hidden" name="attendance_date" value="<?= htmlspecialchars($selected_date) ?>">
            
            <?php foreach($teachers as $teacher): ?>
            <div class="teacher-row">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="teacher-avatar me-3">
                                <?= strtoupper(substr($teacher['full_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <strong><?= htmlspecialchars($teacher['full_name']) ?></strong>
                                <br><small class="text-muted">NIP: <?= htmlspecialchars($teacher['nip']) ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex flex-wrap">
                            <div class="status-radio">
                                <label class="status-present">
                                    <input type="radio" name="attendance_data[<?= $teacher['id'] ?>]" value="present" 
                                           <?= ($attendance_data[$teacher['id']]['status'] ?? '') === 'present' ? 'checked' : '' ?>>
                                    <i class="fas fa-check-circle"></i> Hadir
                                </label>
                            </div>
                            <div class="status-radio">
                                <label class="status-late">
                                    <input type="radio" name="attendance_data[<?= $teacher['id'] ?>]" value="late" 
                                           <?= ($attendance_data[$teacher['id']]['status'] ?? '') === 'late' ? 'checked' : '' ?>>
                                    <i class="fas fa-clock"></i> Terlambat
                                </label>
                            </div>
                            <div class="status-radio">
                                <label class="status-absent">
                                    <input type="radio" name="attendance_data[<?= $teacher['id'] ?>]" value="absent" 
                                           <?= ($attendance_data[$teacher['id']]['status'] ?? '') === 'absent' ? 'checked' : '' ?>>
                                    <i class="fas fa-times-circle"></i> Tidak Hadir
                                </label>
                            </div>
                            <div class="status-radio">
                                <label class="status-sick">
                                    <input type="radio" name="attendance_data[<?= $teacher['id'] ?>]" value="sick" 
                                           <?= ($attendance_data[$teacher['id']]['status'] ?? '') === 'sick' ? 'checked' : '' ?>>
                                    <i class="fas fa-thermometer"></i> Sakit
                                </label>
                            </div>
                            <div class="status-radio">
                                <label class="status-permit">
                                    <input type="radio" name="attendance_data[<?= $teacher['id'] ?>]" value="permit" 
                                           <?= ($attendance_data[$teacher['id']]['status'] ?? '') === 'permit' ? 'checked' : '' ?>>
                                    <i class="fas fa-file-alt"></i> Izin
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 text-end">
                        <?php if (isset($attendance_data[$teacher['id']])): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Tercatat
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning">
                                <i class="fas fa-exclamation me-1"></i>Belum
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-8">
                        <label for="notes" class="form-label">Catatan Tambahan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Catatan khusus untuk hari ini..."><?= htmlspecialchars($attendance_data[array_key_first($attendance_data)]['notes'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>Simpan Absensi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                    <h5>Hadir Hari Ini</h5>
                    <h3 class="text-success"><?= count(array_filter($attendance_data, fn($a) => $a['status'] === 'present')) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-user-times fa-2x text-danger mb-2"></i>
                    <h5>Tidak Hadir</h5>
                    <h3 class="text-danger"><?= count(array_filter($attendance_data, fn($a) => in_array($a['status'], ['absent', 'sick', 'permit']))) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h5>Terlambat</h5>
                    <h3 class="text-warning"><?= count(array_filter($attendance_data, fn($a) => $a['status'] === 'late')) ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Quick mark all present
function markAllPresent() {
    $('input[value="present"]').prop('checked', true);
}

// Auto-save functionality
let autoSaveTimeout;
$('input[type="radio"]').on('change', function() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(function() {
        // Optional: Add auto-save functionality here
    }, 2000);
});
</script>

<?php include '../../includes/footer.php'; ?>