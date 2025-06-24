<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Kelas berhasil dihapus';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus kelas: ' . $e->getMessage();
    }
}

// Get classes data with teacher and student count
try {
    $stmt = $pdo->query("
        SELECT c.*, t.full_name as homeroom_teacher,
               (SELECT COUNT(*) FROM students WHERE class_id = c.id AND status = 'active') as student_count
        FROM classes c 
        LEFT JOIN teachers t ON c.homeroom_teacher_id = t.id 
        ORDER BY c.class_level, c.class_name
    ");
    $classes = $stmt->fetchAll();
} catch(PDOException $e) {
    $classes = [];
    $error = 'Gagal mengambil data kelas';
}

$page_title = 'Data Kelas';
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
    background: linear-gradient(135deg, #fd7e14 0%, #e55a4e 100%);
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
    border-left: 4px solid #fd7e14;
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
    background-color: #fd7e14;
    color: white;
    border: none;
    font-weight: 600;
}

.btn-action {
    margin-right: 0.25rem;
    border-radius: 0.25rem;
}

.class-badge {
    font-size: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
}

.level-x { background: linear-gradient(45deg, #28a745, #20c997); }
.level-xi { background: linear-gradient(45deg, #17a2b8, #007bff); }
.level-xii { background: linear-gradient(45deg, #ffc107, #fd7e14); }
</style>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-school me-2"></i>Data Kelas
                </h2>
                <p class="mb-0 opacity-75">Kelola kelas dan wali kelas</p>
            </div>
            <a href="add-edit.php?action=add" class="btn btn-light btn-lg">
                <i class="fas fa-plus me-2"></i>Tambah Kelas
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
                        <i class="fas fa-door-open fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count($classes) ?></h3>
                        <small class="text-muted">Total Kelas</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-seedling fa-2x text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($classes, fn($c) => $c['class_level'] === 'X')) ?></h3>
                        <small class="text-muted">Kelas X</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-leaf fa-2x text-info"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($classes, fn($c) => $c['class_level'] === 'XI')) ?></h3>
                        <small class="text-muted">Kelas XI</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-tree fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($classes, fn($c) => $c['class_level'] === 'XII')) ?></h3>
                        <small class="text-muted">Kelas XII</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Level Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <select class="form-select" id="levelFilter">
                        <option value="">Semua Tingkat</option>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="yearFilter">
                        <option value="">Semua Tahun Ajaran</option>
                        <option value="2024/2025">2024/2025</option>
                        <option value="2023/2024">2023/2024</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama kelas atau wali kelas...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                        <i class="fas fa-undo me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="classesTable">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Wali Kelas</th>
                        <th>Kapasitas</th>
                        <th>Jumlah Siswa</th>
                        <th>Tahun Ajaran</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($classes as $class): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="class-badge text-white me-3 level-<?= strtolower($class['class_level']) ?>">
                                    <?= htmlspecialchars($class['class_level']) ?>
                                </span>
                                <div>
                                    <strong><?= htmlspecialchars($class['class_level'] . ' ' . $class['class_name']) ?></strong>
                                    <br><small class="text-muted">Ruang: <?= htmlspecialchars($class['room_number'] ?? 'Belum ditentukan') ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($class['homeroom_teacher']): ?>
                                <i class="fas fa-user-tie me-1 text-primary"></i>
                                <?= htmlspecialchars($class['homeroom_teacher']) ?>
                            <?php else: ?>
                                <span class="text-muted">
                                    <i class="fas fa-user-slash me-1"></i>Belum ditentukan
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <i class="fas fa-users me-1"></i><?= $class['capacity'] ?> siswa
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-<?= $class['student_count'] > $class['capacity'] ? 'danger' : 'success' ?> me-2">
                                    <?= $class['student_count'] ?>
                                </span>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-<?= $class['student_count'] > $class['capacity'] ? 'danger' : 'success' ?>" 
                                         style="width: <?= min(($class['student_count'] / $class['capacity']) * 100, 100) ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?= htmlspecialchars($class['academic_year']) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Aktif
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="detail.php?id=<?= $class['id'] ?>" 
                                   class="btn btn-outline-info btn-action" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add-edit.php?action=edit&id=<?= $class['id'] ?>" 
                                   class="btn btn-outline-primary btn-action" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $class['id'] ?>" 
                                   class="btn btn-outline-danger btn-action btn-delete" 
                                   title="Hapus"
                                   onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
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
let classesTable;

$(document).ready(function() {
    classesTable = $('#classesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        },
        pageLength: 25,
        order: [[0, 'asc']],
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
    
    // Custom filters
    $('#searchInput').on('keyup', function() {
        classesTable.search(this.value).draw();
    });
    
    $('#levelFilter').on('change', function() {
        classesTable.column(0).search(this.value).draw();
    });
    
    $('#yearFilter').on('change', function() {
        classesTable.column(4).search(this.value).draw();
    });
});

function resetFilters() {
    $('#levelFilter').val('');
    $('#yearFilter').val('');
    $('#searchInput').val('');
    classesTable.search('').columns().search('').draw();
}
</script>

<?php include '../../includes/footer.php'; ?>