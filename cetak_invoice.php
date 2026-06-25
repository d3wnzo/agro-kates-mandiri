<?php
session_start();
include 'koneksi.php';

// Proteksi: Pastiin yang akses adalah Karyawan, Owner, atau Kurir
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_pesanan = $_GET['id'];

// 1. Ambil data utama pesanan + nama konsumen
$query_nota = mysqli_query($koneksi, "
    SELECT p.*, u.nama AS nama_konsumen 
    FROM pesanan p 
    JOIN users u ON p.konsumen_id = u.id 
    WHERE p.id_pesanan = $id_pesanan
");
$nota = mysqli_fetch_assoc($query_nota);

if (!$nota) {
    echo "Data pesanan kagak ketemu, Bro!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $nota['id_pesanan']; ?> - Agro Kates Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #fff; color: #000; font-family: 'Courier New', Courier, monospace; }
        .invoice-box { max-width: 600px; margin: auto; padding: 20px; border: 1px dashed #000; }
        @media print {
            .no-print { display: none; }
            .invoice-box { border: none; padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Tombol Aksi (Gak bakal ikut kecetak pas di-print) -->
    <div class="container text-center my-4 no-print">
        <button onclick="window.print()" class="btn btn-success fw-bold me-2">🖨️ Cetak Nota Sekarang</button>
        <button onclick="window.close()" class="btn btn-secondary fw-bold">Tutup Halaman</button>
    </div>

    <!-- Tampilan Nota Fisik -->
    <div class="invoice-box mt-3">
        <div class="text-center mb-4">
            <h4 class="fw-bold m-0">AGRO KATES MANDIRI</h4>
            <small>Pemasok Bibit Pepaya Unggul Berkualitas</small>
            <hr style="border-top: 2px dashed #000;">
        </div>

        <div class="row small mb-3">
            <div class="col-6">
                <strong>No. Nota:</strong> #AGRO-<?= $nota['id_pesanan']; ?><br>
                <strong>Metode:</strong> <?= $nota['metode_pembayaran']; ?><br>
                <strong>Status:</strong> <?= $nota['status_pesanan']; ?>
            </div>
            <div class="col-6 text-end">
                <strong>Kepada:</strong> <?= $nota['nama_konsumen']; ?><br>
                <strong>Alamat:</strong> <?= $nota['alamat_tujuan']; ?>
            </div>
        </div>

        <table class="table table-sm table-bordered small">
            <thead class="table-dark">
                <tr>
                    <th>Varian Bibit</th>
                    <th class="text-center">Kuantitas</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // 2. Ambil rincian detail produk yang dibeli di dalam nota ini
                $query_detail = mysqli_query($koneksi, "
                    SELECT dp.*, p.nama_produk 
                    FROM detail_pesanan dp
                    JOIN produk p ON dp.produk_id = p.id
                    WHERE dp.id_pesanan = $id_pesanan
                ");
                while ($item = mysqli_fetch_assoc($query_detail)) {
                    // Konversi gram ke pcs/pack biar singkron sama hitungan grosir lo
                    $pcs = $item['jumlah_gram'] / 10;
                ?>
                    <tr>
                        <td><?= $item['nama_produk']; ?></td>
                        <td class="text-center"><?= $pcs; ?> Pcs (<?= $item['jumlah_gram']; ?>g)</td>
                        <td class="text-end">Rp <?= number_format($item['harga_subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
                <tr class="fw-bold">
                    <td colspan="2" class="text-end">TOTAL BAYAR:</td>
                    <td class="text-end">Rp <?= number_format($nota['total_bayar'], 0, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="text-center mt-4 small">
            <hr style="border-top: 1px dashed #000;">
            <p>Terima kasih telah berbelanja di Agro Kates Mandiri.<br>Benih Unggul, Hasil Makmur!</p>
        </div>
    </div>

</body>
</html>