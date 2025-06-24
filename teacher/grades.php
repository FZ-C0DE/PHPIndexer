<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

checkLogin();
checkRole('teacher');

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';

// Get teacher information
try {
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $teacher = $stmt->fetch();
    
    if (!$teacher) {
        $error = 'Data guru tidak ditemukan';
        header("Location: dashboard.php");
        exit();
    }
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $teacher = null;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $student_id = $_POST['student_id'];
        $subject_id = $_POST['subject_id'];
        $exam_type = sanitize($_POST['exam_type']);
        $score = (float)$_POST['score'];
        $max_score = (float)$_POST['max_score'];
        $exam_date = $_POST['exam_date'];
        $semester = sanitize($_POST['semester']);
        $academic_year = sanitize($_POST['academic_year']);
        
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, exam_type, score, max_score, exam_date, semester, academic_year, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$student_id, $subject_id, $exam_type, $score, $max_score, $exam_date, $semester, $academic_year, $_SESSION['user_id']]);
                $success = 'Data nilai siswa berhasil ditambahkan';
            } else {
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE grades SET student_id = ?, subject_id = ?, exam_type = ?, score = ?, max_score = ?, exam_date = ?, semester = ?, academic_year = ? WHERE id = ?");
                $stmt->execute([$student_id, $subject_id, $exam_type, $score, $max_score, $exam_date, $semester, $academic_year, $id]);
                $success = 'Data nilai siswa berhasil diperbarui';
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
        $stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Data nilai berhasil dihapus';
        $action = 'list';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

// Get grades list for teacher's subjects
if ($action === 'list') {
    try {
        $stmt = $pdo->prepare("
            SELECT g.*, s.full_name as student_name, s.student_id as student_number,
                   subj.subject_name, c.class_name, c.class_level
            FROM grades g
            JOIN students s ON g.student_id = s.id
            JOIN subjects subj ON g.subject_id = subj.id
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE subj.teacher_id = ?
            ORDER BY g.exam_date DESC, s.full_name
        ");
        $stmt->execute([$teacher['id']]);
        $grades = $stmt->fetchAll();
    } catch(PDOException $e) {
        $grades = [];
        $error = 'Gagal mengambil data nilai';
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

// Get students from teacher's subjects
try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT st.*, c.class_name, c.class_level
        FROM students st
        JOIN classes c ON st.class_id = c.id
        WHERE st.class_id IN (
            SELECT DISTINCT s.class_id FROM subjects s WHERE s.teacher_id = ?
        ) AND st.status = 'active'
        ORDER BY c.class_level, c.class_name, st.full_name
    ");
    $stmt->execute([$teacher['id']]);
    $students = $stmt->fetchAll();
} catch(PDOException $e) {
    $students = [];
}

// Get grade data for edit
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM grades WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $grade = $stmt->fetch();
        
        if (!$grade) {
            $error = 'Data nilai tidak ditemukan';
            $action = 'list';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data nilai';
        $action = 'list';
    }
}

$page_title = 'Nilai Siswa';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($action === 'add'): ?>
                        Tambah Nilai Siswa
                    <?php elseif ($action === 'edit'): ?>
                        Edit Nilai Siswa
                    <?php else: ?>
                        Nilai Siswa
                    <?php endif; ?>
                </h1>
                
                <?php if ($action === 'list'): ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Nilai
                    </a>
                </div>
                <?php else: ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="grades.php" class="btn btn-outline-secondary">
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
                <!-- Grades List -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-star me-2"></i>Data Nilai Siswa
                    </div>
                    <div class="card-body">
                        <?php if (empty($grades)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                <h5>Belum ada data nilai</h5>
                                <p class="text-muted">Mulai dengan menambahkan nilai siswa</p>
                                <a href="?action=add" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Tambah Nilai
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>Nama Siswa</th>
                                            <th>ID Siswa</th>
                                            <th>Kelas</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Jenis Ujian</th>
                                            <th>Nilai</th>
                                            <th>Persentase</th>
                                            <th>Tanggal</th>
                                            <th>Semester</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($grades as $grd): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($grd['student_name']) ?></td>
                                            <td><?= htmlspecialchars($grd['student_number']) ?></td>
                                            <td><?= htmlspecialchars($grd['class_level'] . ' ' . $grd['class_name']) ?></td>
                                            <td><?= htmlspecialchars($grd['subject_name']) ?></td>
                                            <td><?= htmlspecialchars($grd['exam_type']) ?></td>
                                            <td><?= number_format($grd['score'], 1) ?>/<?= number_format($grd['max_score'], 0) ?></td>
                                            <td>
                                                <?php 
                                                $percentage = ($grd['score'] / $grd['max_score']) * 100;
                                                $badge_class = $percentage >= 80 ? 'success' : ($percentage >= 70 ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge bg-<?= $badge_class ?>">
                                                    <?= number_format($percentage, 1) ?>%
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($grd['exam_date'])) ?></td>
                                            <td><?= htmlspecialchars($grd['semester'] . ' ' . $grd['academic_year']) ?></td>
                                            <td>
                                                <a href="?action=edit&id=<?= $grd['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?action=delete&id=<?= $grd['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            
            <?php else: ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <?php if ($action === 'edit'): ?>
                                <input type="hidden" name="id" value="<?= $grade['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_id" class="form-label">Siswa *</label>
                                    <select class="form-select select2" id="student_id" name="student_id" required>
                                        <option value="">Pilih Siswa</option>
                                        <?php foreach($students as $student): ?>
                                            <option value="<?= $student['id'] ?>" 
                                                    <?= ($grade['student_id'] ?? '') == $student['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($student['full_name'] . ' (' . $student['student_id'] . ') - ' . $student['class_level'] . ' ' . $student['class_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="subject_id" class="form-label">Mata Pelajaran *</label>
                                    <select class="form-select select2" id="subject_id" name="subject_id" required>
                                        <option value="">Pilih Mata Pelajaran</option>
                                        <?php foreach($subjects as $subject): ?>
                                            <option value="<?= $subject['id'] ?>" 
                                                    <?= ($grade['subject_id'] ?? '') == $subject['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($subject['subject_name'] . ' - ' . ($subject['class_name'] ? $subject['class_level'] . ' ' . $subject['class_name'] : 'Semua Kelas')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="exam_type" class="form-label">Jenis Ujian *</label>
                                    <select class="form-select" id="exam_type" name="exam_type" required>
                                        <option value="">Pilih Jenis Ujian</option>
                                        <option value="UTS" <?= ($grade['exam_type'] ?? '') == 'UTS' ? 'selected' : '' ?>>UTS (Ujian Tengah Semester)</option>
                                        <option value="UAS" <?= ($grade['exam_type'] ?? '') == 'UAS' ? 'selected' : '' ?>>UAS (Ujian Akhir Semester)</option>
                                        <option value="Quiz" <?= ($grade['exam_type'] ?? '') == 'Quiz' ? 'selected' : '' ?>>Quiz</option>
                                        <option value="Tugas" <?= ($grade['exam_type'] ?? '') == 'Tugas' ? 'selected' : '' ?>>Tugas</option>
                                        <option value="Praktikum" <?= ($grade['exam_type'] ?? '') == 'Praktikum' ? 'selected' : '' ?>>Praktikum</option>
                                        <option value="Presentasi" <?= ($grade['exam_type'] ?? '') == 'Presentasi' ? 'selected' : '' ?>>Presentasi</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="exam_date" class="form-label">Tanggal Ujian *</label>
                                    <input type="date" class="form-control" id="exam_date" name="exam_date" 
                                           value="<?= $grade['exam_date'] ?? date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="score" class="form-label">Nilai *</label>
                                    <input type="number" class="form-control" id="score" name="score" 
                                           value="<?= $grade['score'] ?? '' ?>" min="0" max="100" step="0.1" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="max_score" class="form-label">Nilai Maksimal *</label>
                                    <input type="number" class="form-control" id="max_score" name="max_score" 
                                           value="<?= $grade['max_score'] ?? '100' ?>" min="1" max="100" step="0.1" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Persentase</label>
                                    <input type="text" class="form-control" id="percentage" readonly 
                                           style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="semester" class="form-label">Semester *</label>
                                    <select class="form-select" id="semester" name="semester" required>
                                        <option value="Ganjil" <?= ($grade['semester'] ?? 'Ganjil') == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                        <option value="Genap" <?= ($grade['semester'] ?? '') == 'Genap' ? 'selected' : '' ?>>Genap</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="academic_year" class="form-label">Tahun Ajaran *</label>
                                    <input type="text" class="form-control" id="academic_year" name="academic_year" 
                                           value="<?= $grade['academic_year'] ?? '2024/2025' ?>" 
                                           placeholder="2024/2025" required>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="grades.php" class="btn btn-outline-secondary">Batal</a>
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

<script>
// Calculate percentage automatically
function calculatePercentage() {
    const score = parseFloat(document.getElementById('score').value) || 0;
    const maxScore = parseFloat(document.getElementById('max_score').value) || 100;
    const percentage = (score / maxScore) * 100;
    
    document.getElementById('percentage').value = isNaN(percentage) ? '0%' : percentage.toFixed(1) + '%';
    
    // Change color based on percentage
    const percentageField = document.getElementById('percentage');
    if (percentage >= 80) {
        percentageField.style.color = '#10b981';
        percentageField.style.fontWeight = 'bold';
    } else if (percentage >= 70) {
        percentageField.style.color = '#f59e0b';
        percentageField.style.fontWeight = 'bold';
    } else {
        percentageField.style.color = '#ef4444';
        percentageField.style.fontWeight = 'bold';
    }
}

document.getElementById('score').addEventListener('input', calculatePercentage);
document.getElementById('max_score').addEventListener('input', calculatePercentage);

// Calculate initial percentage if editing
document.addEventListener('DOMContentLoaded', calculatePercentage);
</script>

<?php include 'includes/footer.php'; ?>