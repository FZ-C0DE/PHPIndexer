<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $upload_dir = '../../uploads/gallery/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = $_POST['category'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $uploaded_files = [];
    $errors = [];
    
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['images']['name'][$key];
            $file_size = $_FILES['images']['size'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($file_ext, $allowed_types)) {
                $errors[] = "File $file_name: Tipe file tidak diizinkan";
                continue;
            }
            
            // Validate file size (5MB max)
            if ($file_size > 5 * 1024 * 1024) {
                $errors[] = "File $file_name: Ukuran file terlalu besar (max 5MB)";
                continue;
            }
            
            // Generate unique filename
            $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($tmp_name, $upload_path)) {
                $uploaded_files[] = [
                    'filename' => $new_filename,
                    'path' => 'uploads/gallery/' . $new_filename
                ];
            } else {
                $errors[] = "Gagal mengupload file $file_name";
            }
        }
    }
    
    // Save to database
    if (!empty($uploaded_files)) {
        try {
            foreach ($uploaded_files as $file) {
                $stmt = $pdo->prepare("INSERT INTO gallery (title, description, image_url, category, is_featured, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $description, $file['path'], $category, $is_featured]);
            }
            
            $success = count($uploaded_files) . ' foto berhasil diupload';
        } catch(PDOException $e) {
            $error = 'Gagal menyimpan ke database: ' . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    }
}

$page_title = 'Upload Galeri';
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
    background: linear-gradient(135deg, #e83e8c 0%, #fd7e14 100%);
    color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    padding: 3rem;
    text-align: center;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: #e83e8c;
    background-color: #fff;
}

.upload-area.dragover {
    border-color: #e83e8c;
    background-color: #fff5f5;
}

.file-preview {
    display: none;
    margin-top: 1rem;
}

.preview-item {
    display: inline-block;
    margin: 0.5rem;
    position: relative;
}

.preview-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 0.25rem;
    border: 2px solid #dee2e6;
}

.remove-file {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 12px;
    cursor: pointer;
}
</style>

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
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Judul Foto *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
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
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                        <label class="form-check-label" for="is_featured">
                            <i class="fas fa-star text-warning me-1"></i>Jadikan foto unggulan
                        </label>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Upload Foto *</label>
                    <div class="upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5>Drag & Drop foto di sini</h5>
                        <p class="text-muted">atau klik untuk memilih file</p>
                        <p class="small text-muted">Format: JPG, PNG, GIF (Maksimal 5MB per file)</p>
                        <input type="file" id="fileInput" name="images[]" multiple accept="image/*" style="display: none;">
                    </div>
                    
                    <div id="filePreview" class="file-preview"></div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="manage.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>Upload Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    let selectedFiles = [];

    // Click to select files
    uploadArea.addEventListener('click', () => fileInput.click());

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = Array.from(e.dataTransfer.files);
        handleFiles(files);
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleFiles(files);
    });

    function handleFiles(files) {
        files.forEach(file => {
            if (file.type.startsWith('image/')) {
                selectedFiles.push(file);
                createPreview(file);
            }
        });
        
        updateFileInput();
        
        if (selectedFiles.length > 0) {
            filePreview.style.display = 'block';
        }
    }

    function createPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${e.target.result}" class="preview-img" alt="Preview">
                <button type="button" class="remove-file" onclick="removeFile('${file.name}')">×</button>
                <div class="small text-center mt-1">${file.name}</div>
            `;
            filePreview.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    }

    window.removeFile = function(fileName) {
        selectedFiles = selectedFiles.filter(file => file.name !== fileName);
        updateFileInput();
        refreshPreview();
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function refreshPreview() {
        filePreview.innerHTML = '';
        if (selectedFiles.length === 0) {
            filePreview.style.display = 'none';
        } else {
            selectedFiles.forEach(file => createPreview(file));
        }
    }
});
</script>

<?php include '../../includes/footer.php'; ?>