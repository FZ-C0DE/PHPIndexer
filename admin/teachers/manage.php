<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Guru berhasil dihapus';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus guru: ' . $e->getMessage();
    }
}

// Get teachers data
try {
    // First check if teachers table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'teachers'");
    if ($stmt->rowCount() == 0) {
        $teachers = [];
        $error = 'Tabel teachers tidak ditemukan. Silakan setup database terlebih dahulu.';
    } else {
        // Check if there are any teachers
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM teachers");
        $teacher_count = $stmt->fetch()['count'];

        if ($teacher_count == 0) {
            $teachers = [];
            $error = 'Belum ada data guru. Silakan tambah guru terlebih dahulu.';
        } else {
            // Get teachers data with better error handling
            $stmt = $pdo->query("
                SELECT t.*, 
                       COALESCE(u.username, 'N/A') as username, 
                       COALESCE(u.email, t.email) as email, 
                       COALESCE(u.full_name, t.full_name) as user_name,
                       t.full_name as teacher_name,
                       t.nip,
                       t.phone,
                       t.status
                FROM teachers t 
                LEFT JOIN users u ON t.user_id = u.id 
                ORDER BY t.full_name
            ");
            $teachers = $stmt->fetchAll();

            if (empty($teachers)) {
                $error = 'Data guru kosong atau terjadi kesalahan dalam mengambil data.';
            }
        }
    }
} catch(PDOException $e) {
    $teachers = [];
    $error = 'Gagal mengambil data guru: ' . $e->getMessage() . '. Silakan periksa koneksi database.';
}

$page_title = 'Data Guru';
include '../includes/header.php';
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
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
    border-left: 4px solid #dc3545;
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
    background-color: #dc3545;
    color: white;
    border: none;
    font-weight: 600;
}

.btn-action {
    margin-right: 0.25rem;
    border-radius: 0.25rem;
}

.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
}
</style>

<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Data Guru
                </h2>
                <p class="mb-0 opacity-75">Kelola data guru dan informasi mengajar</p>
            </div>
            <div class="btn-group">
                <a href="add-edit.php?action=add" class="btn btn-light btn-lg">
                    <i class="fas fa-plus me-2"></i>Tambah Guru
                </a>
                <a href="../../check_database.php" class="btn btn-outline-light" target="_blank" title="Cek Database">
                    <i class="fas fa-database"></i>
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count($teachers) ?></h3>
                        <small class="text-muted">Total Guru</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-user-check fa-2x text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($teachers, fn($t) => $t['status'] === 'active')) ?></h3>
                        <small class="text-muted">Guru Aktif</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-book fa-2x text-info"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">12</h3>
                        <small class="text-muted">Mata Pelajaran</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-graduation-cap fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">9</h3>
                        <small class="text-muted">Kelas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="teachersTable">
                <thead>
                    <tr>
                        <th>ID Guru</th>
                        <th>Nama Lengkap</th>
                        <th>NIP</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($teachers) && !$error): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data guru</p>
                                <a href="add-edit.php?action=add" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Tambah Guru Pertama
                                </a>
                            </td>
                        </tr>
                    <?php elseif ($error): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <p class="text-muted"><?= htmlspecialchars($error) ?></p>
                                <div class="mt-3">
                                    <a href="check_database.php" class="btn btn-warning me-2" target="_blank">
                                        <i class="fas fa-database me-2"></i>Cek Database
                                    </a>
                                    <a href="../../setup_database.php" class="btn btn-info" target="_blank">
                                        <i class="fas fa-tools me-2"></i>Setup Database
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($teachers as $teacher): ?>
                        <tr>
                            <td>
                                <strong class="text-primary"><?= htmlspecialchars($teacher['teacher_id'] ?? 'N/A') ?></strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($teacher['teacher_name'] ?? $teacher['full_name'] ?? 'N/A') ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($teacher['subject_specialty'] ?? 'Belum ada') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($teacher['nip'] ?? 'N/A') ?></code>
                            </td>
                            <td>
                                <?php if (!empty($teacher['email'])): ?>
                                    <a href="mailto:<?= htmlspecialchars($teacher['email']) ?>" class="text-decoration-none">
                                        <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($teacher['email']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada email</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <i class="fas fa-phone me-1"></i><?= htmlspecialchars($teacher['phone'] ?? 'N/A') ?>
                            </td>
                            <td>
                                <?php if (($teacher['status'] ?? 'active') === 'active'): ?>
                                    <span class="badge bg-success status-badge">
                                        <i class="fas fa-check me-1"></i>Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary status-badge">
                                        <i class="fas fa-pause me-1"></i>Tidak Aktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="detail.php?id=<?= $teacher['id'] ?>" 
                                       class="btn btn-outline-info btn-action" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="add-edit.php?action=edit&id=<?= $teacher['id'] ?>" 
                                       class="btn btn-outline-primary btn-action" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?action=delete&id=<?= $teacher['id'] ?>" 
                                       class="btn btn-outline-danger btn-action btn-delete" 
                                       title="Hapus"
                                       onclick="return confirm('Yakin ingin menghapus guru ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>