<?php
session_start();
include 'koneksi.php';

$owner_id = $_SESSION['id_user'];
$produk_id = $_POST['produk_id'];
$jumlah_kg = $_POST['jumlah_kg']; 

// --- FITUR AUTO ASSIGN ---
// Cari 1 petani mana aja yang ada di database secara otomatis
$q_petani = mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'petani' LIMIT 1");
$data_pt = mysqli_fetch_assoc($q_petani);
$petani_id = $data_pt['id'];

// Cari 1 kurir pengirim bibit (hulu) mana aja yang ada di database secara otomatis
$q_kurir = mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'pengirim_bibit' LIMIT 1");
$data_kr = mysqli_fetch_assoc($q_kurir);
$pengirim_id = $data_kr['id'];
// -------------------------

// Harga grosir hulu: 1 Juta per Kg
$harga_per_kg = 1000000;
$total_tagihan_b2b = $jumlah_kg * $harga_per_kg;

// 1. Cek dulu apakah Saldo Dompet Perusahaan si Owner cukup
$query_cek_saldo = mysqli_query($koneksi, "SELECT saldo FROM users WHERE id = $owner_id");
$data_owner = mysqli_fetch_assoc($query_cek_saldo);

if ($data_owner['saldo'] < $total_tagihan_b2b) {
    echo "<script>alert('Gagal! Saldo kas perusahaan hasil penjualan retail tidak mencukupi untuk order ini.'); window.location='dashboard_owner.php';</script>";
    exit;
}

// 2. Jika saldo cukup, potong langsung saldo kas Owner
mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $total_tagihan_b2b WHERE id = $owner_id");

// 3. Masukkan data ke tabel restock dengan status langsung 'Sudah Dibayar'
$query_insert = "INSERT INTO restock (petani_id, pengirim_id, produk_id, jumlah_beli, status_kirim) 
                 VALUES ($petani_id, $pengirim_id, $produk_id, $jumlah_kg, 'Sudah Dibayar')";

if(mysqli_query($koneksi, $query_insert)) {
    echo "<script>alert('Restock Cepat Berhasil! Saldo kas terpotong Rp " . number_format($total_tagihan_b2b, 0, ',', '.') . ". Petani & Kurir Hulu sudah otomatis didelegasikan sistem.'); window.location='dashboard_owner.php';</script>";
} else {
    echo "<script>alert('Terjadi kesalahan pada sistem database.'); window.location='dashboard_owner.php';</script>";
}
?>