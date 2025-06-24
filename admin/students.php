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
        $student_id = sanitize($_POST['student_id']);
        $full_name = sanitize($_POST['full_name']);
        $class_id = $_POST['class_id'] ?: null;
        $birth_date = $_POST['birth_date'] ?: null;
        $gender = $_POST['gender'];
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $parent_name = sanitize($_POST['parent_name']);
        $parent_phone = sanitize($_POST['parent_phone']);
        $enrollment_date = $_POST['enrollment_date'];
        $status = $_POST['status'];
        
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, class_id, birth_date, gender, phone, address, parent_name, parent_phone, enrollment_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$student_id, $full_name, $class_id, $birth_date, $gender, $phone, $address, $parent_name, $parent_phone, $enrollment_date, $status]);
                $success = 'Data siswa berhasil ditambahkan';
            } else {
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE students SET student_id = ?, full_name = ?, class_id = ?, birth_date = ?, gender = ?, phone = ?, address = ?, parent_name = ?, parent_phone = ?, enrollment_date = ?, status = ? WHERE id = ?");
                $stmt->execute([$student_id, $full_name, $class_id, $birth_date, $gender, $phone, $address, $parent_name, $parent_phone, $enrollment_date, $status, $id]);
                $success = 'Data siswa berhasil diperbarui';
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
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Data siswa berhasil dihapus';
        $action = 'list';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

// Get students list
if ($action === 'list') {
    try {
        $stmt = $pdo->query("
            SELECT s.*, c.class_name, c.class_level 
            FROM students s 
            LEFT JOIN classes c ON s.class_id = c.id 
            ORDER BY s.created_at DESC
        ");
        $students = $stmt->fetchAll();
    } catch(PDOException $e) {
        $students = [];
        $error = 'Gagal mengambil data siswa';
    }
}

// Get classes for dropdown
try {
    $stmt = $pdo->query("SELECT id, class_name, class_level FROM classes ORDER BY class_level, class_name");
    $classes = $stmt->fetchAll();
} catch(PDOException $e) {
    $classes = [];
}

// Get student data for edit
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $student = $stmt->fetch();
        
        if (!$student) {
            $error = 'Data siswa tidak ditemukan';
            $action = 'list';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data siswa';
        $action = 'list';
    }
}

$page_title = 'Data Siswa';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($action === 'add'): ?>
                        Tambah Siswa
                    <?php elseif ($action === 'edit'): ?>
                        Edit Siswa
                    <?php else: ?>
                        Data Siswa
                    <?php endif; ?>
                </h1>
                
                <?php if ($action === 'list'): ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Siswa
                    </a>
                </div>
                <?php else: ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="students.php" class="btn btn-outline-secondary">
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
                <!-- Students List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>ID Siswa</th>
                                        <th>Nama Lengkap</th>
                                        <th>Kelas</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Orang Tua</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($students as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                        <td><?= $student['class_name'] ? htmlspecialchars($student['class_level'] . ' ' . $student['class_name']) : '-' ?></td>
                                        <td><?= $student['gender'] == 'male' ? 'Laki-laki' : 'Perempuan' ?></td>
                                        <td><?= htmlspecialchars($student['parent_name']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $student['status'] == 'active' ? 'success' : ($student['status'] == 'graduated' ? 'primary' : 'danger') ?>">
                                                <?= ucfirst($student['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?action=edit&id=<?= $student['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?action=delete&id=<?= $student['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
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
                                <input type="hidden" name="id" value="<?= $student['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_id" class="form-label">ID Siswa *</label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" 
                                           value="<?= htmlspecialchars($student['student_id'] ?? '') ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($student['full_name'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="class_id" class="form-label">Kelas</label>
                                    <select class="form-select select2" id="class_id" name="class_id">
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach($classes as $class): ?>
                                            <option value="<?= $class['id'] ?>" 
                                                    <?= ($student['class_id'] ?? '') == $class['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($class['class_level'] . ' ' . $class['class_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="male" <?= ($student['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="female" <?= ($student['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                           value="<?= $student['birth_date'] ?? '' ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Telepon Siswa</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="parent_name" class="form-label">Nama Orang Tua</label>
                                    <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                           value="<?= htmlspecialchars($student['parent_name'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="parent_phone" class="form-label">Telepon Orang Tua</label>
                                    <input type="text" class="form-control" id="parent_phone" name="parent_phone" 
                                           value="<?= htmlspecialchars($student['parent_phone'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="enrollment_date" class="form-label">Tanggal Masuk</label>
                                    <input type="date" class="form-control" id="enrollment_date" name="enrollment_date" 
                                           value="<?= $student['enrollment_date'] ?? date('Y-m-d') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?= ($student['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="inactive" <?= ($student['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Non-aktif</option>
                                        <option value="graduated" <?= ($student['status'] ?? '') == 'graduated' ? 'selected' : '' ?>>Lulus</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($student['address'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="students.php" class="btn btn-outline-secondary">Batal</a>
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