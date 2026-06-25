<?php
include 'koneksi.php';

// Identitas Sesi Akun Pelanggan (Simulasi ID: 1)
$konsumen_id = 1;

$alamat_tujuan = mysqli_real_escape_string($koneksi, $_POST['alamat_tujuan']);
$metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
$belanjaan = $_POST['beli']; // Menampung array kuantitas dalam satuan Pcs

$total_bayar = 0;
$pesanan_valid = false;
$rincian_belanja = [];

// 1. Verifikasi Validasi Sisa Ketersediaan Stok Fisik secara Real-time
foreach ($belanjaan as $id_produk => $jumlah_pcs) {
    if ($jumlah_pcs > 0) {
        
        $id_produk = (int)$id_produk;
        $jumlah_pcs = (int)$jumlah_pcs;

        $query_stok_cek = mysqli_query($koneksi, "SELECT nama_produk, stok FROM produk WHERE id = $id_produk");
        $produk_cek = mysqli_fetch_assoc($query_stok_cek);
        
        // Proteksi jika kuantitas pesanan melampaui sisa stok gudang
        if ($jumlah_pcs > $produk_cek['stok']) {
            echo "<script>alert('Transaksi Dibatalkan. Stok produk ".$produk_cek['nama_produk']." yang tersedia tidak mencukupi (Sisa: ".$produk_cek['stok']." Pcs).'); window.location='dashboard_konsumen.php';</script>";
            exit;
        }
        
        $pesanan_valid = true;
        
        // Implementasi Kebijakan Harga Grosir Progresif Bertingkat
        if ($jumlah_pcs > 10) {
            $harga_per_pcs = 100000;
        } elseif ($jumlah_pcs > 5) {
            $harga_per_pcs = 110000;
        } elseif ($jumlah_pcs > 3) {
            $harga_per_pcs = 115000;
        } else {
            $harga_per_pcs = 130000;
        }
        
        $subtotal = $jumlah_pcs * $harga_per_pcs;
        $total_bayar += $subtotal;
        
        // Konversi kuantitas logistik ke satuan Gram (1 Pcs setara 10 Gram)
        $jumlah_gram = $jumlah_pcs * 10;
        
        $rincian_belanja[] = [
            'produk_id' => $id_produk,
            'jumlah_gram' => $jumlah_gram,
            'jumlah_pcs' => $jumlah_pcs,
            'subtotal' => $subtotal
        ];
    }
}

// Interpsi jika formulir belanja kosong
if (!$pesanan_valid) {
    echo "<script>alert('Gagal Memproses. Harap tentukan kuantitas produk pada minimal satu item produk.'); window.location='dashboard_konsumen.php';</script>";
    exit;
}

// 2. Validasi Kecukupan Dana Komponen Saldo Kredit Internal
if ($metode_pembayaran == "Saldo Kredit") {
    $query_user = mysqli_query($koneksi, "SELECT saldo FROM users WHERE id = $konsumen_id");
    $user = mysqli_fetch_assoc($query_user);
    
    if ($user['saldo'] < $total_bayar) {
        echo "<script>alert('Metode Ditolak. Saldo Kredit internal Anda tidak mencukupi untuk menyelesaikan transaksi ini.'); window.location='dashboard_konsumen.php';</script>";
        exit;
    }
}

// 3. Eksekusi Pencatatan Data Invoice Induk ke Dalam Database
$status_awal = ($metode_pembayaran == "Saldo Kredit") ? "Dikemas" : "Belum Bayar";

mysqli_query($koneksi, "INSERT INTO pesanan (konsumen_id, alamat_tujuan, total_bayar, metode_pembayaran, status_pesanan) 
               VALUES ($konsumen_id, '$alamat_tujuan', $total_bayar, '$metode_pembayaran', '$status_awal')");

$id_pesanan = mysqli_insert_id($koneksi);

// Pencatatan Rincian Item (Detail Invoice) dan Pengurangan Nilai Stok Inventori
foreach ($rincian_belanja as $barang) {
    $p_id = $barang['produk_id'];
    $j_gram = $barang['jumlah_gram'];
    $j_pcs = $barang['jumlah_pcs'];
    $sub = $barang['subtotal'];

    mysqli_query($koneksi, "INSERT INTO detail_pesanan (id_pesanan, produk_id, jumlah_gram, harga_subtotal) 
                            VALUES ($id_pesanan, $p_id, $j_gram, $sub)");
    
    mysqli_query($koneksi, "UPDATE produk SET stok = stok - $j_pcs WHERE id = $p_id");
}

// 4. Manajemen Jalur Alur Pembayaran Terpilih
if ($metode_pembayaran == "Saldo Kredit") {
    mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $total_bayar WHERE id = $konsumen_id");
    echo "<script>alert('Transaksi Berhasil. Pembayaran via Saldo Kredit dikonfirmasi lunas. Pesanan diteruskan ke gudang operasional.'); window.location='dashboard_konsumen.php';</script>";
} else {
    // Alur Integrasi Gerbang Pembayaran Otomatis Midtrans Gateway
    echo "<script>window.location='midtrans_snap.php?id=$id_pesanan';</script>";
}
?>