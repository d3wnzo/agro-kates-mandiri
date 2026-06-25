<?php
include 'koneksi.php';

// Menarik data pesanan aktif dari database
$query_pesanan = mysqli_query($koneksi, "
    SELECT p.*, u.nama AS nama_konsumen 
    FROM pesanan p 
    JOIN users u ON p.konsumen_id = u.id 
    WHERE p.status_pesanan IN ('Dikemas', 'Sedang Dikirim', 'Tunggu Konfirmasi', 'Selesai', 'Ditolak', 'Return Diambil')
    ORDER BY p.id_pesanan DESC
");

// Menarik data kurir ekspedisi aktif untuk elemen dropdown select
$query_kurir = mysqli_query($koneksi, "SELECT * FROM users WHERE role = 'kurir_ekspedisi'");
$list_kurir = [];
while ($k = mysqli_fetch_assoc($query_kurir)) {
    $list_kurir[] = $k;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Karyawan - Sistem Manajemen Operasional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navigasi Panel Manajemen -->
    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fa-solid fa-gears me-2 text-warning"></i>Agro Kates Mandiri - Panel Administrasi Karyawan</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm fw-bold px-3"><i class="fa-solid fa-power-off me-1"></i>Keluar</a>
        </div>
    </nav>

    <div class="container mt-5">
        
        <!-- SEKSI 1: TABEL KONTROL INVENTORI STOK REAL-TIME -->
        <div class="d-flex align-items-center mb-3">
            <h4 class="fw-bold text-dark m-0"><i class="fa-solid fa-cubes-stacked me-2 text-secondary"></i>Kontrol Inventori Stok Gudang (Real-time)</h4>
        </div>
        <div class="card border-0 shadow-sm p-4 mb-5 rounded-3 bg-white">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle m-0">
                    <thead class="table-dark">
                        <tr class="small text-uppercase">
                            <th width="20%">ID Produk</th>
                            <th width="50%">Nama Varietas Bibit</th>
                            <th width="30%">Stok Siap Jual (Kuantitas Per 10 Gram)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $q_stok = mysqli_query($koneksi, "SELECT * FROM produk");
                        while($st = mysqli_fetch_assoc($q_stok)) {
                        ?>
                            <tr>
                                <td class="fw-bold text-secondary">#PRD-<?= $st['id']; ?></td>
                                <td class="text-start fw-semibold"><?= $st['nama_produk']; ?></td>
                                <td><span class="badge bg-success py-2 px-4 fs-6 rounded-pill"><?= $st['stok']; ?> Pcs / Pack</span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SEKSI 2: MANAJEMEN DATA PESANAN MASUK -->
        <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-folder-tree me-2 text-secondary"></i>Manajemen Distribusi & Pesanan Konsumen</h4>
        <div class="card border-0 shadow-sm p-4 rounded-3 bg-white mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr class="text-secondary small text-uppercase border-bottom">
                            <th>ID Transaksi</th>
                            <th>Nama Konsumen</th>
                            <th>Alamat Tujuan Pengiriman</th>
                            <th>Total Pembayaran</th>
                            <th class="text-center">Status Operasional</th>
                            <th class="text-center" width="25%">Detail Operasional & Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_pesanan) == 0) { ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted"><i class="fa-solid fa-ban me-2"></i>Tidak ada antrean pesanan masuk saat ini.</td>
                            </tr>
                        <?php } ?>
                        
                        <?php while ($data = mysqli_fetch_assoc($query_pesanan)) { ?>
                            <tr>
                                <td class="fw-bold text-dark">#INV-<?= $data['id_pesanan']; ?></td>
                                <td><strong><?= $data['nama_konsumen']; ?></strong></td>
                                <td class="text-muted small"><?= htmlspecialchars($data['alamat_tujuan']); ?></td>
                                <td class="fw-bold text-success">Rp <?= number_format($data['total_bayar'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <?php 
                                    if ($data['status_pesanan'] == 'Dikemas') echo "<span class='badge bg-warning text-dark px-3 py-2 rounded-pill'>Proses Packing</span>";
                                    elseif ($data['status_pesanan'] == 'Sedang Dikirim') echo "<span class='badge bg-info text-dark px-3 py-2 rounded-pill'>Dalam Perjalanan</span>";
                                    elseif ($data['status_pesanan'] == 'Tunggu Konfirmasi') echo "<span class='badge bg-primary px-3 py-2 rounded-pill'>Menunggu Konfirmasi</span>";
                                    elseif ($data['status_pesanan'] == 'Selesai') echo "<span class='badge bg-success px-3 py-2 rounded-pill'>Transaksi Selesai</span>";
                                    elseif ($data['status_pesanan'] == 'Ditolak') echo "<span class='badge bg-danger px-3 py-2 rounded-pill'>Ditolak Konsumen</span>";
                                    elseif ($data['status_pesanan'] == 'Return Diambil') echo "<span class='badge bg-secondary px-3 py-2 rounded-pill'>Retur Selesai</span>";
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($data['status_pesanan'] == 'Dikemas') { ?>
                                        <!-- Form Penugasan Kurir Hilir -->
                                        <form action="proses_kirim_karyawan.php" method="POST" class="d-flex gap-2 justify-content-center m-0">
                                            <input type="hidden" name="id_pesanan" value="<?= $data['id_pesanan']; ?>">
                                            <select class="form-select form-select-sm border-secondary-subtle" name="kurir_id" required>
                                                <option value="">-- Pilih Kurir Kurir --</option>
                                                <?php foreach ($list_kurir as $kurir) { ?>
                                                    <option value="<?= $kurir['id']; ?>"><?= $kurir['nama']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <button type="submit" class="btn btn-dark btn-sm fw-bold text-nowrap"><i class="fa-solid fa-paper-plane me-1"></i>Kirim</button>
                                        </form>
                                    <?php } else { ?>
                                        <!-- Detail logistik berjalan -->
                                        <div class="mb-1"><small class="text-muted d-block">ID Petugas Kurir: <span class="fw-bold text-dark"><?= $data['kurir_id'] ?? 'Tidak Ada'; ?></span></small></div>
                                        
                                        <!-- Tombol Cetak Nota Fisik Terintegrasi -->
                                        <a href="cetak_invoice.php?id=<?= $data['id_pesanan']; ?>" target="_blank" class="btn btn-outline-secondary btn-sm fw-bold py-1 px-3 rounded-pill mt-1">
                                            <i class="fa-solid fa-print me-1"></i>Cetak Dokumen Nota
                                        </a>

                                        <?php if ($data['status_pesanan'] == 'Ditolak') { ?>
                                            <div class="alert alert-danger border-0 p-2 small mt-2 mb-0 text-start">
                                                <strong>Catatan Retur:</strong> "<?= htmlspecialchars($data['alasan_tolak']); ?>"
                                            </div>
                                        <?php } ?>
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