<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

checkLogin();
checkRole('admin');

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_name = sanitize($_POST['school_name']);
    $vision = sanitize($_POST['vision']);
    $mission = sanitize($_POST['mission']);
    $history = sanitize($_POST['history']);
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $website = sanitize($_POST['website']);
    $principal_name = sanitize($_POST['principal_name']);
    $logo_url = sanitize($_POST['logo_url']);
    
    try {
        // Check if profile exists
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM school_profile");
        $exists = $stmt->fetch()['count'] > 0;
        
        if ($exists) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE school_profile SET school_name = ?, vision = ?, mission = ?, history = ?, address = ?, phone = ?, email = ?, website = ?, principal_name = ?, logo_url = ?, updated_at = CURRENT_TIMESTAMP");
            $stmt->execute([$school_name, $vision, $mission, $history, $address, $phone, $email, $website, $principal_name, $logo_url]);
        } else {
            // Insert new profile
            $stmt = $pdo->prepare("INSERT INTO school_profile (school_name, vision, mission, history, address, phone, email, website, principal_name, logo_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$school_name, $vision, $mission, $history, $address, $phone, $email, $website, $principal_name, $logo_url]);
        }
        
        $success = 'Profil sekolah berhasil disimpan';
    } catch(PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

// Get current profile
try {
    $stmt = $pdo->query("SELECT * FROM school_profile LIMIT 1");
    $profile = $stmt->fetch();
} catch(PDOException $e) {
    $profile = null;
    $error = 'Gagal mengambil data profil sekolah';
}

$page_title = 'Profil Sekolah';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Profil Sekolah</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../index.php" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i>Lihat Website
                    </a>
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

            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="school_name" class="form-label">Nama Sekolah *</label>
                                <input type="text" class="form-control" id="school_name" name="school_name" 
                                       value="<?= htmlspecialchars($profile['school_name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="logo_url" class="form-label">URL Logo</label>
                                <input type="url" class="form-control" id="logo_url" name="logo_url" 
                                       value="<?= htmlspecialchars($profile['logo_url'] ?? '') ?>" 
                                       placeholder="https://example.com/logo.png">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="principal_name" class="form-label">Nama Kepala Sekolah</label>
                                <input type="text" class="form-control" id="principal_name" name="principal_name" 
                                       value="<?= htmlspecialchars($profile['principal_name'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?= htmlspecialchars($profile['website'] ?? '') ?>" 
                                       placeholder="https://sekolah.sch.id">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($profile['email'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="vision" class="form-label">Visi</label>
                            <textarea class="form-control" id="vision" name="vision" rows="3"><?= htmlspecialchars($profile['vision'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mission" class="form-label">Misi</label>
                            <textarea class="form-control" id="mission" name="mission" rows="4"><?= htmlspecialchars($profile['mission'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="history" class="form-label">Sejarah</label>
                            <textarea class="form-control" id="history" name="history" rows="5"><?= htmlspecialchars($profile['history'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>