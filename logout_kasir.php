<?php
session_start();

// Hapus session kasir
unset($_SESSION['kasir']);

// Redirect ke halaman login kasir
header("Location: login_kasir.php");
exit();
?>