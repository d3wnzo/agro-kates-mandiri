<?php
include 'koneksi.php';
$id_pesanan = $_GET['id'];

// Ubah status pesanan jadi dikemas biar masuk ke panel karyawan
$query = mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'Dikemas' WHERE id_pesanan = $id_pesanan");

if($query) {
    echo "<script>alert('Sistem mencatat pembayaran lunas! Pesanan diteruskan ke Karyawan.'); window.location='dashboard_konsumen.php';</script>";
}
?>