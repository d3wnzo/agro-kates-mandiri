<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'kurir_ekspedisi') {
    header("Location: login.php");
    exit;
}

$kurir_id = $_SESSION['id_user'];
$query_kirim = mysqli_query($koneksi, "
    SELECT p.*, u.nama AS nama_konsumen 
    FROM pesanan p
    JOIN users u ON p.konsumen_id = u.id
    WHERE p.kurir_id = $kurir_id AND p.status_pesanan IN ('Sedang Dikirim', 'Tunggu Konfirmasi')
");

$query_return = mysqli_query($koneksi, "
    SELECT p.*, u.nama AS nama_konsumen 
    FROM pesanan p
    JOIN users u ON p.konsumen_id = u.id
    WHERE p.kurir_id = $kurir_id AND p.status_pesanan IN ('Ditolak', 'Return Diambil')
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Distribusi Ekspedisi - Agro Kates Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .navbar-premium { background-color: #1e293b !important; padding: 15px 0; }
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
            <a class="navbar-brand fw-bold fs-5" href="#"><i class="fa-solid fa-motorcycle me-2 text-warning"></i>Distribusi Logistik Hilir</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-4 opacity-75"><i class="fa-regular fa-user me-2"></i><?= $_SESSION['nama']; ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm rounded-pill fw-bold px-4">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <!-- PENGIRIMAN AKTIF -->
        <div class="card-panel mb-5">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-dolly text-secondary me-2"></i>Manifes Pengiriman Reguler</h5>
            <div class="table-responsive">
                <table class="table-premium w-100">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Penerima</th>
                            <th>Alamat Tujuan</th>
                            <th>Status Transit</th>
                            <th class="text-center">Tindakan Ekspedisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_kirim) == 0) { ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fa-solid fa-folder-minus fs-2 d-block mb-2"></i>Manifes kosong hari ini.</td></tr>
                        <?php } ?>
                        <?php while ($kirim = mysqli_fetch_assoc($query_kirim)) { ?>
                            <tr>
                                <td class="text-muted fw-medium">#INV-<?= $kirim['id_pesanan']; ?></td>
                                <td class="text-dark fw-semibold"><?= $kirim['nama_konsumen']; ?></td>
                                <td class="small text-muted" style="max-width: 200px;"><?= htmlspecialchars($kirim['alamat_tujuan']); ?></td>
                                <td>
                                    <?php if ($kirim['status_pesanan'] == 'Sedang Dikirim') { ?>
                                        <span class="badge bg-info bg-opacity-10 text-info-emphasis px-3 py-2 rounded-pill">Dalam Perjalanan</span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Tunggu Konfirmasi</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-2 justify-content-center align-items-center">
                                        <?php if ($kirim['status_pesanan'] == 'Sedang Dikirim') { ?>
                                            <form action="proses_kurir_ekspedisi.php" method="POST" class="w-100 m-0">
                                                <input type="hidden" name="id_pesanan" value="<?= $kirim['id_pesanan']; ?>">
                                                <input type="hidden" name="aksi" value="selesai_kirim">
                                                <button type="submit" class="btn btn-success btn-action w-100"><i class="fa-solid fa-check me-1"></i>Selesai Kirim</button>
                                            </form>
                                        <?php } else { ?>
                                            <span class="text-muted small fw-semibold"><i class="fa-solid fa-spinner fa-spin me-1"></i>Menunggu Pelanggan</span>
                                        <?php } ?>
                                        <a href="cetak_invoice.php?id=<?= $kirim['id_pesanan']; ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold w-100"><i class="fa-solid fa-print me-1"></i>Cetak Surat Jalan</a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- LOGISTIK RETURN -->
        <div class="card-panel border border-danger-subtle shadow-sm">
            <h5 class="fw-bold text-danger mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i>Instruksi Penjemputan Retur</h5>
            <div class="table-responsive">
                <table class="table-premium w-100">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Keterangan Penolakan</th>
                            <th>Status Penjemputan</th>
                            <th class="text-center">Tindakan Logistik</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_return) == 0) { ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fa-solid fa-shield-heart text-success fs-2 d-block mb-2"></i>Tidak ada komplain atau paket retur.</td></tr>
                        <?php } ?>
                        <?php while ($ret = mysqli_fetch_assoc($query_return)) { ?>
                            <tr>
                                <td class="text-muted fw-medium">#INV-<?= $ret['id_pesanan']; ?></td>
                                <td class="text-dark fw-semibold"><?= $ret['nama_konsumen']; ?></td>
                                <td class="text-danger small fw-medium" style="max-width: 200px;">"<?= htmlspecialchars($ret['alasan_tolak']); ?>"</td>
                                <td>
                                    <?php if ($ret['status_pesanan'] == 'Ditolak') { ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Wajib Ambil</span>
                                    <?php } else { ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Sukses Retur Gudang</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($ret['status_pesanan'] == 'Ditolak') { ?>
                                        <form action="proses_kurir_ekspedisi.php" method="POST" class="m-0">
                                            <input type="hidden" name="id_pesanan" value="<?= $ret['id_pesanan']; ?>">
                                            <input type="hidden" name="aksi" value="ambil_return">
                                            <button type="submit" class="btn btn-warning btn-action text-dark"><i class="fa-solid fa-box-open me-1"></i>Ambil Retur</button>
                                        </form>
                                    <?php } else { ?>
                                        <span class="text-success small fw-semibold"><i class="fa-solid fa-check-double me-1"></i>Selesai</span>
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