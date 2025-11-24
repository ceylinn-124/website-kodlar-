<?php
// SESSION BAŞLATMA (İleride belki lazım olur diye ekleyelim)
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
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Durumu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
/* =======================================
   3. FORMDAN VERİ ALMA VE İŞLEME
   ======================================= */

$email = $_POST['form_email'];
$kullanici_adi = $_POST['form_kullanici_adi'];
$gelen_sifre = $_POST['form_sifre'];

// GÜVENLİK: ŞİFREYİ ŞİFRELEME (HASH)
$sifre_hash = password_hash($gelen_sifre, PASSWORD_DEFAULT);

/* =======================================
   4. VERİYİ VERİTABANINA EKLEME
   ======================================= */

// SQL Sorgusu (Sütun adının 'sifre' olduğundan eminiz)
$sql = "INSERT INTO kullanicilar (email, kullanici_adi, sifre) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("SQL sorgusu hazırlanamadı: " . $conn->error);
}

$stmt->bind_param("sss", $email, $kullanici_adi, $sifre_hash);

/* =======================================
   5. SONUÇLARI GÖSTERME
   ======================================= */

if ($stmt->execute()) {
    // BAŞARILI KAYIT!
    // Stilize edilmiş BAŞARI kutusu (Giriş sayfasıyla aynı stili kullanıyor)
    echo '<div class="message-container success">';
    echo '  <h1>Kayıt Başarılı!</h1>';
    echo '  <p>Harika! Hesabınız başarıyla oluşturuldu.</p>';
    echo '  <p>Artık "Giriş Yap" sayfasından hesabınıza erişebilirsiniz.</p>';
    echo '  <a href="giris-yap.html" class="btn-link">Giriş Yapmak için tıklayın</a>';
    echo '</div>';
    
} else {
    // HATALI KAYIT (Belki e-posta zaten kayıtlıdır? - Bu, ileride eklenecek bir özellik)
    echo '<div class="message-container error">';
    echo '  <h1>Kayıt Başarısız</h1>';
    echo '  <p>Beklenmedik bir hata oluştu: ' . $stmt->error . '</p>';
    echo '  <a href="kayitol.html" class="btn-link">Geri Dönmek için tıklayın</a>';
    echo '</div>';
}

$stmt->close();
$conn->close();

?>

</body>
</html>