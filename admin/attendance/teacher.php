<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $full_name = sanitize($_POST['full_name']);
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $teacher_id = sanitize($_POST['teacher_id']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $hire_date = $_POST['hire_date'];
        $status = $_POST['status'];
        
        try {
            $pdo->beginTransaction();
            
            if ($action === 'add') {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                // Insert user
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, 'teacher', ?)");
                $stmt->execute([$username, $email, $password, $full_name]);
                $user_id = $pdo->lastInsertId();
                
                // Insert teacher
                $stmt = $pdo->prepare("INSERT INTO teachers (user_id, teacher_id, phone, address, hire_date, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $teacher_id, $phone, $address, $hire_date, $status]);
                
                $success = 'Data guru berhasil ditambahkan';
            } else {
                $id = $_POST['id'];
                
                // Update user
                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, full_name = ? WHERE id = (SELECT user_id FROM teachers WHERE id = ?)");
                    $stmt->execute([$username, $email, $password, $full_name, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, full_name = ? WHERE id = (SELECT user_id FROM teachers WHERE id = ?)");
                    $stmt->execute([$username, $email, $full_name, $id]);
                }
                
                // Update teacher
                $stmt = $pdo->prepare("UPDATE teachers SET teacher_id = ?, phone = ?, address = ?, hire_date = ?, status = ? WHERE id = ?");
                $stmt->execute([$teacher_id, $phone, $address, $hire_date, $status, $id]);
                
                $success = 'Data guru berhasil diperbarui';
            }
            
            $pdo->commit();
            $action = 'list';
        } catch(PDOException $e) {
            $pdo->rollBack();
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Data guru berhasil dihapus';
        $action = 'list';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

// Get teachers list
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT t.*, u.username, u.email, u.full_name 
            FROM teachers t 
            JOIN users u ON t.user_id = u.id 
            ORDER BY t.created_at DESC
        ");
        $teachers = $stmt->fetchAll();
    } catch(PDOException $e) {
        $teachers = [];
        $error = 'Gagal mengambil data guru';
    }
}

// Get teacher data for edit
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT t.*, u.username, u.email, u.full_name 
            FROM teachers t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $teacher = $stmt->fetch();
        
        if (!$teacher) {
            $error = 'Data guru tidak ditemukan';
            $action = 'list';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data guru';
        $action = 'list';
    }
}

$page_title = 'Data Guru';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($action === 'add'): ?>
                        Tambah Guru
                    <?php elseif ($action === 'edit'): ?>
                        Edit Guru
                    <?php else: ?>
                        Data Guru
                    <?php endif; ?>
                </h1>
                
                <?php if ($action === 'list'): ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Guru
                    </a>
                </div>
                <?php else: ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="manage.php" class="btn btn-outline-secondary">
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
                <!-- Teachers List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>ID Guru</th>
                                        <th>Nama Lengkap</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Telepon</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($teachers as $teacher): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($teacher['teacher_id']) ?></td>
                                        <td><?= htmlspecialchars($teacher['full_name']) ?></td>
                                        <td><?= htmlspecialchars($teacher['username']) ?></td>
                                        <td><?= htmlspecialchars($teacher['email']) ?></td>
                                        <td><?= htmlspecialchars($teacher['phone']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $teacher['status'] == 'active' ? 'success' : 'danger' ?>">
                                                <?= ucfirst($teacher['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?action=edit&id=<?= $teacher['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?action=delete&id=<?= $teacher['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
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
                                <input type="hidden" name="id" value="<?= $teacher['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($teacher['full_name'] ?? '') ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="teacher_id" class="form-label">ID Guru *</label>
                                    <input type="text" class="form-control" id="teacher_id" name="teacher_id" 
                                           value="<?= htmlspecialchars($teacher['teacher_id'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= htmlspecialchars($teacher['username'] ?? '') ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($teacher['email'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        Password <?= $action === 'add' ? '*' : '(Kosongkan jika tidak diubah)' ?>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           <?= $action === 'add' ? 'required' : '' ?>>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Telepon</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="hire_date" class="form-label">Tanggal Masuk</label>
                                    <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                           value="<?= $teacher['hire_date'] ?? '' ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?= ($teacher['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="inactive" <?= ($teacher['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Non-aktif</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($teacher['address'] ?? '') ?></textarea>
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
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>