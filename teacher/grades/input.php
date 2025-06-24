<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('teacher');

$error = '';
$success = '';
$subject_id = $_GET['subject_id'] ?? null;
$class_id = $_GET['class_id'] ?? null;

// Get teacher information
try {
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $teacher = $stmt->fetch();
    
    if (!$teacher) {
        $error = 'Data guru tidak ditemukan';
        header("Location: ../dashboard.php");
        exit();
    }
} catch(PDOException $e) {
    $error = 'Terjadi kesalahan sistem';
    $teacher = null;
}

// Get subject info
if ($subject_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, c.class_name, c.class_level 
            FROM subjects s 
            LEFT JOIN classes c ON s.class_id = c.id 
            WHERE s.id = ? AND s.teacher_id = ?
        ");
        $stmt->execute([$subject_id, $teacher['id']]);
        $subject = $stmt->fetch();
        
        if (!$subject) {
            $error = 'Mata pelajaran tidak ditemukan atau Anda tidak berhak mengakses';
            $subject_id = null;
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data mata pelajaran';
        $subject_id = null;
    }
}

// Get students for the subject
$students = [];
if ($subject_id && $subject) {
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, 
                   COALESCE(g.score, 0) as current_score,
                   g.id as grade_id
            FROM students s 
            WHERE s.class_id = ? AND s.status = 'active'
            LEFT JOIN grades g ON s.id = g.student_id AND g.subject_id = ? 
                AND g.exam_type = ? AND g.semester = ? AND g.academic_year = ?
            ORDER BY s.full_name
        ");
        
        $exam_type = $_GET['exam_type'] ?? 'UTS';
        $semester = $_GET['semester'] ?? CURRENT_SEMESTER;
        $academic_year = $_GET['academic_year'] ?? CURRENT_ACADEMIC_YEAR;
        
        $stmt->execute([$subject['class_id'], $subject_id, $exam_type, $semester, $academic_year]);
        $students = $stmt->fetchAll();
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data siswa';
        $students = [];
    }
}

