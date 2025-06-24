<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';
$action = $_GET['action'] ?? 'add';
$class_id = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = sanitize($_POST['class_name']);
    $class_level = $_POST['class_level'];
    $capacity = (int)$_POST['capacity'];
    $room_number = sanitize($_POST['room_number']);
    $homeroom_teacher_id = $_POST['homeroom_teacher_id'] ?: null;
    $academic_year = sanitize($_POST['academic_year']);
    
    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO classes (class_name, class_level, capacity, room_number, homeroom_teacher_id, academic_year) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$class_name, $class_level, $capacity, $room_number, $homeroom_teacher_id, $academic_year]);
            $success = 'Kelas berhasil ditambahkan';
        } else {
            $stmt = $pdo->prepare("UPDATE classes SET class_name = ?, class_level = ?, capacity = ?, room_number = ?, homeroom_teacher_id = ?, academic_year = ? WHERE id = ?");
            $stmt->execute([$class_name, $class_level, $capacity, $room_number, $homeroom_teacher_id, $academic_year, $class_id]);
            $success = 'Kelas berhasil diperbarui';
        }
        
        header("Location: manage.php");
        exit();
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get class data for edit
if ($action === 'edit' && $class_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$class_id]);
        $class = $stmt->fetch();
        
        if (!$class) {
            $error = 'Kelas tidak ditemukan';
            $action = 'add';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data kelas';
        $action = 'add';
    }
}

// Get teachers for dropdown
try {
    $stmt = $pdo->query("SELECT * FROM teachers WHERE status = 'active' ORDER BY full_name");
    $teachers = $stmt->fetchAll();
} catch(PDOException $e) {
    $teachers = [];
}

$page_title = ($action === 'add') ? 'Tambah Kelas' : 'Edit Kelas';
$custom_css = '../../assets/css/admin/dashboard.css';
include '../../includes/header.php';
?>

<style>
.main-content {
    margin-left: 250px;
    padding: 2rem;
    margin-top: 60px;
    min-height: calc(100vh - 60px);
}
</style>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= $page_title ?></h2>
        <a href="manage.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="class_level" class="form-label">Tingkat Kelas *</label>
                        <select class="form-select" id="class_level" name="class_level" required>
                            <option value="">Pilih Tingkat</option>
                            <option value="X" <?= ($class['class_level'] ?? '') == 'X' ? 'selected' : '' ?>>X</option>
                            <option value="XI" <?= ($class['class_level'] ?? '') == 'XI' ? 'selected' : '' ?>>XI</option>
                            <option value="XII" <?= ($class['class_level'] ?? '') == 'XII' ? 'selected' : '' ?>>XII</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="class_name" class="form-label">Nama Kelas *</label>
                        <input type="text" class="form-control" id="class_name" name="class_name" 
                               value="<?= htmlspecialchars($class['class_name'] ?? '') ?>" required>
                        <div class="form-text">Contoh: IPA 1, IPS 2, dll</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="capacity" class="form-label">Kapasitas *</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" 
                               value="<?= $class['capacity'] ?? '30' ?>" min="1" max="50" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="room_number" class="form-label">Nomor Ruang</label>
                        <input type="text" class="form-control" id="room_number" name="room_number" 
                               value="<?= htmlspecialchars($class['room_number'] ?? '') ?>">
                        <div class="form-text">Contoh: R101, Lab IPA, dll</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="academic_year" class="form-label">Tahun Ajaran *</label>
                        <input type="text" class="form-control" id="academic_year" name="academic_year" 
                               value="<?= htmlspecialchars($class['academic_year'] ?? '2024/2025') ?>" required>
                        <div class="form-text">Format: 2024/2025</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="homeroom_teacher_id" class="form-label">Wali Kelas</label>
                    <select class="form-select" id="homeroom_teacher_id" name="homeroom_teacher_id">
                        <option value="">Pilih Wali Kelas</option>
                        <?php foreach($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>" <?= ($class['homeroom_teacher_id'] ?? '') == $teacher['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($teacher['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="manage.php" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>