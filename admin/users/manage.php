<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'User berhasil dihapus';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus user: ' . $e->getMessage();
    }
}

// Get users data
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    $users = [];
    $error = 'Gagal mengambil data users';
}

$page_title = 'Manajemen User';
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
    background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
    color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.stats-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #6f42c1;
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.data-table {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table th {
    background-color: #6f42c1;
    color: white;
    border: none;
    font-weight: 600;
}

.btn-action {
    margin-right: 0.25rem;
    border-radius: 0.25rem;
}

.role-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #6f42c1, #5a32a3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}
</style>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-users-cog me-2"></i>Manajemen User
                </h2>
                <p class="mb-0 opacity-75">Kelola akun pengguna sistem</p>
            </div>
            <a href="add-edit.php?action=add" class="btn btn-light btn-lg">
                <i class="fas fa-user-plus me-2"></i>Tambah User
            </a>
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count($users) ?></h3>
                        <small class="text-muted">Total User</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-crown fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?></h3>
                        <small class="text-muted">Admin</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-chalkboard-teacher fa-2x text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($users, fn($u) => $u['role'] === 'teacher')) ?></h3>
                        <small class="text-muted">Teacher</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-clock fa-2x text-info"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($users, fn($u) => strtotime($u['last_login']) > strtotime('-7 days'))) ?></h3>
                        <small class="text-muted">Aktif 7 Hari</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pengguna</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Terakhir Login</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td>
                            <strong class="text-primary"><?= $user['id'] ?></strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars($user['full_name']) ?></strong>
                                    <br><small class="text-muted">Dibuat: <?= date('d/m/Y', strtotime($user['created_at'])) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($user['username']) ?></code>
                        </td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="text-decoration-none">
                                <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($user['email']) ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-warning role-badge">
                                    <i class="fas fa-crown me-1"></i>Admin
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success role-badge">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>Teacher
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['last_login']): ?>
                                <small>
                                    <i class="fas fa-clock me-1"></i>
                                    <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                </small>
                            <?php else: ?>
                                <small class="text-muted">Belum pernah login</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="detail.php?id=<?= $user['id'] ?>" 
                                   class="btn btn-outline-info btn-action" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add-edit.php?action=edit&id=<?= $user['id'] ?>" 
                                   class="btn btn-outline-primary btn-action" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="?action=delete&id=<?= $user['id'] ?>" 
                                   class="btn btn-outline-danger btn-action btn-delete" 
                                   title="Hapus"
                                   onclick="return confirm('Yakin ingin menghapus user ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        },
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [6] }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel me-1"></i>Export Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf me-1"></i>Export PDF',
                className: 'btn btn-danger btn-sm'
            }
        ]
    });
});
</script>

<?php include '../../includes/footer.php'; ?>