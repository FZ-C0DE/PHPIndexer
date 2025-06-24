<?php
require_once '../../includes/auth-check.php';
require_once '../../config/database.php';

requireRole('admin');

$error = '';
$success = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        // Get image info first
        $stmt = $pdo->prepare("SELECT image_url FROM gallery WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $image = $stmt->fetch();
        
        if ($image) {
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            // Delete physical file
            $image_path = '../../' . $image['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            
            $success = 'Foto berhasil dihapus';
        }
    } catch(PDOException $e) {
        $error = 'Gagal menghapus foto: ' . $e->getMessage();
    }
}

// Handle featured toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle_featured' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE gallery SET is_featured = NOT is_featured WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Status unggulan berhasil diubah';
    } catch(PDOException $e) {
        $error = 'Gagal mengubah status: ' . $e->getMessage();
    }
}

// Get gallery data
$category_filter = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

try {
    $sql = "SELECT * FROM gallery WHERE 1=1";
    $params = [];
    
    if ($category_filter) {
        $sql .= " AND category = ?";
        $params[] = $category_filter;
    }
    
    if ($search) {
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $photos = $stmt->fetchAll();
} catch(PDOException $e) {
    $photos = [];
    $error = 'Gagal mengambil data galeri';
}

$page_title = 'Kelola Galeri';
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

.photo-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s;
    height: 100%;
}

.photo-card:hover {
    transform: translateY(-2px);
}

.photo-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.photo-actions {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    opacity: 0;
    transition: opacity 0.2s;
}

.photo-card:hover .photo-actions {
    opacity: 1;
}

.featured-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    background: linear-gradient(45deg, #ffc107, #ff6b35);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: bold;
}

.category-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 1rem;
}

.stats-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #e83e8c;
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
}
</style>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-images me-2"></i>Kelola Galeri
                </h2>
                <p class="mb-0 opacity-75">Kelola foto dan gambar sekolah</p>
            </div>
            <a href="upload.php" class="btn btn-light btn-lg">
                <i class="fas fa-cloud-upload-alt me-2"></i>Upload Foto
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
                        <i class="fas fa-images fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count($photos) ?></h3>
                        <small class="text-muted">Total Foto</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-star fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($photos, fn($p) => $p['is_featured'])) ?></h3>
                        <small class="text-muted">Foto Unggulan</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-folder fa-2x text-info"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_unique(array_column($photos, 'category'))) ?></h3>
                        <small class="text-muted">Kategori</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-calendar fa-2x text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-0"><?= count(array_filter($photos, fn($p) => date('Y-m-d', strtotime($p['created_at'])) === date('Y-m-d'))) ?></h3>
                        <small class="text-muted">Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row align-items-center">
                <div class="col-md-3">
                    <select class="form-select" name="category" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <option value="general" <?= $category_filter === 'general' ? 'selected' : '' ?>>Umum</option>
                        <option value="activities" <?= $category_filter === 'activities' ? 'selected' : '' ?>>Kegiatan</option>
                        <option value="facilities" <?= $category_filter === 'facilities' ? 'selected' : '' ?>>Fasilitas</option>
                        <option value="achievements" <?= $category_filter === 'achievements' ? 'selected' : '' ?>>Prestasi</option>
                        <option value="events" <?= $category_filter === 'events' ? 'selected' : '' ?>>Acara</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search" 
                           value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul atau deskripsi foto...">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Cari
                    </button>
                    <a href="manage.php" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Photo Grid -->
    <?php if (empty($photos)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <h5>Belum Ada Foto</h5>
                <p class="text-muted">Belum ada foto yang diupload ke galeri</p>
                <a href="upload.php" class="btn btn-primary">
                    <i class="fas fa-cloud-upload-alt me-1"></i>Upload Foto Pertama
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($photos as $photo): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="photo-card position-relative">
                    <?php if ($photo['is_featured']): ?>
                        <div class="featured-badge">
                            <i class="fas fa-star me-1"></i>Unggulan
                        </div>
                    <?php endif; ?>
                    
                    <div class="photo-actions">
                        <div class="btn-group-vertical btn-group-sm">
                            <a href="?action=toggle_featured&id=<?= $photo['id'] ?>" 
                               class="btn btn-<?= $photo['is_featured'] ? 'warning' : 'outline-warning' ?>" 
                               title="<?= $photo['is_featured'] ? 'Hapus dari unggulan' : 'Jadikan unggulan' ?>">
                                <i class="fas fa-star"></i>
                            </a>
                            <a href="edit.php?id=<?= $photo['id'] ?>" 
                               class="btn btn-outline-primary" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?action=delete&id=<?= $photo['id'] ?>" 
                               class="btn btn-outline-danger" 
                               title="Hapus"
                               onclick="return confirm('Yakin ingin menghapus foto ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    
                    <img src="<?= htmlspecialchars($photo['image_url']) ?>" 
                         alt="<?= htmlspecialchars($photo['title']) ?>" 
                         class="photo-img"
                         data-bs-toggle="modal" 
                         data-bs-target="#photoModal"
                         data-src="<?= htmlspecialchars($photo['image_url']) ?>"
                         data-title="<?= htmlspecialchars($photo['title']) ?>"
                         data-description="<?= htmlspecialchars($photo['description']) ?>"
                         style="cursor: pointer;">
                    
                    <div class="card-body">
                        <h6 class="card-title"><?= htmlspecialchars($photo['title']) ?></h6>
                        <p class="card-text text-muted small">
                            <?= htmlspecialchars(substr($photo['description'], 0, 80)) ?>
                            <?= strlen($photo['description']) > 80 ? '...' : '' ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="category-badge badge bg-secondary">
                                <?= ucfirst($photo['category']) ?>
                            </span>
                            <small class="text-muted">
                                <?= date('d/m/Y', strtotime($photo['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination if needed -->
        <div class="d-flex justify-content-center mt-4">
            <p class="text-muted">Menampilkan <?= count($photos) ?> foto</p>
        </div>
    <?php endif; ?>
</div>

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="photoModalImg" src="" alt="" class="img-fluid rounded">
                <p id="photoModalDescription" class="mt-3 text-muted"></p>
            </div>
        </div>
    </div>
</div>

<script>
// Photo modal functionality
$('img[data-bs-toggle="modal"]').on('click', function() {
    const src = $(this).data('src');
    const title = $(this).data('title');
    const description = $(this).data('description');
    
    $('#photoModalImg').attr('src', src);
    $('#photoModalTitle').text(title);
    $('#photoModalDescription').text(description);
});
</script>

<?php include '../../includes/footer.php'; ?>