
<?php
require_once 'config/constants.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/public/style.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-6 text-center">
                <div class="card shadow-lg">
                    <div class="card-body py-5">
                        <i class="fas fa-ban fa-5x text-danger mb-4"></i>
                        <h1 class="display-4 mb-3">403</h1>
                        <h2 class="h4 mb-3">Akses Ditolak</h2>
                        <p class="text-muted mb-4">
                            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
                            Silakan login dengan akun yang tepat.
                        </p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="index.php" class="btn btn-primary me-md-2">
                                <i class="fas fa-home me-2"></i>Kembali ke Beranda
                            </a>
                            <a href="auth/login.php" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
