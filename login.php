<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] === 'pemilik') { header("Location: dashboard_owner.php"); exit; }
    elseif ($_SESSION['role'] === 'karyawan') { header("Location: dashboard_karyawan.php"); exit; }
    elseif ($_SESSION['role'] === 'petani') { header("Location: dashboard_petani.php"); exit; }
    elseif ($_SESSION['role'] === 'pengirim_bibit') { header("Location: dashboard_pengirim_bibit.php"); exit; }
    elseif ($_SESSION['role'] === 'kurir_ekspedisi') { header("Location: dashboard_kurir_ekspedisi.php"); exit; }
    else { header("Location: dashboard_konsumen.php"); exit; }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Masuk - Agro Kates Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f5132 0%, #198754 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-container {
            background-color: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 480px;
        }
        .auth-header {
            padding: 40px 30px 20px;
            text-align: center;
        }
        .auth-body {
            padding: 0 30px 40px;
        }
        .nav-pills {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 5px;
            margin-bottom: 25px;
        }
        .nav-pills .nav-link {
            color: #64748b;
            font-weight: 500;
            border-radius: 8px;
            padding: 10px 0;
        }
        .nav-pills .nav-link.active {
            background-color: #ffffff;
            color: #0f5132;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            font-weight: 600;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1);
            background-color: #ffffff;
        }
        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: none;
            color: #94a3b8;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .form-control { border-left: none; }
        .btn-auth {
            background: linear-gradient(to right, #0f5132, #198754);
            color: #ffffff;
            font-weight: 600;
            padding: 14px;
            border-radius: 12px;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            border: none;
        }
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(25, 135, 84, 0.3);
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-header">
            <div class="mb-3 d-inline-block p-3 rounded-circle" style="background: #ecfdf5;">
                <i class="fa-solid fa-leaf fs-2 text-success"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Agro Kates Mandiri</h3>
            <p class="text-muted small">Sistem Perdagangan Komoditas Unggulan</p>
        </div>

        <div class="auth-body">
            <ul class="nav nav-pills nav-justified" id="authTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login-panel" type="button"><i class="fa-solid fa-right-to-bracket me-2"></i>Masuk</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#register-panel" type="button"><i class="fa-solid fa-user-plus me-2"></i>Daftar Akun</button>
                </li>
            </ul>

            <div class="tab-content" id="authTabContent">
                <!-- FORM LOGIN -->
                <div class="tab-pane fade show active" id="login-panel">
                    <form action="proses_auth.php" method="POST">
                        <input type="hidden" name="action_type" value="login">
                        
                        <div class="mb-3">
                            <label class="form-label text-dark small fw-semibold">Nama Pengguna (Username)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username Anda" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-dark small fw-semibold">Kata Sandi (Password)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-auth w-100">
                            Masuk ke Sistem <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>

                <!-- FORM REGISTRASI -->
                <div class="tab-pane fade" id="register-panel">
                    <form action="proses_auth.php" method="POST">
                        <input type="hidden" name="action_type" value="register">
                        
                        <div class="mb-3">
                            <label class="form-label text-dark small fw-semibold">Nama Lengkap (Sesuai Identitas)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                                <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Budi Santoso" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark small fw-semibold">Nama Pengguna Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-at"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Gunakan huruf kecil" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-dark small fw-semibold">Kata Sandi Keamanan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-shield-halved"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" minlength="6" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-auth w-100">
                            Konfirmasi Pendaftaran <i class="fa-solid fa-check ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>