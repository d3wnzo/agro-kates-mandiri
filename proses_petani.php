<?php
include 'koneksi.php';

// Nangkep ID Restock yang dikirim dari form dashboard petani
$id_restock = $_POST['id_restock'];

// Update status_kirim dari 'Sudah Dibayar' menjadi 'Dalam Perjalanan'
$query = "UPDATE restock SET status_kirim = 'Dalam Perjalanan' WHERE id_restock = $id_restock";

if(mysqli_query($koneksi, $query)) {
    echo "<script>alert('Bibit siap! Status berubah menjadi Dalam Perjalanan. Kurir hulu akan segera menjemput barang.'); window.location='dashboard_petani.php';</script>";
} else {
    echo "<script>alert('Gagal memproses pengiriman, Bro! Cek query database lo.'); window.location='dashboard_petani.php';</script>";
}
?>