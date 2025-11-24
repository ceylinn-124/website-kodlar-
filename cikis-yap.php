<?php
session_start(); // Hafızayı başlat
session_destroy(); // Hafızayı yok et (Giriş bilgilerini sil)
header("Location: index.php"); // Ana sayfaya geri yönlendir
exit;
?>