// Handle batch grade input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batch_input'])) {
    $exam_type = sanitize($_POST['exam_type']);
    $max_score = (float)$_POST['max_score'];
    $exam_date = $_POST['exam_date'];
    $semester = sanitize($_POST['semester']);
    $academic_year = sanitize($_POST['academic_year']);
    
    $success_count = 0;
    $error_count = 0;
    
    try {
        $pdo->beginTransaction();
        
        foreach ($_POST['grades'] as $student_id => $score) {
            if (!empty($score) && is_numeric($score)) {
                $score = (float)$score;
                
                // Check if grade already exists
                $stmt = $pdo->prepare("
                    SELECT id FROM grades 
                    WHERE student_id = ? AND subject_id = ? AND exam_type = ? 
                    AND semester = ? AND academic_year = ?
                ");
                $stmt->execute([$student_id, $subject_id, $exam_type, $semester, $academic_year]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Update existing grade
                    $stmt = $pdo->prepare("
                        UPDATE grades 
                        SET score = ?, max_score = ?, exam_date = ?, recorded_by = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$score, $max_score, $exam_date, $_SESSION['user_id'], $existing['id']]);
                } else {
                    // Insert new grade
                    $stmt = $pdo->prepare("
                        INSERT INTO grades (student_id, subject_id, exam_type, score, max_score, exam_date, semester, academic_year, recorded_by) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$student_id, $subject_id, $exam_type, $score, $max_score, $exam_date, $semester, $academic_year, $_SESSION['user_id']]);
                }
                $success_count++;
            }
        }
        
        $pdo->commit();
        $success = "Berhasil menyimpan nilai untuk {$success_count} siswa";
        
        // Refresh students data
        $stmt = $pdo->prepare("
            SELECT s.*, 
                   COALESCE(g.score, 0) as current_score,
                   g.id as grade_id
            FROM students s 
            LEFT JOIN grades g ON s.id = g.student_id AND g.subject_id = ? 
                AND g.exam_type = ? AND g.semester = ? AND g.academic_year = ?
            WHERE s.class_id = ? AND s.status = 'active'
            ORDER BY s.full_name
        ");
        $stmt->execute([$subject_id, $exam_type, $semester, $academic_year, $subject['class_id']]);
        $students = $stmt->fetchAll();
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get subjects for dropdown
try {
    $stmt = $pdo->prepare("
        SELECT s.*, c.class_name, c.class_level
        FROM subjects s 
        LEFT JOIN classes c ON s.class_id = c.id 
        WHERE s.teacher_id = ?
        ORDER BY s.subject_name
    ");
    $stmt->execute([$teacher['id']]);
    $teacher_subjects = $stmt->fetchAll();
} catch(PDOException $e) {
    $teacher_subjects = [];
}

$page_title = 'Input Nilai Siswa';
$custom_css = '../../assets/css/admin/dashboard.css';
include '../../includes/header.php';
?>

<?php include '../includes/navbar-teacher.php'; ?>

<div class="container-fluid" style="margin-top: 80px;">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Input Nilai Siswa</h2>
                <a href="manage.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Nilai
                </a>
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

            <!-- Subject Selection -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-book me-2"></i>Pilih Mata Pelajaran
                </div>
                <div class="card-body">
                    <form method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-3">
                                <label for="subject_id" class="form-label">Mata Pelajaran</label>
                                <select class="form-select" id="subject_id" name="subject_id" onchange="this.form.submit()">
                                    <option value="">Pilih Mata Pelajaran</option>
                                    <?php foreach($teacher_subjects as $subj): ?>
                                        <option value="<?= $subj['id'] ?>" <?= $subject_id == $subj['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($subj['subject_name'] . ' - ' . $subj['class_level'] . ' ' . $subj['class_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="exam_type" class="form-label">Jenis Ujian</label>
                                <select class="form-select" id="exam_type" name="exam_type" onchange="this.form.submit()">
                                    <option value="UTS" <?= ($_GET['exam_type'] ?? 'UTS') == 'UTS' ? 'selected' : '' ?>>UTS</option>
                                    <option value="UAS" <?= ($_GET['exam_type'] ?? '') == 'UAS' ? 'selected' : '' ?>>UAS</option>
                                    <option value="Quiz" <?= ($_GET['exam_type'] ?? '') == 'Quiz' ? 'selected' : '' ?>>Quiz</option>
                                    <option value="Tugas" <?= ($_GET['exam_type'] ?? '') == 'Tugas' ? 'selected' : '' ?>>Tugas</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select class="form-select" id="semester" name="semester" onchange="this.form.submit()">
                                    <option value="Ganjil" <?= ($_GET['semester'] ?? CURRENT_SEMESTER) == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                    <option value="Genap" <?= ($_GET['semester'] ?? '') == 'Genap' ? 'selected' : '' ?>>Genap</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Grade Input Form -->
            <?php if ($subject_id && !empty($students)): ?>
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-edit me-2"></i>
                    Input Nilai - <?= htmlspecialchars($subject['subject_name']) ?> 
                    (<?= htmlspecialchars($subject['class_level'] . ' ' . $subject['class_name']) ?>)
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="batch_input" value="1">
                        
                        <!-- Exam Info -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="exam_type_input" class="form-label">Jenis Ujian</label>
                                <select class="form-select" name="exam_type" required>
                                    <option value="UTS" <?= ($_GET['exam_type'] ?? 'UTS') == 'UTS' ? 'selected' : '' ?>>UTS</option>
                                    <option value="UAS" <?= ($_GET['exam_type'] ?? '') == 'UAS' ? 'selected' : '' ?>>UAS</option>
                                    <option value="Quiz" <?= ($_GET['exam_type'] ?? '') == 'Quiz' ? 'selected' : '' ?>>Quiz</option>
                                    <option value="Tugas" <?= ($_GET['exam_type'] ?? '') == 'Tugas' ? 'selected' : '' ?>>Tugas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="max_score" class="form-label">Nilai Maksimal</label>
                                <input type="number" class="form-control" name="max_score" value="100" min="1" max="100" required>
                            </div>
                            <div class="col-md-3">
                                <label for="exam_date" class="form-label">Tanggal Ujian</label>
                                <input type="date" class="form-control" name="exam_date" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="semester_input" class="form-label">Semester</label>
                                <select class="form-select" name="semester" required>
                                    <option value="Ganjil" <?= ($_GET['semester'] ?? CURRENT_SEMESTER) == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                    <option value="Genap" <?= ($_GET['semester'] ?? '') == 'Genap' ? 'selected' : '' ?>>Genap</option>
                                </select>
                            </div>
                        </div>
                        
                        <input type="hidden" name="academic_year" value="<?= $_GET['academic_year'] ?? CURRENT_ACADEMIC_YEAR ?>">
                        
                        <!-- Students Grade Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Siswa</th>
                                        <th>Nama Siswa</th>
                                        <th>Nilai Saat Ini</th>
                                        <th>Input Nilai Baru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($students as $index => $student): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                        <td>
                                            <?php if ($student['current_score'] > 0): ?>
                                                <span class="badge bg-info"><?= $student['current_score'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Belum ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="grades[<?= $student['id'] ?>]" 
                                                   value="<?= $student['current_score'] > 0 ? $student['current_score'] : '' ?>"
                                                   min="0" 
                                                   max="100" 
                                                   step="0.1"
                                                   placeholder="0-100">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary" onclick="fillAllGrades()">
                                <i class="fas fa-fill me-1"></i>Isi Semua dengan Nilai Sama
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Simpan Semua Nilai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php elseif ($subject_id): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5>Tidak Ada Siswa</h5>
                    <p class="text-muted">Belum ada siswa di kelas untuk mata pelajaran ini</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function fillAllGrades() {
    const score = prompt('Masukkan nilai yang akan diterapkan untuk semua siswa:');
    if (score !== null && !isNaN(score) && score >= 0 && score <= 100) {
        const inputs = document.querySelectorAll('input[name^="grades["]');
        inputs.forEach(input => {
            input.value = score;
        });
    }
}
</script>

<?php 
$custom_js = '../../assets/js/admin/dashboard.js';
include '../../includes/footer.php'; 
?>