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
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $image_url = sanitize($_POST['image_url']);
        $category = sanitize($_POST['category']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO gallery (title, description, image_url, category, is_featured) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $image_url, $category, $is_featured]);
                $success = 'Foto berhasil ditambahkan ke galeri';
            } else {
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE gallery SET title = ?, description = ?, image_url = ?, category = ?, is_featured = ? WHERE id = ?");
                $stmt->execute([$title, $description, $image_url, $category, $is_featured, $id]);
                $success = 'Foto galeri berhasil diperbarui';
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
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $success = 'Foto berhasil dihapus dari galeri';
        $action = 'list';
    } catch(PDOException $e) {
        $error = 'Gagal menghapus foto: ' . $e->getMessage();
    }
}

// Get gallery items
if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM gallery ORDER BY is_featured DESC, created_at DESC");
        $gallery_items = $stmt->fetchAll();
    } catch(PDOException $e) {
        $gallery_items = [];
        $error = 'Gagal mengambil data galeri';
    }
}

// Get gallery item for edit
if ($action === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $gallery_item = $stmt->fetch();
        
        if (!$gallery_item) {
            $error = 'Foto tidak ditemukan';
            $action = 'list';
        }
    } catch(PDOException $e) {
        $error = 'Gagal mengambil data foto';
        $action = 'list';
    }
}

$page_title = 'Galeri';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($action === 'add'): ?>
                        Tambah Foto Galeri
                    <?php elseif ($action === 'edit'): ?>
                        Edit Foto Galeri
                    <?php else: ?>
                        Galeri
                    <?php endif; ?>
                </h1>
                
                <?php if ($action === 'list'): ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Foto
                    </a>
                </div>
                <?php else: ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="gallery.php" class="btn btn-outline-secondary">
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
                <!-- Gallery List -->
                <div class="row">
                    <?php if (empty($gallery_items)): ?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                    <h5>Belum ada foto di galeri</h5>
                                    <p class="text-muted">Mulai dengan menambahkan foto pertama</p>
                                    <a href="?action=add" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Tambah Foto
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach($gallery_items as $item): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card gallery-item">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($item['title']) ?>" 
                                     class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title d-flex justify-content-between">
                                        <?= htmlspecialchars($item['title']) ?>
                                        <?php if ($item['is_featured']): ?>
                                            <span class="badge bg-primary">Featured</span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="card-text"><?= htmlspecialchars($item['description']) ?></p>
                                    <small class="text-muted">
                                        Kategori: <?= htmlspecialchars($item['category']) ?><br>
                                        Ditambahkan: <?= date('d/m/Y', strtotime($item['created_at'])) ?>
                                    </small>
                                    <div class="mt-3">
                                        <a href="?action=edit&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?action=delete&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            
            <?php else: ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <?php if ($action === 'edit'): ?>
                                <input type="hidden" name="id" value="<?= $gallery_item['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="title" class="form-label">Judul Foto *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= htmlspecialchars($gallery_item['title'] ?? '') ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="category" class="form-label">Kategori</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="general" <?= ($gallery_item['category'] ?? 'general') == 'general' ? 'selected' : '' ?>>Umum</option>
                                        <option value="activities" <?= ($gallery_item['category'] ?? '') == 'activities' ? 'selected' : '' ?>>Kegiatan</option>
                                        <option value="facilities" <?= ($gallery_item['category'] ?? '') == 'facilities' ? 'selected' : '' ?>>Fasilitas</option>
                                        <option value="achievements" <?= ($gallery_item['category'] ?? '') == 'achievements' ? 'selected' : '' ?>>Prestasi</option>
                                        <option value="events" <?= ($gallery_item['category'] ?? '') == 'events' ? 'selected' : '' ?>>Acara</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image_url" class="form-label">URL Gambar *</label>
                                <input type="url" class="form-control" id="image_url" name="image_url" 
                                       value="<?= htmlspecialchars($gallery_item['image_url'] ?? '') ?>" 
                                       placeholder="https://example.com/image.jpg" required>
                                <div class="form-text">Masukkan URL gambar yang valid. Pastikan gambar dapat diakses secara publik.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($gallery_item['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                           <?= ($gallery_item['is_featured'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_featured">
                                        Tampilkan di halaman utama (Featured)
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="gallery.php" class="btn btn-outline-secondary">Batal</a>
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