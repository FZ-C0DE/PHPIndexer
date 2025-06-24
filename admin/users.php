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
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $full_name = sanitize($_POST['full_name']);
        $role = $_POST['role'];
        
        try {
            if ($action === 'add') {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $password, $role, $full_name]);
                $success = 'User berhasil ditambahkan';
            } else {
                $id = $_POST['id'];
                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ?, full_name = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $password, $role, $full_name, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ?, full_name = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $role, $full_name, $id]);
                }
                $success = 'User berhasil diperbarui';
            }
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($id == $_SESSION['user_id']) {
        $error = 'Tidak dapat menghapus akun sendiri';
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'User berhasil dihapus';
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Gagal menghapus user: ' . $e->getMessage();
        }
    }
}

// Get users list
if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();
    } catch(PDOException $e) {
        $users = [];
        $error = 'Gagal mengambil data user';
    }
}

// Get user data for edit
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $error = 'User tidak ditemukan';
            $action = 'list';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data user';
        $action = 'list';
    }
}

$page_title = 'Manajemen User';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($action === 'add'): ?>
                        Tambah User
                    <?php elseif ($action === 'edit'): ?>
                        Edit User
                    <?php else: ?>
                        Manajemen User
                    <?php endif; ?>
                </h1>
                
                <?php if ($action === 'list'): ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah User
                    </a>
                </div>
                <?php else: ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="users.php" class="btn btn-outline-secondary">
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
                <!-- Users List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Nama Lengkap</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $usr): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($usr['username']) ?></td>
                                        <td><?= htmlspecialchars($usr['full_name']) ?></td>
                                        <td><?= htmlspecialchars($usr['email']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $usr['role'] == 'admin' ? 'danger' : 'primary' ?>">
                                                <?= ucfirst($usr['role']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($usr['created_at'])) ?></td>
                                        <td>
                                            <a href="?action=edit&id=<?= $usr['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($usr['id'] != $_SESSION['user_id']): ?>
                                                <a href="?action=delete&id=<?= $usr['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
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
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <?php endif; ?>
                            
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
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        Password <?= $action === 'add' ? '*' : '(Kosongkan jika tidak diubah)' ?>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           <?= $action === 'add' ? 'required' : '' ?>>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Role *</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="admin" <?= ($user['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="teacher" <?= ($user['role'] ?? '') == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="users.php" class="btn btn-outline-secondary">Batal</a>
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