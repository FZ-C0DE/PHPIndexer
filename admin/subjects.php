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
        $subject_code = sanitize($_POST['subject_code']);
        $subject_name = sanitize($_POST['subject_name']);
        $teacher_id = $_POST['teacher_id'] ?: null;
        $class_id = $_POST['class_id'] ?: null;
        $credit_hours = (int)$_POST['credit_hours'];
        
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO subjects (subject_code, subject_name, teacher_id, class_id, credit_hours) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$subject_code, $subject_name, $teacher_id, $class_id, $credit_hours]);
                $success = 'Data mata pelajaran berhasil ditambahkan';
            } else {
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE subjects SET subject_code = ?, subject_name = ?, teacher_id = ?, class_id = ?, credit_hours = ? WHERE id = ?");
                $stmt->execute([$subject_code, $subject_name, $teacher_id, $class_id, $credit_hours, $id]);
                $success = 'Data mata pelajaran berhasil diperbarui';
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
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Data mata pelajaran berhasil dihapus';
        $action = 'list';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

// Get subjects list
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT s.*, u.full_name as teacher_name, c.class_name, c.class_level
            FROM subjects s 
            LEFT JOIN teachers t ON s.teacher_id = t.id 
            LEFT JOIN users u ON t.user_id = u.id 
            LEFT JOIN classes c ON s.class_id = c.id
            ORDER BY s.subject_name
        ");
        $subjects = $stmt->fetchAll();
    } catch(PDOException $e) {
        $subjects = [];
        $error = 'Gagal mengambil data mata pelajaran';
    }
}

// Get teachers for dropdown
try {
    $stmt = $pdo->query("SELECT t.id, u.full_name FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.status = 'active' ORDER BY u.full_name");
    $teachers = $stmt->fetchAll();
} catch(PDOException $e) {
    $teachers = [];
}

// Get classes for dropdown
try {
    $stmt = $pdo->query("SELECT id, class_name, class_level FROM classes ORDER BY class_level, class_name");
    $classes = $stmt->fetchAll();
} catch(PDOException $e) {
    $classes = [];
}

// Get subject data for edit
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $subject = $stmt->fetch();
        
        if (!$subject) {
            $error = 'Data mata pelajaran tidak ditemukan';
            $action = 'list';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data mata pelajaran';
        $action = 'list';
    }
}

$page_title = 'Mata Pelajaran';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($action === 'add'): ?>
                        Tambah Mata Pelajaran
                    <?php elseif ($action === 'edit'): ?>
                        Edit Mata Pelajaran
                    <?php else: ?>
                        Mata Pelajaran
                    <?php endif; ?>
                </h1>
                
                <?php if ($action === 'list'): ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Mata Pelajaran
                    </a>
                </div>
                <?php else: ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="subjects.php" class="btn btn-outline-secondary">
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
                <!-- Subjects List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Mata Pelajaran</th>
                                        <th>Guru Pengampu</th>
                                        <th>Kelas</th>
                                        <th>SKS</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($subjects as $subject): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($subject['subject_code']) ?></td>
                                        <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                                        <td><?= $subject['teacher_name'] ? htmlspecialchars($subject['teacher_name']) : '-' ?></td>
                                        <td><?= $subject['class_name'] ? htmlspecialchars($subject['class_level'] . ' ' . $subject['class_name']) : '-' ?></td>
                                        <td><?= $subject['credit_hours'] ?></td>
                                        <td>
                                            <a href="?action=edit&id=<?= $subject['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?action=delete&id=<?= $subject['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
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
                                <input type="hidden" name="id" value="<?= $subject['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="subject_code" class="form-label">Kode Mata Pelajaran *</label>
                                    <input type="text" class="form-control" id="subject_code" name="subject_code" 
                                           value="<?= htmlspecialchars($subject['subject_code'] ?? '') ?>" required>
                                    <div class="form-text">Contoh: MAT001, IPA002, BHS001</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="subject_name" class="form-label">Nama Mata Pelajaran *</label>
                                    <input type="text" class="form-control" id="subject_name" name="subject_name" 
                                           value="<?= htmlspecialchars($subject['subject_name'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="teacher_id" class="form-label">Guru Pengampu</label>
                                    <select class="form-select select2" id="teacher_id" name="teacher_id">
                                        <option value="">Pilih Guru</option>
                                        <?php foreach($teachers as $teacher): ?>
                                            <option value="<?= $teacher['id'] ?>" 
                                                    <?= ($subject['teacher_id'] ?? '') == $teacher['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($teacher['full_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="class_id" class="form-label">Kelas</label>
                                    <select class="form-select select2" id="class_id" name="class_id">
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach($classes as $class): ?>
                                            <option value="<?= $class['id'] ?>" 
                                                    <?= ($subject['class_id'] ?? '') == $class['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($class['class_level'] . ' ' . $class['class_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="credit_hours" class="form-label">Jam Pelajaran/Minggu</label>
                                    <input type="number" class="form-control" id="credit_hours" name="credit_hours" 
                                           value="<?= $subject['credit_hours'] ?? 2 ?>" min="1" max="10">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="subjects.php" class="btn btn-outline-secondary">Batal</a>
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