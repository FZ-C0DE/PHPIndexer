<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';
$action = $_GET['action'] ?? 'add';
$subject_id = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = sanitize($_POST['subject_code']);
    $subject_name = sanitize($_POST['subject_name']);
    $description = sanitize($_POST['description']);
    $credit_hours = (int)$_POST['credit_hours'];
    $semester = $_POST['semester'];
    $class_id = $_POST['class_id'];
    $teacher_id = $_POST['teacher_id'] ?: null;
    
    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_code, subject_name, description, credit_hours, semester, class_id, teacher_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$subject_code, $subject_name, $description, $credit_hours, $semester, $class_id, $teacher_id]);
            $success = 'Mata pelajaran berhasil ditambahkan';
        } else {
            $stmt = $pdo->prepare("UPDATE subjects SET subject_code = ?, subject_name = ?, description = ?, credit_hours = ?, semester = ?, class_id = ?, teacher_id = ? WHERE id = ?");
            $stmt->execute([$subject_code, $subject_name, $description, $credit_hours, $semester, $class_id, $teacher_id, $subject_id]);
            $success = 'Mata pelajaran berhasil diperbarui';
        }
        
        header("Location: manage.php");
        exit();
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get subject data for edit
if ($action === 'edit' && $subject_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
        $stmt->execute([$subject_id]);
        $subject = $stmt->fetch();
        
        if (!$subject) {
            $error = 'Mata pelajaran tidak ditemukan';
            $action = 'add';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data mata pelajaran';
        $action = 'add';
    }
}

// Get classes and teachers for dropdown
try {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY class_level, class_name");
    $classes = $stmt->fetchAll();
    
    $stmt = $pdo->query("SELECT * FROM teachers WHERE status = 'active' ORDER BY full_name");
    $teachers = $stmt->fetchAll();
} catch(PDOException $e) {
    $classes = [];
    $teachers = [];
}

$page_title = ($action === 'add') ? 'Tambah Mata Pelajaran' : 'Edit Mata Pelajaran';
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
                        <label for="subject_code" class="form-label">Kode Mata Pelajaran *</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code" 
                               value="<?= htmlspecialchars($subject['subject_code'] ?? '') ?>" required>
                        <div class="form-text">Contoh: MAT001, IPA002, dll</div>
                    </div>
                    
                    <div class="col-md-8 mb-3">
                        <label for="subject_name" class="form-label">Nama Mata Pelajaran *</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" 
                               value="<?= htmlspecialchars($subject['subject_name'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($subject['description'] ?? '') ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="credit_hours" class="form-label">Jam Pelajaran *</label>
                        <input type="number" class="form-control" id="credit_hours" name="credit_hours" 
                               value="<?= $subject['credit_hours'] ?? '2' ?>" min="1" max="10" required>
                        <div class="form-text">Jam per minggu</div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="semester" class="form-label">Semester *</label>
                        <select class="form-select" id="semester" name="semester" required>
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil" <?= ($subject['semester'] ?? '') == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                            <option value="Genap" <?= ($subject['semester'] ?? '') == 'Genap' ? 'selected' : '' ?>>Genap</option>
                            <option value="Keduanya" <?= ($subject['semester'] ?? '') == 'Keduanya' ? 'selected' : '' ?>>Keduanya</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="class_id" class="form-label">Kelas *</label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">Pilih Kelas</option>
                            <?php foreach($classes as $class): ?>
                                <option value="<?= $class['id'] ?>" <?= ($subject['class_id'] ?? '') == $class['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($class['class_level'] . ' ' . $class['class_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">Guru Pengampu</label>
                    <select class="form-select" id="teacher_id" name="teacher_id">
                        <option value="">Pilih Guru</option>
                        <?php foreach($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>" <?= ($subject['teacher_id'] ?? '') == $teacher['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($teacher['full_name'] . ' - ' . $teacher['subject_specialty']) ?>
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