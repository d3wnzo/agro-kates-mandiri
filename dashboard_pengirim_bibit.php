<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pengirim_bibit') {
    header("Location: login.php");
    exit;
}

$pengirim_id = $_SESSION['id_user'];
$query_tugas = mysqli_query($koneksi, "
    SELECT r.*, p.nama_produk, u.nama AS nama_petani
    FROM restock r
    JOIN produk p ON r.produk_id = p.id
    JOIN users u ON r.petani_id = u.id
    WHERE r.pengirim_id = $pengirim_id AND r.status_kirim IN ('Dalam Perjalanan', 'Diterima')
    ORDER BY r.id_restock DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Logistik Hulu - Agro Kates Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .navbar-premium { background-color: #0f4c5c !important; padding: 15px 0; }
        .card-panel { background: #ffffff; border: none; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 25px; }
        .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-premium thead th { background: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; padding: 15px; border-bottom: 2px solid #e2e8f0; }
        .table-premium tbody td { padding: 16px 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .btn-action { background-color: #0f172a; color: white; border-radius: 10px; font-weight: 600; padding: 8px 16px; transition: all 0.2s; }
        .btn-action:hover { background-color: #1e293b; color: white; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-premium shadow-sm mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold fs-5" href="#"><i class="fa-solid fa-truck-moving me-2 text-info"></i>Ekspedisi Rantai Pasok (Hulu)</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-4 opacity-75"><i class="fa-solid fa-id-card me-2"></i><?= $_SESSION['nama']; ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm rounded-pill fw-bold px-4">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="card-panel">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-route text-secondary me-2"></i>Manifes Penjemputan Suplai</h5>
            <div class="table-responsive">
                <table class="table-premium w-100">
                    <thead>
                        <tr>
                            <th>Kode Manifes</th>
                            <th>Mitra Produsen</th>
                            <th>Varietas Komoditas</th>
                            <th>Volume Grosir</th>
                            <th>Status Transit</th>
                            <th class="text-center">Konfirmasi Gudang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_tugas) == 0) { ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fa-solid fa-calendar-check fs-2 d-block mb-2"></i>Tidak ada jadwal transit hari ini.</td></tr>
                        <?php } ?>
                        <?php while ($row = mysqli_fetch_assoc($query_tugas)) { ?>
                            <tr>
                                <td class="text-muted fw-medium">#RSC-<?= $row['id_restock']; ?></td>
                                <td class="text-dark fw-semibold"><?= $row['nama_petani']; ?></td>
                                <td class="text-primary fw-medium"><?= $row['nama_produk']; ?></td>
                                <td><span class="badge bg-light text-dark border px-3 py-2"><?= $row['jumlah_beli']; ?> Kg</span></td>
                                <td>
                                    <?php if ($row['status_kirim'] == 'Dalam Perjalanan') { ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis px-3 py-2 rounded-pill"><i class="fa-solid fa-spinner fa-spin me-1"></i>Diangkut</span>
                                    <?php } else { ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="fa-solid fa-check me-1"></i>Selesai Bongkar</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status_kirim'] == 'Dalam Perjalanan') { ?>
                                        <form action="proses_kurir_hulu.php" method="POST" class="m-0">
                                            <input type="hidden" name="id_restock" value="<?= $row['id_restock']; ?>">
                                            <button type="submit" class="btn btn-action"><i class="fa-solid fa-boxes-packing me-1"></i>Selesai Bongkar</button>
                                        </form>
                                    <?php } else { ?>
                                        <span class="text-success small fw-semibold"><i class="fa-solid fa-check-double me-1"></i>Terkonversi</span>
                                    <?php } ?>
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