<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petani') {
    header("Location: login.php");
    exit;
}

$petani_id = $_SESSION['id_user'];
$query_restock = mysqli_query($koneksi, "
    SELECT r.*, p.nama_produk 
    FROM restock r
    JOIN produk p ON r.produk_id = p.id
    WHERE r.petani_id = $petani_id AND r.status_kirim = 'Sudah Dibayar'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Mitra Produsen - Agro Kates Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .navbar-premium { background: linear-gradient(135deg, #0f5132 0%, #16a34a 100%); padding: 15px 0; }
        .card-panel { background: #ffffff; border: none; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 25px; }
        .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-premium thead th { background: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; padding: 15px; border-bottom: 2px solid #e2e8f0; }
        .table-premium tbody td { padding: 16px 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .btn-action { border-radius: 10px; font-weight: 600; padding: 8px 16px; transition: all 0.2s; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-premium shadow-sm mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold fs-5" href="#"><i class="fa-solid fa-wheat-awn me-2"></i>Portal Manajemen Produsen</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-4 opacity-75"><i class="fa-regular fa-user me-2"></i><?= $_SESSION['nama']; ?></span>
                <a href="logout.php" class="btn btn-light btn-sm rounded-pill fw-bold px-4 text-success">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="card-panel">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-clipboard-list text-success me-2"></i>Instruksi Pengadaan Bibit (Lunas)</h5>
            <div class="table-responsive">
                <table class="table-premium w-100">
                    <thead>
                        <tr>
                            <th>Kode Pengadaan</th>
                            <th>Varietas Komoditas</th>
                            <th>Volume Muatan</th>
                            <th class="text-center">Tindakan Operasional</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($query_restock) == 0) { ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted"><i class="fa-solid fa-inbox fs-2 d-block mb-2"></i>Tidak ada antrean pengadaan baru saat ini.</td></tr>
                        <?php } ?>
                        <?php while($row = mysqli_fetch_assoc($query_restock)) { ?>
                            <tr>
                                <td class="text-muted fw-medium">#RSC-<?= $row['id_restock']; ?></td>
                                <td class="text-dark fw-semibold"><?= $row['nama_produk']; ?></td>
                                <td><span class="badge bg-light text-dark border px-3 py-2 fs-6"><?= $row['jumlah_beli']; ?> Kg</span></td>
                                <td class="text-center">
                                    <form action="proses_petani.php" method="POST" class="m-0">
                                        <input type="hidden" name="id_restock" value="<?= $row['id_restock']; ?>">
                                        <button type="submit" class="btn btn-warning btn-action text-dark">
                                            <i class="fa-solid fa-truck-ramp-box me-1"></i>Serahkan ke Logistik
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>