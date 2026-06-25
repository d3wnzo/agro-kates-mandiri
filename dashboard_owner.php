<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}

$owner_id = $_SESSION['id_user'];
$query_owner = mysqli_query($koneksi, "SELECT saldo FROM users WHERE id = $owner_id");
$data_owner = mysqli_fetch_assoc($query_owner);

$query_monitoring = mysqli_query($koneksi, "
    SELECT p.*, u.nama AS nama_konsumen, k.nama AS nama_kurir
    FROM pesanan p
    JOIN users u ON p.konsumen_id = u.id
    LEFT JOIN users k ON p.kurir_id = k.id
    ORDER BY p.id_pesanan DESC
");

$query_alert_stok = mysqli_query($koneksi, "SELECT nama_produk, stok FROM produk WHERE stok <= 20");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Eksekutif - Agro Kates Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        
        .navbar-executive { background-color: #0f172a !important; padding: 15px 0; }
        .card-panel { background: #ffffff; border: none; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 25px; }
        
        .metric-card { background: linear-gradient(135deg, #0f5132 0%, #16a34a 100%); color: white; border-radius: 20px; border: none; overflow: hidden; position: relative; }
        .metric-card::after { content: ''; position: absolute; right: -20px; top: -20px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; }
        
        .alert-premium { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 16px; border-left: 6px solid #e11d48; }
        
        .form-control, .form-select { border-radius: 10px; border-color: #e2e8f0; background: #f8fafc; padding: 12px; }
        .btn-submit { background: #0f172a; color: white; border-radius: 10px; font-weight: 600; }
        .btn-submit:hover { background: #1e293b; color: white; }
        
        .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-premium thead th { background: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; padding: 15px; border-bottom: 2px solid #e2e8f0; }
        .table-premium tbody td { padding: 16px 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-executive shadow-sm mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="#"><i class="fa-solid fa-chart-pie text-success me-2"></i>Dashboard Direksi</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-4 opacity-75"><i class="fa-regular fa-user me-2"></i><?= $_SESSION['nama']; ?></span>
                <a href="logout.php" class="btn btn-light btn-sm rounded-pill fw-bold px-4 text-dark">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <!-- ALERT STOK -->
        <?php if (mysqli_num_rows($query_alert_stok) > 0) { ?>
            <div class="alert alert-premium p-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-danger mb-3"><i class="fa-solid fa-bell me-2"></i>Peringatan Persediaan Kritis</h5>
                <div class="d-flex flex-wrap gap-3">
                    <?php while ($produk_kritis = mysqli_fetch_assoc($query_alert_stok)) { ?>
                        <div class="bg-white px-4 py-2 rounded-pill border shadow-sm text-dark fw-medium">
                            <?= $produk_kritis['nama_produk']; ?> 
                            <span class="badge bg-danger ms-2 rounded-pill"><?= $produk_kritis['stok']; ?> Pcs</span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <div class="row mb-5">
            <div class="col-lg-5">
                <div class="metric-card p-4 shadow">
                    <h6 class="fw-semibold opacity-75 mb-1">Saldo Perusahaan</h6>
                    <h1 class="fw-bold mb-3 display-5">Rp <?= number_format($data_owner['saldo'], 0, ',', '.'); ?></h1>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- TABEL GUDANG -->
            <div class="col-lg-4">
                <div class="card-panel h-100">
                    <h5 class="fw-bold text-dark mb-4">Inventori Gudang</h5>
                    <div class="table-responsive">
                        <table class="table-premium w-100">
                            <thead><tr><th>Varietas</th><th class="text-end">Volume</th></tr></thead>
                            <tbody>
                                <?php 
                                $q_stok_gudang = mysqli_query($koneksi, "SELECT * FROM produk");
                                while($st = mysqli_fetch_assoc($q_stok_gudang)) {
                                    $color = ($st['stok'] <= 20) ? 'text-danger fw-bold' : 'text-success fw-bold';
                                ?>
                                    <tr>
                                        <td class="text-dark fw-medium"><?= $st['nama_produk']; ?></td>
                                        <td class="text-end <?= $color; ?>"><?= $st['stok']; ?> Pcs</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TABEL RETAIL -->
            <div class="col-lg-8">
                <div class="card-panel h-100">
                    <h5 class="fw-bold text-dark mb-4">Pemantauan Penjualan Retail</h5>
                    <div class="table-responsive">
                        <table class="table-premium w-100">
                            <thead><tr><th>Invoice</th><th>Konsumen</th><th>Pendapatan</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($query_monitoring)) { ?>
                                    <tr>
                                        <td class="text-muted fw-medium">#INV-<?= $row['id_pesanan']; ?></td>
                                        <td class="text-dark fw-semibold"><?= $row['nama_konsumen']; ?></td>
                                        <td class="text-success fw-bold">Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php 
                                            if ($row['status_pesanan'] == 'Selesai') echo "<span class='badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill'>Selesai</span>";
                                            elseif ($row['status_pesanan'] == 'Belum Bayar') echo "<span class='badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill'>Belum Bayar</span>";
                                            else echo "<span class='badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill'>Proses Logistik</span>";
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM RESTOCK: DIBIKIN EASY GOING -->
        <div class="card-panel mb-5">
            <h5 class="fw-bold text-dark mb-4">Pengadaan Restock Komoditas (Auto-Assign)</h5>
            <form action="proses_restock_owner.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Varietas Bibit</label>
                        <select class="form-select" name="produk_id" required>
                            <option value="">Pilih Varietas</option>
                            <?php 
                            $q_prod = mysqli_query($koneksi, "SELECT * FROM produk");
                            while($p = mysqli_fetch_assoc($q_prod)) { echo "<option value='".$p['id']."'>".$p['nama_produk']."</option>"; }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">Volume (Kg)</label>
                        <input type="number" name="jumlah_kg" class="form-control" min="1" placeholder="0" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-submit w-100 py-2"></i>Order</button>
                    </div>
                </div>
            </form>
            
            <div class="table-responsive mt-5">
                <table class="table-premium w-100">
                    <thead><tr><th>Kode</th><th>Komoditas</th><th>Volume</th><th>Biaya</th><th>Status Distribusi</th></tr></thead>
                    <tbody>
                        <?php 
                        $q_res = mysqli_query($koneksi, "
                            SELECT r.*, p.nama_produk, u1.nama AS nama_petani, u2.nama AS nama_kurir 
                            FROM restock r
                            JOIN produk p ON r.produk_id = p.id
                            JOIN users u1 ON r.petani_id = u1.id
                            JOIN users u2 ON r.pengirim_id = u2.id
                            ORDER BY r.id_restock DESC
                        ");
                        while($res = mysqli_fetch_assoc($q_res)) {
                            $tagihan = $res['jumlah_beli'] * 1000000;
                        ?>
                            <tr>
                                <td class="text-muted fw-medium">#RSC-<?= $res['id_restock']; ?></td>
                                <td class="text-dark fw-semibold"><?= $res['nama_produk']; ?></td>
                                <td><span class="badge bg-light text-dark border px-3 py-2"><?= $res['jumlah_beli']; ?> Kg</span></td>
                                <td class="text-dark fw-bold">Rp <?= number_format($tagihan, 0, ',', '.'); ?></td>
                                <td>
                                    <?php 
                                    if($res['status_kirim'] == 'Diterima') echo "<span class='badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill'>Stok Masuk Gudang</span>";
                                    else echo "<span class='badge bg-warning bg-opacity-10 text-warning-emphasis px-3 py-2 rounded-pill'>Proses Logistik</span>";
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>