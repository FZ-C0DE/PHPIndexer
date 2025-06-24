<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Mata pelajaran berhasil dihapus';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus mata pelajaran: ' . $e->getMessage();
    }
}

// Get subjects data with teacher and class info
try {
    $stmt = $pdo->query("
        SELECT s.*, t.full_name as teacher_name, c.class_name, c.class_level
        FROM subjects s 
        LEFT JOIN teachers t ON s.teacher_id = t.id 
        LEFT JOIN classes c ON s.class_id = c.id 
        ORDER BY c.class_level, s.subject_name
    ");
    $subjects = $stmt->fetchAll();
} catch(PDOException $e) {
    $subjects = [];
    $error = 'Gagal mengambil data mata pelajaran';
}

$page_title = 'Mata Pelajaran';
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
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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
    border-left: 4px solid #17a2b8;
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
    background-color: #17a2b8;
    color: white;
    border: none;
    font-weight: 600;
}

.btn-action {
    margin-right: 0.25rem;
    border-radius: 0.25rem;
}

.subject-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #17a2b8, #138496);
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
                    <i class="fas fa-book-open me-2"></i>Mata Pelajaran
                </h2>
                <p class="mb-0 opacity-75">Kelola mata pelajaran dan pengampu</p>
            </div>
            <a href="add-edit.php?action=add" class="btn btn-light btn-lg">
                <i class="fas fa-plus me-2"></i>Tambah Mata Pelajaran
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
                        <i class="fas fa-book fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count($subjects) ?></h3>
                        <small class="text-muted">Total Mata Pelajaran</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-calculator fa-2x text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= array_sum(array_column($subjects, 'credit_hours')) ?></h3>
                        <small class="text-muted">Total SKS</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-chalkboard-teacher fa-2x text-info"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_unique(array_column($subjects, 'teacher_id'))) ?></h3>
                        <small class="text-muted">Guru Pengampu</small>
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
                        <h3 class="mb-0"><?= count(array_unique(array_column($subjects, 'class_id'))) ?></h3>
                        <small class="text-muted">Kelas Aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <select class="form-select" id="classFilter">
                        <option value="">Semua Tingkat</option>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="teacherFilter">
                        <option value="">Semua Guru</option>
                        <?php 
                        $teachers = array_unique(array_filter(array_column($subjects, 'teacher_name')));
                        foreach($teachers as $teacher): 
                        ?>
                            <option value="<?= htmlspecialchars($teacher) ?>"><?= htmlspecialchars($teacher) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama mata pelajaran...">
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
            <table class="table table-hover mb-0" id="subjectsTable">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Guru Pengampu</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($subjects as $subject): ?>
                    <tr>
                        <td>
                            <code class="text-primary"><?= htmlspecialchars($subject['subject_code']) ?></code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="subject-icon me-3">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars($subject['subject_name']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($subject['description'] ?? 'Tidak ada deskripsi') ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <?= htmlspecialchars($subject['class_level'] . ' ' . $subject['class_name']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($subject['teacher_name']): ?>
                                <i class="fas fa-user-tie me-1 text-primary"></i>
                                <?= htmlspecialchars($subject['teacher_name']) ?>
                            <?php else: ?>
                                <span class="text-muted">
                                    <i class="fas fa-user-slash me-1"></i>Belum ditentukan
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <?= $subject['credit_hours'] ?> JP
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                <?= htmlspecialchars($subject['semester']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="detail.php?id=<?= $subject['id'] ?>" 
                                   class="btn btn-outline-info btn-action" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add-edit.php?action=edit&id=<?= $subject['id'] ?>" 
                                   class="btn btn-outline-primary btn-action" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $subject['id'] ?>" 
                                   class="btn btn-outline-danger btn-action btn-delete" 
                                   title="Hapus"
                                   onclick="return confirm('Yakin ingin menghapus mata pelajaran ini?')">
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
let subjectsTable;

$(document).ready(function() {
    subjectsTable = $('#subjectsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        },
        pageLength: 25,
        order: [[1, 'asc']],
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
        subjectsTable.search(this.value).draw();
    });
    
    $('#classFilter').on('change', function() {
        subjectsTable.column(2).search(this.value).draw();
    });
    
    $('#teacherFilter').on('change', function() {
        subjectsTable.column(3).search(this.value).draw();
    });
});

function resetFilters() {
    $('#classFilter').val('');
    $('#teacherFilter').val('');
    $('#searchInput').val('');
    subjectsTable.search('').columns().search('').draw();
}
</script>

<?php include '../../includes/footer.php'; ?>