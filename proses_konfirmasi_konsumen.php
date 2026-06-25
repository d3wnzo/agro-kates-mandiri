<?php
include 'koneksi.php';

$id_pesanan = $_POST['id_pesanan'];
$status_aksi = $_POST['status_aksi'];

// Ambil info pesanan untuk tahu nominal uang dan siapa konsumennya
$get_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = $id_pesanan");
$pesanan = mysqli_fetch_assoc($get_pesanan);
$total_bayar = $pesanan['total_bayar'];
$konsumen_id = $pesanan['konsumen_id'];

if ($status_aksi == 'terima') {
    // 1. Update status pesanan jadi Selesai
    mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'Selesai' WHERE id_pesanan = $id_pesanan");
    
    // 2. Uang gateway langsung masuk ke dompet OWNER (Aktor role 'pemilik' punya id 3 di dummy)
    mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $total_bayar WHERE role = 'pemilik'");
    
    echo "<script>alert('Mantap! Pesanan selesai dan dana diteruskan ke Owner.'); window.location='dashboard_konsumen.php';</script>";
} 
elseif ($status_aksi == 'tolak') {
    $alasan_tolak = mysqli_real_escape_string($koneksi, $_POST['alasan_tolak']);
    
    // 1. Update status pesanan jadi Ditolak dan simpan alasannya
    mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan = 'Ditolak', alasan_tolak = '$alasan_tolak' WHERE id_pesanan = $id_pesanan");
    
    // 2. REFUND otomatis: Uang kembali ke Kredit Akun Konsumen tersebut
    mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $total_bayar WHERE id = $konsumen_id");
    
    echo "<script>alert('Pesanan ditolak! Dana belanja lo udah di-refund ke saldo kredit.'); window.location='dashboard_konsumen.php';</script>";
}
?>