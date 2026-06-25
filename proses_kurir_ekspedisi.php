<?php
include 'koneksi.php';

$id_pesanan = $_POST['id_pesanan'];
$aksi = $_POST['aksi'];

if ($aksi == 'selesai_kirim') {
    // Kurir sukses antar barang, nunggu konfirmasi dari konsumen
    mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'Tunggu Konfirmasi' WHERE id_pesanan = $id_pesanan");
    echo "<script>alert('Status diupdate: Tunggu konfirmasi konsumen!'); window.location='dashboard_kurir_ekspedisi.php';</script>";
} 
elseif ($aksi == 'ambil_return') {
    // Kurir menjemput barang yang ditolak konsumen untuk dibawa balik ke gudang
    mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'Return Diambil' WHERE id_pesanan = $id_pesanan");
    echo "<script>alert('Status diupdate: Paket return berhasil diambil kurir!'); window.location='dashboard_kurir_ekspedisi.php';</script>";
}
?>