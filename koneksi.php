<?php
// koneksi.php
$koneksi = mysqli_connect("localhost", "root", "", "agro_kates_rpl");

if (mysqli_connect_errno()){
    echo "Koneksi database gagal: " . mysqli_connect_error();
}
?>