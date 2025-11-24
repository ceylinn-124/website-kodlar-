<?php
// SESSION BAŞLATMA (Her zaman en üstte olmalı)
session_start();

/* =======================================
   1. VERİTABANI BAĞLANTI BİLGİLERİ
   ======================================= */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sanat_db";

/* =======================================
   2. VERİTABANI BAĞLANTISINI OLUŞTURMA
   ======================================= */
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız oldu: " . $conn->connect_error);
}

/* =======================================
   HTML SAYFA YAPISINI BAŞLATMA
   ======================================= */
// Artık PHP dosyamız, stil sahibi bir HTML sayfası oluşturacak
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Durumu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
/* =======================================
   3. FORMDAN VERİ ALMA VE İŞLEME
   ======================================= */
$gelen_kullanici_adi = $_POST['form_kullanici_adi'];
$gelen_sifre = $_POST['form_sifre'];

$sql = "SELECT kullanici_id, kullanici_adi, email, sifre FROM kullanicilar WHERE kullanici_adi = ? OR email = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("SQL sorgusu hazırlanamadı: " . $conn->error);
}

$stmt->bind_param("ss", $gelen_kullanici_adi, $gelen_kullanici_adi);
$stmt->execute();
$result = $stmt->get_result();

/* =======================================
   4. SONUÇLARI GÖSTERME
   ======================================= */

if ($result->num_rows == 1) {
    $kullanici = $result->fetch_assoc();
    
    // Şifreler uyuşuyor mu?
    if (password_verify($gelen_sifre, $kullanici['sifre'])) {
        
        // BAŞARILI GİRİŞ!
        $_SESSION['kullanici_id'] = $kullanici['kullanici_id'];
        $_SESSION['kullanici_adi'] = $kullanici['kullanici_adi'];
        $_SESSION['giris_yapti'] = true;

        // Stilize edilmiş BAŞARI kutusu
        echo '<div class="message-container success">';
        echo '  <h1>Giriş Başarılı!</h1>';
        echo '  <p>Hoş geldiniz, <strong>' . htmlspecialchars($kullanici['kullanici_adi']) . '</strong>!</p>';
        echo '  <p>Harika bir sanat yolculuğu dileriz. Ana sayfaya yönlendiriliyorsunuz...</p>';
        echo '  <a href="index.html" class="btn-link">Eğer yönlendirilmezseniz buraya tıklayın.</a>';
        echo '</div>';
        
        // Kullanıcıyı 3 saniye sonra ana sayfaya yönlendir
        header("refresh:3;url=index.html");
        
    } else {
        // Hatalı Şifre
        echo '<div class="message-container error">';
        echo '  <h1>Giriş Başarısız</h1>';
        echo '  <p>Girdiğiniz şifre hatalı. Lütfen tekrar deneyin.</p>';
        echo '  <a href="giris-yap.html" class="btn-link">Geri Dönmek için tıklayın</a>';
        echo '</div>';
    }
    
} else {
    // Kullanıcı Bulunamadı
    echo '<div class="message-container error">';
    echo '  <h1>Giriş Başarısız</h1>';
    echo '  <p>Bu kullanıcı adına veya e-postaya sahip bir hesap bulunamadı.</p>';
    echo '  <a href="giris-yap.html" class="btn-link">Geri Dönmek için tıklayın</a>';
    echo '</div>';
}

$stmt->close();
$conn->close();

?>

</body>
</html>