<?php
session_start();
include 'koneksi.php';

// Memastikan berkas diakses melalui metode POST pengiriman formulir
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$action_type = mysqli_real_escape_string($koneksi, $_POST['action_type']);

// ==========================================
// LOGIKA PEMROSESAN OTORISASI MASUK (LOGIN)
// ==========================================
if ($action_type === 'login') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Melakukan query pencarian berdasarkan kecocokan data username dan password
    $query_check = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    
    if (mysqli_num_rows($query_check) === 1) {
        $user_data = mysqli_fetch_assoc($query_check);
        
        // Mengisi variabel session untuk manajemen hak akses sistem
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $user_data['id'];
        $_SESSION['nama'] = $user_data['nama'];
        $_SESSION['role'] = $user_data['role'];

        // Melakukan manajemen pengalihan halaman berdasarkan hak peran (role) aktor
        if ($user_data['role'] === 'pemilik') {
            header("Location: dashboard_owner.php");
        } elseif ($user_data['role'] === 'karyawan') {
            header("Location: dashboard_karyawan.php");
        } elseif ($user_data['role'] === 'petani') {
            header("Location: dashboard_petani.php");
        } elseif ($user_data['role'] === 'pengirim_bibit') {
            header("Location: dashboard_pengirim_bibit.php");
        } elseif ($user_data['role'] === 'kurir_ekspedisi') {
            header("Location: dashboard_kurir_ekspedisi.php");
        } else {
            header("Location: dashboard_konsumen.php");
        }
        exit;
    } else {
        // Interpsi apabila kombinasi data kredensial tidak ditemukan
        echo "<script>alert('Gagal Masuk. Nama pengguna (username) atau kata sandi Anda salah.'); window.location='login.php';</script>";
        exit;
    }
}

// ==========================================
// LOGIKA PEMROSESAN PENDAFTARAN (REGISTER)
// ==========================================
elseif ($action_type === 'register') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    // 1. Validasi keunikan nama pengguna untuk menghindari duplikasi data primer
    $query_username_validate = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($query_username_validate) > 0) {
        echo "<script>alert('Pendaftaran Ditolak. Nama pengguna (username) telah terdaftar di sistem. Harap gunakan nama lain.'); window.location='login.php';</script>";
        exit;
    }

    // 2. Eksekusi penyimpanan entitas pengguna baru ke database sebagai peran konsumen
    $query_insert_user = "INSERT INTO users (nama, username, password, role, saldo) 
                          VALUES ('$nama_lengkap', '$username', '$password', 'konsumen', 0)";
    
    if (mysqli_query($koneksi, $query_insert_user)) {
        echo "<script>alert('Pendaftaran Berhasil. Akun Anda telah aktif. Silakan lakukan proses login.'); window.location='login.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal Sistem. Terjadi kendala teknis pada proses penyimpanan database.'); window.location='login.php';</script>";
        exit;
    }
}
?>