<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';
$action = $_GET['action'] ?? 'add';
$user_id = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $full_name = sanitize($_POST['full_name']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    
    try {
        if ($action === 'add') {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password, $full_name, $role, $status]);
            $success = 'User berhasil ditambahkan';
        } else {
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, full_name = ?, role = ?, status = ? WHERE id = ?");
                $stmt->execute([$username, $email, $password, $full_name, $role, $status, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, role = ?, status = ? WHERE id = ?");
                $stmt->execute([$username, $email, $full_name, $role, $status, $user_id]);
            }
            $success = 'User berhasil diperbarui';
        }
        
        header("Location: manage.php");
        exit();
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get user data for edit
if ($action === 'edit' && $user_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $error = 'User tidak ditemukan';
            $action = 'add';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data user';
        $action = 'add';
    }
}

$page_title = ($action === 'add') ? 'Tambah User' : 'Edit User';
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
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap *</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" 
                           value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password <?= $action === 'add' ? '*' : '(kosongkan jika tidak diubah)' ?></label>
                        <input type="password" class="form-control" id="password" name="password" 
                               <?= $action === 'add' ? 'required' : '' ?>>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="role" class="form-label">Role *</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin" <?= ($user['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="teacher" <?= ($user['role'] ?? '') == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= ($user['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= ($user['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
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