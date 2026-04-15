<?php
session_start();
// Hata raporlamayı açalım (Geliştirme aşamasında olduğumuz için hataları görmek önemli)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı sınıfımızı dahil ediyoruz
require_once 'app/config/database.php';

// Basit bir MVC Yönlendirici (Router) yapısı
// Örnek URL: localhost/isg/uye/profil
// Controller = UyeController, Metot = profil
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'login';
$url = explode('/', $url);

$controllerName = ucfirst($url[0]) . 'Controller'; // ilk eleman Controller adı (örn: Home -> HomeController)
$methodName = isset($url[1]) ? $url[1] : 'index'; // ikinci eleman metod adı, yoksa 'index' sayılır

$controllerFile = 'app/controllers/' . $controllerName . '.php';

// Eğer gidilmek istenen sayfa (Controller) varsa onu çağırıyoruz
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();

    // Metot var mı kontrol et (örn: HomeController'daki index() fonksiyonu)
    if (method_exists($controller, $methodName)) {
        // İlgili controller'ın metodunu çağır ve ekstra parametreleri (.htaccess'ten gelen /id vs) pasla
        call_user_func_array([$controller, $methodName], array_slice($url, 2));
    } else {
        echo "<h1>404 - Sayfa Bulunamadı</h1><br> Method <b>'$methodName'</b> bulunamadı: <b>$controllerName</b>!";
    }
} else {
    echo "<h1>404 - Sayfa Bulunamadı</h1><br> Controller <b>'$controllerName'</b> dosyası bulunamadı!";
}
?>
