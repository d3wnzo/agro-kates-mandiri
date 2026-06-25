<?php
include 'koneksi.php';

$id_pesanan = $_POST['id_pesanan'];
$kurir_id = $_POST['kurir_id'];

// Update status pesanan jadi 'Sedang Dikirim' dan set kurir yang bertugas
$query = "UPDATE pesanan SET status_pesanan = 'Sedang Dikirim', kurir_id = $kurir_id WHERE id_pesanan = $id_pesanan";
mysqli_query($koneksi, $query);

echo "<script>alert('Pesanan berhasil diserahkan ke kurir!'); window.location='dashboard_karyawan.php';</script>";
?>