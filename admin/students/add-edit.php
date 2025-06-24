<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';
$action = $_GET['action'] ?? 'add';
$student_id = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $student_id_num = sanitize($_POST['student_id_num']);
    $class_id = $_POST['class_id'];
    $gender = $_POST['gender'];
    $place_of_birth = sanitize($_POST['place_of_birth']);
    $date_of_birth = $_POST['date_of_birth'];
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $parent_name = sanitize($_POST['parent_name']);
    $parent_phone = sanitize($_POST['parent_phone']);
    $status = $_POST['status'];
    
    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, class_id, gender, place_of_birth, date_of_birth, phone, address, parent_name, parent_phone, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$student_id_num, $full_name, $class_id, $gender, $place_of_birth, $date_of_birth, $phone, $address, $parent_name, $parent_phone, $status]);
            $success = 'Siswa berhasil ditambahkan';
        } else {
            $stmt = $pdo->prepare("UPDATE students SET student_id = ?, full_name = ?, class_id = ?, gender = ?, place_of_birth = ?, date_of_birth = ?, phone = ?, address = ?, parent_name = ?, parent_phone = ?, status = ? WHERE id = ?");
            $stmt->execute([$student_id_num, $full_name, $class_id, $gender, $place_of_birth, $date_of_birth, $phone, $address, $parent_name, $parent_phone, $status, $student_id]);
            $success = 'Siswa berhasil diperbarui';
        }
        
        header("Location: manage.php");
        exit();
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get student data for edit
if ($action === 'edit' && $student_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();
        
        if (!$student) {
            $error = 'Siswa tidak ditemukan';
            $action = 'add';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data siswa';
        $action = 'add';
    }
}

// Get classes for dropdown
try {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY class_level, class_name");
    $classes = $stmt->fetchAll();
} catch(PDOException $e) {
    $classes = [];
}

$page_title = ($action === 'add') ? 'Tambah Siswa' : 'Edit Siswa';
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
                    <div class="col-md-6 mb-3">
                        <label for="student_id_num" class="form-label">NISN *</label>
                        <input type="text" class="form-control" id="student_id_num" name="student_id_num" 
                               value="<?= htmlspecialchars($student['student_id'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($student['full_name'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="class_id" class="form-label">Kelas *</label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">Pilih Kelas</option>
                            <?php foreach($classes as $class): ?>
                                <option value="<?= $class['id'] ?>" <?= ($student['class_id'] ?? '') == $class['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($class['class_level'] . ' ' . $class['class_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Jenis Kelamin *</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Pilih</option>
                            <option value="male" <?= ($student['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="female" <?= ($student['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= ($student['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= ($student['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                            <option value="graduated" <?= ($student['status'] ?? '') == 'graduated' ? 'selected' : '' ?>>Lulus</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="place_of_birth" class="form-label">Tempat Lahir *</label>
                        <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" 
                               value="<?= htmlspecialchars($student['place_of_birth'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="date_of_birth" class="form-label">Tanggal Lahir *</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                               value="<?= $student['date_of_birth'] ?? '' ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Telepon Siswa</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="parent_phone" class="form-label">Telepon Orang Tua *</label>
                        <input type="text" class="form-control" id="parent_phone" name="parent_phone" 
                               value="<?= htmlspecialchars($student['parent_phone'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="parent_name" class="form-label">Nama Orang Tua *</label>
                    <input type="text" class="form-control" id="parent_name" name="parent_name" 
                           value="<?= htmlspecialchars($student['parent_name'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat *</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($student['address'] ?? '') ?></textarea>
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