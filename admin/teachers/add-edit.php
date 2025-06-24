<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';
$action = $_GET['action'] ?? 'add';
$teacher_id = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $nip = sanitize($_POST['nip']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $hire_date = $_POST['hire_date'];
    $subject_specialty = sanitize($_POST['subject_specialty']);
    $status = $_POST['status'];
    
    try {
        if ($action === 'add') {
            $teacher_id_gen = 'T' . str_pad(rand(1, 9999), 3, '0', STR_PAD_LEFT);
            $stmt = $pdo->prepare("INSERT INTO teachers (teacher_id, full_name, nip, email, phone, address, hire_date, subject_specialty, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$teacher_id_gen, $full_name, $nip, $email, $phone, $address, $hire_date, $subject_specialty, $status]);
            $success = 'Guru berhasil ditambahkan';
        } else {
            $stmt = $pdo->prepare("UPDATE teachers SET full_name = ?, nip = ?, email = ?, phone = ?, address = ?, hire_date = ?, subject_specialty = ?, status = ? WHERE id = ?");
            $stmt->execute([$full_name, $nip, $email, $phone, $address, $hire_date, $subject_specialty, $status, $teacher_id]);
            $success = 'Guru berhasil diperbarui';
        }
        
        header("Location: manage.php");
        exit();
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get teacher data for edit
if ($action === 'edit' && $teacher_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
        $stmt->execute([$teacher_id]);
        $teacher = $stmt->fetch();
        
        if (!$teacher) {
            $error = 'Guru tidak ditemukan';
            $action = 'add';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data guru';
        $action = 'add';
    }
}

$page_title = ($action === 'add') ? 'Tambah Guru' : 'Edit Guru';
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
                        <label for="full_name" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($teacher['full_name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="nip" class="form-label">NIP *</label>
                        <input type="text" class="form-control" id="nip" name="nip" 
                               value="<?= htmlspecialchars($teacher['nip'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($teacher['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Telepon *</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($teacher['address'] ?? '') ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="hire_date" class="form-label">Tanggal Masuk *</label>
                        <input type="date" class="form-control" id="hire_date" name="hire_date" 
                               value="<?= $teacher['hire_date'] ?? '' ?>" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="subject_specialty" class="form-label">Bidang Keahlian</label>
                        <input type="text" class="form-control" id="subject_specialty" name="subject_specialty" 
                               value="<?= htmlspecialchars($teacher['subject_specialty'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= ($teacher['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= ($teacher['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
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