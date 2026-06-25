<?php
include 'koneksi.php';

$id_pesanan = $_GET['id'];

// Ambil info pesanan buat dapet nominal total bayarnya
$query = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = $id_pesanan");
$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) {
    echo "Pesanan kagak ketemu, Bro!";
    exit;
}

$total_bayar = $pesanan['total_bayar'];

/**
 * CONFIGURATION MIDTRANS SANDBOX (BUAT KULIAH / DEMO)
 * Lo tinggal ganti SERVER_KEY & CLIENT_KEY di bawah ini nanti 
 * setelah lo daftar di dashboard.midtrans.com (mode Sandbox)
 */
$server_key = "Mid-server-zDvoxyXLUF_444ZkLMzyU04R"; 
$client_key = "Mid-client-G6yE5CKGb7Iis6MP";

// 1. Siapkan data JSON untuk dikirim ke API Midtrans (Meminta Token Snap)
$transaction_details = [
    'order_id'     => 'AGRO-' . $id_pesanan . '-' . time(), // Harus unik setiap request
    'gross_amount' => (int)$total_bayar,
];

$payload = [
    'transaction_details' => $transaction_details,
];

$json_payload = json_encode($payload);

// 2. Request Token ke API Midtrans pake cURL (Taktis tanpa install library)
$ch = curl_init('https://app.sandbox.midtrans.com/snap/v1/transactions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Basic ' . base64_encode($server_key . ':')
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$snap_token = isset($result['token']) ? $result['token'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesaikan Pembayaran - Agro Kates Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- PENTING: Panggil Script Snap JS Resmi Midtrans Sandbox -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $client_key; ?>"></script>
</head>
<body class="bg-light">

    <div class="container mt-5 text-center">
        <div class="card border-0 shadow-sm p-5 mx-auto" style="max-width: 500px;">
            <h4 class="fw-bold text-success mb-3">Satu Langkah Lagi, Bro!</h4>
            <p class="text-muted">Pesanan lo **#<?= $id_pesanan; ?>** udah tercatat di sistem. Silakan klik tombol di bawah untuk membayar aman via Midtrans.</p>
            
            <h3 class="fw-bold text-dark my-4">Rp <?= number_format($total_bayar, 0, ',', '.'); ?></h3>

            <?php if ($snap_token != '') { ?>
                <button id="pay-button" class="btn btn-success btn-lg w-100 fw-bold shadow-sm py-3">💳 BAYAR SEKARANG</button>
            <?php } else { ?>
                <div class="alert alert-danger fw-bold small">
                    Gagal dapet Token Midtrans, Bro! <br>
                    <span class="fw-normal">Pastiin lo udah ganti <code class="text-dark">$server_key</code> di baris kode atas pake akun Sandbox lo sendiri.</span>
                </div>
            <?php } ?>
            
            <a href="dashboard_konsumen.php" class="btn btn-link btn-sm text-muted mt-3">Kembali ke Dashboard</a>
        </div>
    </div>

    <!-- LOGIKA TRIGER POP-UP MIDTRANS SNAP -->
    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.addEventListener('click', function () {
                // Jalankan pop-up Midtrans bawaan SDK
                window.snap.pay('<?= $snap_token; ?>', {
                    onSuccess: function(result){
                        /* Otomatis lempar ke file update status jadi DIKEMAS kalau sukses bayar */
                        alert("Pembayaran lunas! Mantap, Bro."); 
                        window.location.href = 'proses_update_lunas.php?id=<?= $id_pesanan; ?>';
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran lo nih, jangan lupa bayar ya!"); 
                    },
                    onError: function(result){
                        alert("Waduh, pembayaran gagal. Coba lagi deh, Bro.");
                    },
                    onClose: function(){
                        alert('Lo nutup pop-up sebelum kelar bayar, Bro.');
                    }
                });
            });
        }
    </script>
</body>
</html>