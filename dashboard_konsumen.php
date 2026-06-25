<?php
include 'koneksi.php';
$konsumen_id = 1; 
$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id = $konsumen_id");
$user = mysqli_fetch_assoc($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Premium - Agro Kates Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; color: #334155; }
        
        /* Premium Navbar Glassmorphism */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
        }
        .navbar-brand { color: #0f5132 !important; font-weight: 700; }
        .nav-text { color: #475569; font-weight: 500; }
        
        /* Product Cards */
        .product-card {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }
        .badge-stok { position: absolute; top: 20px; right: 20px; padding: 8px 16px; border-radius: 30px; font-weight: 600; letter-spacing: 0.5px; }
        
        /* Form & Sidebar */
        .card-summary {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            background-color: #ffffff;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #0f5132 0%, #10b981 100%);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 16px;
            transition: transform 0.2s;
        }
        .btn-checkout:hover { transform: scale(1.02); color: white; }
        
        /* Tables */
        .table-custom { background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .table-custom thead { background: #f1f5f9; color: #475569; font-size: 0.85rem; letter-spacing: 0.5px; }
        .table-custom th, .table-custom td { padding: 16px 20px; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fs-4" href="#"><i class="fa-solid fa-leaf me-2 text-success"></i>Agro Kates Mandiri</a>
            <div class="d-flex align-items-center">
                <span class="nav-text me-4 d-none d-md-inline"><i class="fa-regular fa-circle-user me-2"></i><?= $user['nama']; ?></span>
                <span class="badge bg-light text-success border border-success-subtle px-3 py-2 rounded-pill me-3 shadow-sm fs-6">
                    <i class="fa-solid fa-wallet me-2 text-warning"></i>Rp <?= number_format($user['saldo'], 0, ',', '.'); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-semibold">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="mb-5 text-center">
            <h2 class="fw-bold text-dark mb-2">Katalog Komoditas Premium</h2>
            <p class="text-muted">Pilih varietas bibit pepaya terbaik untuk investasi perkebunan Anda.</p>
        </div>
        
        <form action="proses_checkout.php" method="POST">
            <div class="row g-4">
                <!-- KATALOG -->
                <div class="col-lg-8">
                    <div class="row g-4">
                        <?php 
                        $query_produk = mysqli_query($koneksi, "SELECT * FROM produk");
                        while($produk = mysqli_fetch_assoc($query_produk)) {
                            $is_habis = ($produk['stok'] <= 0);
                        ?>
                        <div class="col-md-6">
                            <div class="card product-card h-100 position-relative p-2">
                                <?php if ($is_habis) { ?>
                                    <span class="badge bg-danger badge-stok"><i class="fa-solid fa-ban me-1"></i>Habis</span>
                                <?php } else { ?>
                                    <span class="badge bg-success badge-stok bg-opacity-10 text-success border border-success-subtle"><i class="fa-solid fa-box-open me-1"></i><?= $produk['stok']; ?> Pcs</span>
                                <?php } ?>

                                <div class="card-body p-4 mt-3">
                                    <h5 class="fw-bold text-dark mb-1"><?= $produk['nama_produk']; ?></h5>
                                    <p class="text-muted small mb-4">Volume per kemasan setara 10 Gram.</p>
                                    
                                    <div class="mb-4">
                                        <span class="text-muted small text-uppercase fw-semibold d-block">Harga Satuan</span>
                                        <span class="fs-3 fw-bold text-success">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span>
                                    </div>
                                    
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden border border-secondary-subtle">
                                        <span class="input-group-text bg-white border-0 fw-semibold text-dark">Kuantitas</span>
                                        <input type="number" class="form-control border-0 text-center fw-bold text-dark bg-light" 
                                               name="beli[<?= $produk['id']; ?>]" min="0" max="<?= $produk['stok']; ?>" 
                                               placeholder="<?= $is_habis ? 'Kosong' : '0'; ?>" <?= $is_habis ? 'disabled' : ''; ?>>
                                        <span class="input-group-text bg-white border-0 text-muted">Pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- RINGKASAN CHECKOUT -->
                <div class="col-lg-4">
                    <div class="card-summary p-4 sticky-top" style="top: 100px;">
                        <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-clipboard-list me-2 text-success"></i>Detail Pengiriman</h5>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Alamat Lengkap Penerima</label>
                            <textarea class="form-control" name="alamat_tujuan" rows="3" required placeholder="Masukkan alamat detail pengiriman..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Pilihan Pembayaran</label>
                            <select class="form-select" name="metode_pembayaran">
                                <option value="Midtrans Gateway" selected>Gerbang Pembayaran (QRIS/VA)</option>
                                <option value="Saldo Kredit">Saldo Kredit Internal</option>
                            </select>
                        </div>

                        <div class="alert bg-success bg-opacity-10 border border-success-subtle rounded-3 small text-success p-3 mb-4">
                            <i class="fa-solid fa-tags me-2"></i><strong>Harga Grosir Otomatis</strong> aktif untuk pembelian > 3 Pcs.
                        </div>

                        <button type="submit" class="btn btn-checkout text-white w-100 shadow">
                            Konfirmasi Pemesanan <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- RIWAYAT TRANSAKSI -->
        <div class="mt-5 pt-5 mb-5">
            <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-clock-rotate-left me-2 text-success"></i>Riwayat Transaksi Pelanggan</h4>
            <div class="table-custom">
                <table class="table table-borderless m-0">
                    <thead>
                        <tr>
                            <th>KODE INVOICE</th>
                            <th>ALAMAT PENGIRIMAN</th>
                            <th>TOTAL TAGIHAN</th>
                            <th class="text-center">STATUS PEMESANAN</th>
                            <th class="text-center">TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_riwayat = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE konsumen_id = $konsumen_id ORDER BY id_pesanan DESC");
                        if (mysqli_num_rows($query_riwayat) == 0) {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'><i class='fa-solid fa-box-open fs-2 d-block mb-3'></i>Belum ada data transaksi tersimpan.</td></tr>";
                        }
                        while ($row = mysqli_fetch_assoc($query_riwayat)) {
                        ?>
                            <tr>
                                <td class="fw-bold text-dark">#INV-<?= $row['id_pesanan']; ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($row['alamat_tujuan']); ?></td>
                                <td class="fw-bold text-dark">Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <?php 
                                    if ($row['status_pesanan'] == 'Belum Bayar') echo "<span class='badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-2 rounded-pill'>Menunggu Bayar</span>";
                                    elseif ($row['status_pesanan'] == 'Dikemas') echo "<span class='badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill'>Diproses</span>";
                                    elseif ($row['status_pesanan'] == 'Sedang Dikirim') echo "<span class='badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle px-3 py-2 rounded-pill'>Dikirim</span>";
                                    elseif ($row['status_pesanan'] == 'Tunggu Konfirmasi') echo "<span class='badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 rounded-pill'>Tiba di Lokasi</span>";
                                    elseif ($row['status_pesanan'] == 'Selesai') echo "<span class='badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill'>Selesai</span>";
                                    elseif ($row['status_pesanan'] == 'Ditolak') echo "<span class='badge bg-dark bg-opacity-10 text-dark border border-dark-subtle px-3 py-2 rounded-pill'>Ditolak</span>";
                                    elseif ($row['status_pesanan'] == 'Return Diambil') echo "<span class='badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-3 py-2 rounded-pill'>Dana Dikembalikan</span>";
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status_pesanan'] == 'Tunggu Konfirmasi') { ?>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <form action="proses_konfirmasi_konsumen.php" method="POST" class="m-0">
                                                <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan']; ?>">
                                                <input type="hidden" name="status_aksi" value="terima">
                                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold shadow-sm">Terima</button>
                                            </form>
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTolak<?= $row['id_pesanan']; ?>">Tolak</button>
                                        </div>

                                        <!-- Modal Premium -->
                                        <div class="modal fade" id="modalTolak<?= $row['id_pesanan']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                                    <div class="modal-header border-bottom-0 p-4 pb-0">
                                                        <h5 class="fw-bold text-dark">Formulir Pengembalian #INV-<?= $row['id_pesanan']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="proses_konfirmasi_konsumen.php" method="POST">
                                                        <div class="modal-body p-4">
                                                            <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan']; ?>">
                                                            <input type="hidden" name="status_aksi" value="tolak">
                                                            <label class="form-label text-muted small fw-semibold">Alasan Penolakan Barang</label>
                                                            <textarea class="form-control bg-light" name="alasan_tolak" rows="4" required placeholder="Jelaskan kendala secara rinci..."></textarea>
                                                        </div>
                                                        <div class="modal-footer border-top-0 p-4 pt-0">
                                                            <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-2 shadow-sm">Ajukan Pengembalian</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <span class="text-muted small">-</span>
                                    <?php } ?>
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