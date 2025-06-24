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
        $class_name = sanitize($_POST['class_name']);
        $class_level = sanitize($_POST['class_level']);
        $teacher_id = $_POST['teacher_id'] ?: null;
        $academic_year = sanitize($_POST['academic_year']);
        $capacity = (int)$_POST['capacity'];
        
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO classes (class_name, class_level, teacher_id, academic_year, capacity) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$class_name, $class_level, $teacher_id, $academic_year, $capacity]);
                $success = 'Data kelas berhasil ditambahkan';
            } else {
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE classes SET class_name = ?, class_level = ?, teacher_id = ?, academic_year = ?, capacity = ? WHERE id = ?");
                $stmt->execute([$class_name, $class_level, $teacher_id, $academic_year, $capacity, $id]);
                $success = 'Data kelas berhasil diperbarui';
            }
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Data kelas berhasil dihapus';
        $action = 'list';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

// Get classes list
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT c.*, u.full_name as teacher_name,
                   (SELECT COUNT(*) FROM students WHERE class_id = c.id AND status = 'active') as student_count
            FROM classes c 
            LEFT JOIN teachers t ON c.teacher_id = t.id 
            LEFT JOIN users u ON t.user_id = u.id 
            ORDER BY c.class_level, c.class_name
        ");
        $classes = $stmt->fetchAll();
    } catch(PDOException $e) {
        $classes = [];
        $error = 'Gagal mengambil data kelas';
    }
}

// Get teachers for dropdown
try {
    $stmt = $pdo->query("SELECT t.id, u.full_name FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.status = 'active' ORDER BY u.full_name");
    $teachers = $stmt->fetchAll();
} catch(PDOException $e) {
    $teachers = [];
}

// Get class data for edit
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $class = $stmt->fetch();
        
        if (!$class) {
            $error = 'Data kelas tidak ditemukan';
            $action = 'list';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data kelas';
        $action = 'list';
    }
}

$page_title = 'Data Kelas';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($action === 'add'): ?>
                        Tambah Kelas
                    <?php elseif ($action === 'edit'): ?>
                        Edit Kelas
                    <?php else: ?>
                        Data Kelas
                    <?php endif; ?>
                </h1>
                
                <?php if ($action === 'list'): ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Kelas
                    </a>
                </div>
                <?php else: ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="classes.php" class="btn btn-outline-secondary">
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
                <!-- Classes List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Nama Kelas</th>
                                        <th>Tingkat</th>
                                        <th>Wali Kelas</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Jumlah Siswa</th>
                                        <th>Kapasitas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($classes as $cls): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($cls['class_name']) ?></td>
                                        <td><?= htmlspecialchars($cls['class_level']) ?></td>
                                        <td><?= $cls['teacher_name'] ? htmlspecialchars($cls['teacher_name']) : '-' ?></td>
                                        <td><?= htmlspecialchars($cls['academic_year']) ?></td>
                                        <td><?= $cls['student_count'] ?></td>
                                        <td><?= $cls['capacity'] ?></td>
                                        <td>
                                            <a href="?action=edit&id=<?= $cls['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?action=delete&id=<?= $cls['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
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
                                <input type="hidden" name="id" value="<?= $class['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="class_name" class="form-label">Nama Kelas *</label>
                                    <input type="text" class="form-control" id="class_name" name="class_name" 
                                           value="<?= htmlspecialchars($class['class_name'] ?? '') ?>" required>
                                    <div class="form-text">Contoh: A, B, IPA-1, IPS-2</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="class_level" class="form-label">Tingkat Kelas *</label>
                                    <select class="form-select" id="class_level" name="class_level" required>
                                        <option value="">Pilih Tingkat</option>
                                        <option value="X" <?= ($class['class_level'] ?? '') == 'X' ? 'selected' : '' ?>>Kelas X</option>
                                        <option value="XI" <?= ($class['class_level'] ?? '') == 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                                        <option value="XII" <?= ($class['class_level'] ?? '') == 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="teacher_id" class="form-label">Wali Kelas</label>
                                    <select class="form-select select2" id="teacher_id" name="teacher_id">
                                        <option value="">Pilih Wali Kelas</option>
                                        <?php foreach($teachers as $teacher): ?>
                                            <option value="<?= $teacher['id'] ?>" 
                                                    <?= ($class['teacher_id'] ?? '') == $teacher['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($teacher['full_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="academic_year" class="form-label">Tahun Ajaran *</label>
                                    <input type="text" class="form-control" id="academic_year" name="academic_year" 
                                           value="<?= htmlspecialchars($class['academic_year'] ?? date('Y') . '/' . (date('Y') + 1)) ?>" 
                                           placeholder="2024/2025" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="capacity" class="form-label">Kapasitas Siswa</label>
                                    <input type="number" class="form-control" id="capacity" name="capacity" 
                                           value="<?= $class['capacity'] ?? 30 ?>" min="1" max="50">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="classes.php" class="btn btn-outline-secondary">Batal</a>
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