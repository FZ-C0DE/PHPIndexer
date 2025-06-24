<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Siswa berhasil dihapus';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus siswa: ' . $e->getMessage();
    }
}

// Get students data with class info
try {
    $stmt = $pdo->query("
        SELECT s.*, c.class_name, c.class_level, c.academic_year
        FROM students s 
        LEFT JOIN classes c ON s.class_id = c.id 
        ORDER BY s.full_name
    ");
    $students = $stmt->fetchAll();
} catch(PDOException $e) {
    $students = [];
    $error = 'Gagal mengambil data siswa';
}

$page_title = 'Data Siswa';
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
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    border-left: 4px solid #28a745;
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
    background-color: #28a745;
    color: white;
    border: none;
    font-weight: 600;
}

.btn-action {
    margin-right: 0.25rem;
    border-radius: 0.25rem;
}

.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #28a745, #20c997);
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
                    <i class="fas fa-user-graduate me-2"></i>Data Siswa
                </h2>
                <p class="mb-0 opacity-75">Kelola data siswa dan informasi akademik</p>
            </div>
            <a href="add-edit.php?action=add" class="btn btn-light btn-lg">
                <i class="fas fa-plus me-2"></i>Tambah Siswa
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
                        <h3 class="mb-0"><?= count($students) ?></h3>
                        <small class="text-muted">Total Siswa</small>
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
                        <h3 class="mb-0"><?= count(array_filter($students, fn($s) => $s['status'] === 'active')) ?></h3>
                        <small class="text-muted">Siswa Aktif</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-graduation-cap fa-2x text-info"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($students, fn($s) => $s['class_level'] === 'XII')) ?></h3>
                        <small class="text-muted">Kelas XII</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-trophy fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">85%</h3>
                        <small class="text-muted">Tingkat Kelulusan</small>
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
                        <option value="">Semua Kelas</option>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                        <option value="graduated">Lulus</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama atau NISN...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary" onclick="resetFilters()">
                        <i class="fas fa-undo me-1"></i>Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="studentsTable">
                <thead>
                    <tr>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jenis Kelamin</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $student): ?>
                    <tr>
                        <td>
                            <strong class="text-primary"><?= htmlspecialchars($student['student_id']) ?></strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="student-avatar me-3">
                                    <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars($student['full_name']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($student['place_of_birth']) ?>, <?= date('d/m/Y', strtotime($student['date_of_birth'])) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <?= htmlspecialchars($student['class_level'] . ' ' . $student['class_name']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($student['gender'] === 'male'): ?>
                                <i class="fas fa-mars text-primary me-1"></i>Laki-laki
                            <?php else: ?>
                                <i class="fas fa-venus text-danger me-1"></i>Perempuan
                            <?php endif; ?>
                        </td>
                        <td>
                            <i class="fas fa-phone me-1"></i><?= htmlspecialchars($student['phone']) ?>
                        </td>
                        <td>
                            <?php 
                            $statusColors = [
                                'active' => 'success',
                                'inactive' => 'secondary', 
                                'graduated' => 'primary'
                            ];
                            $statusTexts = [
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                                'graduated' => 'Lulus'
                            ];
                            ?>
                            <span class="badge bg-<?= $statusColors[$student['status']] ?>">
                                <?= $statusTexts[$student['status']] ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="detail.php?id=<?= $student['id'] ?>" 
                                   class="btn btn-outline-info btn-action" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add-edit.php?action=edit&id=<?= $student['id'] ?>" 
                                   class="btn btn-outline-primary btn-action" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $student['id'] ?>" 
                                   class="btn btn-outline-danger btn-action btn-delete" 
                                   title="Hapus"
                                   onclick="return confirm('Yakin ingin menghapus siswa ini?')">
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
let studentsTable;

$(document).ready(function() {
    studentsTable = $('#studentsTable').DataTable({
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
    
    // Custom search
    $('#searchInput').on('keyup', function() {
        studentsTable.search(this.value).draw();
    });
    
    // Class filter
    $('#classFilter').on('change', function() {
        studentsTable.column(2).search(this.value).draw();
    });
    
    // Status filter
    $('#statusFilter').on('change', function() {
        studentsTable.column(5).search(this.value).draw();
    });
});

function resetFilters() {
    $('#classFilter').val('');
    $('#statusFilter').val('');
    $('#searchInput').val('');
    studentsTable.search('').columns().search('').draw();
}
</script>

<?php include '../../includes/footer.php'; ?>