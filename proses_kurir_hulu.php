<?php
include 'koneksi.php';

$id_restock = $_POST['id_restock'];

// 1. Ambil data restock buat tahu jumlah Kg dan Produk ID-nya
$query_res = mysqli_query($koneksi, "SELECT * FROM restock WHERE id_restock = $id_restock");
$data_res = mysqli_fetch_assoc($query_res);

$produk_id = $data_res['produk_id'];
$jumlah_kg = $data_res['jumlah_beli'];

// 2. LOGIKA KONVERSI: 1 Kg = 100 pack kemasan retail 10 gram
$tambahan_kuantitas_pack = $jumlah_kg * 100;

// 3. Update status transaksi restock menjadi 'Diterima' (Pengiriman Selesai)
mysqli_query($koneksi, "UPDATE restock SET status_kirim = 'Diterima' WHERE id_restock = $id_restock");

// 4. Otomatis tambahkan ke stok utama produk di gudang karyawan
mysqli_query($koneksi, "UPDATE produk SET stok = stok + $tambahan_kuantitas_pack WHERE id = $produk_id");

echo "<script>alert('Pengiriman Selesai! Bibit sukses diterima Karyawan Gudang. Otomatis terkonversi jadi $tambahan_kuantitas_pack Pack siap jual!'); window.location='dashboard_pengirim_bibit.php';</script>";
?>