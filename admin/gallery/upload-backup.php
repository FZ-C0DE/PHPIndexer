<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = $_POST['category'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/gallery/';
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_size = $_FILES['image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validate file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_types)) {
            $error = 'Format file tidak didukung. Gunakan JPG, PNG, atau GIF.';
        } elseif ($file_size > MAX_FILE_SIZE) {
            $error = 'Ukuran file terlalu besar. Maksimal 5MB.';
        } else {
            // Generate unique filename
            $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_url = '../uploads/gallery/' . $new_filename;
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO gallery (title, description, image_url, category, is_featured) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $description, $image_url, $category, $is_featured]);
                    $success = 'Foto berhasil diupload ke galeri';
                } catch(PDOException $e) {
                    unlink($upload_path); // Delete uploaded file if database insert fails
                    $error = 'Gagal menyimpan data ke database: ' . $e->getMessage();
                }
            } else {
                $error = 'Gagal mengupload file';
            }
        }
    } else {
        $error = 'Pilih file foto yang akan diupload';
    }
}

$page_title = 'Upload Galeri';
$custom_css = '../../assets/css/admin/dashboard.css';
include '../../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Upload Foto Galeri
                </h2>
                <p class="mb-0 opacity-75">Upload foto untuk galeri sekolah</p>
            </div>
            <a href="manage.php" class="btn btn-light btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Galeri
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
                    <div class="mt-2">
                        <a href="manage.php" class="btn btn-sm btn-success">Lihat Galeri</a>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="location.reload()">Upload Lagi</button>
                    </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
                <div class="card-header">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Form Upload Foto
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Foto *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Kategori *</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="general">Umum</option>
                                            <option value="activities">Kegiatan</option>
                                            <option value="facilities">Fasilitas</option>
                                            <option value="achievements">Prestasi</option>
                                            <option value="events">Acara</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                            <label class="form-check-label" for="is_featured">
                                                <i class="fas fa-star text-warning me-1"></i>Jadikan Foto Unggulan
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">File Foto *</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                    <div class="form-text">
                                        <small>
                                            • Format: JPG, PNG, GIF<br>
                                            • Maksimal: 5MB<br>
                                            • Resolusi disarankan: 800x600px
                                        </small>
                                    </div>
                                </div>
                                
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <img id="preview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="manage.php" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cloud-upload-alt me-1"></i>Upload Foto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
});
</script>

<?php 
$custom_js = '../../assets/js/admin/dashboard.js';
include '../../includes/footer.php'; 
?>