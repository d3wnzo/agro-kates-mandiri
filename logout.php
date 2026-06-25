<?php
session_start();
// Hancurkan semua data session login
session_unset();
session_destroy();

// Tendang balik ke halaman login utama
header("Location: login.php");
exit;
?>