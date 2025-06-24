<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

checkLogin();
checkRole('teacher');

$error = '';
$success = '';

// Get teacher information
try {
    $stmt = $pdo->prepare("
        SELECT t.*, u.username, u.email, u.full_name 
        FROM teachers t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $teacher = $stmt->fetch();
    
    if (!$teacher) {
        $error = 'Data guru tidak ditemukan';
    }
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $teacher = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    try {
        $pdo->beginTransaction();
        
        // Update user table
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $password, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $_SESSION['user_id']]);
        }
        
        // Update teacher table
        $stmt = $pdo->prepare("UPDATE teachers SET phone = ?, address = ? WHERE user_id = ?");
        $stmt->execute([$phone, $address, $_SESSION['user_id']]);
        
        // Update session
        $_SESSION['full_name'] = $full_name;
        
        $pdo->commit();
        $success = 'Profil berhasil diperbarui';
        
        // Refresh teacher data
        $stmt = $pdo->prepare("
            SELECT t.*, u.username, u.email, u.full_name 
            FROM teachers t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $teacher = $stmt->fetch();
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get subjects taught by teacher
try {
    $stmt = $pdo->prepare("
        SELECT s.*, c.class_name, c.class_level
        FROM subjects s 
        LEFT JOIN classes c ON s.class_id = c.id 
        WHERE s.teacher_id = ?
        ORDER BY s.subject_name
    ");
    $stmt->execute([$teacher['id']]);
    $subjects = $stmt->fetchAll();
} catch(PDOException $e) {
    $subjects = [];
}

// Get homeroom classes
try {
    $stmt = $pdo->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM students WHERE class_id = c.id AND status = 'active') as student_count
        FROM classes c 
        WHERE c.teacher_id = ?
    ");
    $stmt->execute([$teacher['id']]);
    $homeroom_classes = $stmt->fetchAll();
} catch(PDOException $e) {
    $homeroom_classes = [];
}

$page_title = 'Profil Saya';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Profil Saya</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-primary fs-6">
                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($teacher['full_name'] ?? 'Guru') ?>
                    </span>
                </div>
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

            <div class="row">
                <!-- Profile Form -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-user-edit me-2"></i>Edit Profil
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label">Nama Lengkap *</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               value="<?= htmlspecialchars($teacher['full_name'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="teacher_id" class="form-label">ID Guru</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($teacher['teacher_id'] ?? '') ?>" readonly>
                                        <div class="form-text">ID Guru tidak dapat diubah</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($teacher['username'] ?? '') ?>" readonly>
                                        <div class="form-text">Username tidak dapat diubah</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($teacher['email'] ?? '') ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Telepon</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($teacher['address'] ?? '') ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Masuk</label>
                                        <input type="text" class="form-control" 
                                               value="<?= $teacher['hire_date'] ? date('d/m/Y', strtotime($teacher['hire_date'])) : '-' ?>" readonly>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <input type="text" class="form-control" 
                                               value="<?= ucfirst($teacher['status'] ?? 'active') ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Summary Info -->
                <div class="col-lg-4 mb-4">
                    <!-- Teaching Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Ringkasan Mengajar
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-0"><?= count($subjects) ?></h4>
                                        <small class="text-muted">Mata Pelajaran</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success mb-0"><?= count($homeroom_classes) ?></h4>
                                    <small class="text-muted">Kelas Wali</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subjects Taught -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-book me-2"></i>Mata Pelajaran
                        </div>
                        <div class="card-body">
                            <?php if (empty($subjects)): ?>
                                <p class="text-muted text-center">Belum ada mata pelajaran</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($subjects as $subject): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($subject['subject_name']) ?></h6>
                                                <small class="text-muted">
                                                    <?= $subject['class_name'] ? htmlspecialchars($subject['class_level'] . ' ' . $subject['class_name']) : 'Semua Kelas' ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-info rounded-pill"><?= $subject['credit_hours'] ?>j</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Homeroom Classes -->
                    <?php if (!empty($homeroom_classes)): ?>
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-users me-2"></i>Kelas Wali
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php foreach($homeroom_classes as $class): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($class['class_level'] . ' ' . $class['class_name']) ?></h6>
                                            <small class="text-muted">Tahun: <?= htmlspecialchars($class['academic_year']) ?></small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill"><?= $class['student_count'] ?> siswa</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>