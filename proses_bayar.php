<?php
include 'koneksi.php';

$id_pesanan = $_POST['id_pesanan'];

// Update status pesanan jadi 'Dikemas'
mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'Dikemas' WHERE id_pesanan = $id_pesanan");

// Balikin user ke dashboard konsumen dengan alert sukses
echo "<script>alert('Pembayaran Berhasil! Pesanan lo sekarang lagi dikemas sama karyawan.'); window.location='dashboard_konsumen.php';</script>";
?>