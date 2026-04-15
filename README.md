<div align="center">
  <img src="https://img.shields.io/badge/STATUS-ACTIVE_PRODUCTION-success?style=for-the-badge" alt="Active Production" />
  <h1>🛡️ Kurumsal İSG Yönetim Sistemleri (Aktif Kullanımda)</h1>
  <p><strong>Şirket içi İş Sağlığı ve Güvenliği (İSG) süreçlerini dijitalleştiren, çoklu platform destekli yönetim ve takip platformu.</strong></p>

  <!-- Teknoloji Rozetleri -->
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5" />
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
  <img src="https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E" alt="JavaScript" />
</div>

<br>

## 🚀 Proje Hakkında
Bu proje, kurum içi gerçekleştirilen İş Sağlığı ve Güvenliği (İSG) denetimlerini kolaylaştırmak ve veri takibini anlık hale getirmek amacıyla **sıfırdan tasarlanmış ve geliştirilmiştir**. Ekibimin ve şirketimin tüm İSG operasyonları bu platform üzerinden yürütülmekte olup **aktif üretim ortamında (production)** kullanılmaktadır. 

Sistem, özel olarak kurguladığım bir **MVC (Model-View-Controller)** mimarisi üzerine inşa edilmiştir. Sadece veri tutan bir program olmakla kalmayıp, karmaşık kurumsal iş akışlarını otomatikleştirerek departmanlar arası koordinasyonu ve belge onay süreçlerini dijitalleştirmektedir.

---

## ✨ Temel Özellikler

- **🛡️ Katmanlı Rol & Yetki Kontrolü (RBAC)**
  Sistemde Admin, İK (İnsan Kaynakları), Departman Yöneticisi ve Standart Personel olmak üzere farklı yetki seviyeleri bulunmaktadır. Kullanıcıların göreceği menüler ve yapabileceği işlemler unvanlarına göre otomatik filtrelenir.

- **📊 Dinamik Analitik & Yönetici Dashboard'u**
  Anasayfa üzerinden departmanların aylık istatistikleri, risk değerlendirmeleri, açık/kapalı durumdaki talep sayıları grafiksel ve listeler halinde anlık sunulur.

- **📢 Öneri, Bildirim ve Ramak Kala Yönetimi**
  Çalışanların tehlike bildirimlerini veya İSG önerilerini kolayca iletebildikleri ve yöneticilerin bu bildirimleri tek bir panelden "Çözüldü/Çözülmedi" olarak yönetebildikleri gelişmiş veri havuzu.

- **📬 Çok Aşamalı Onay ve İş Akışları (Multi-Stage Workflow)**
  Oluşturulan bildirimler zincirleme hiyerarşik aşamalardan geçer (Örn: Önce Departman Yöneticisi ➡️ Sonra İK Onayı). Süreç içinde tüm ilgili yöneticilere **otomatik e-posta bildirimleri** atılır.

- **📄 Dinamik PDF Raporlama Sistemi**
  Aylık raporlar, duyurular veya yapılan bildirim eylemleri kurumsal formata uygun olarak sunucuda tek tuşla manipüle edilir ve **PDF formatında dışa aktarılır**.

- **📱 %100 Mobil ve Tablet Uyumluluğu (Responsive Arayüz)**
  Kullanıcı deneyimi en üstte tutularak tüm "Dashboard" ve menüler akıllı telefonlarda kesintisiz çalışacak, premium bir hissiyat verecek tasarımla kodlanmıştır.

- **💬 Kurum İçi İletişim Konsolu**
  Personellerin birbirleriyle ve İK departmanıyla uygulama üzerinden gerçek zamanlı senkronizasyonla hızlıca haberleşebilmesi için uçtan uca kapalı Chat modülü geliştirilmiştir.

---

## 🛠️ Sistem Mimarisi ve Teknoloji Yığını

Maksimum esneklik, bağımsızlık ve hız hedeflenerek tamamen **Özel Framework (Custom MVC)** olarak yapılandırılmıştır. Güvelik zafiyeti oluşturabilecek fazla dış bağımlılıklardan kaçınılmıştır.

*   **Backend İşlemleri:** Güvenli veri iletişimi için Pure PHP 8.* ve nesne yönelimli programlama (OOP) kavramlarıyla (PDO entegrasyonlu).
*   **Mimari Yapı:** View, Controller ve Model katmanlarının kesin bir çizgiyle ayrıldığı, yönlendirmelerin kendi Route mekanizmam tarafından yönetildiği bir altyapı.
*   **Veritabanı Yönetimi:** Karmaşık ilişkili veri tabloları yapısı, performans indekslemeleri ile optimize edilmiş MySQL. 
*   **Frontend Arayüzü:** Vanilla CSS (Tailwind bootstrap gibi frameworklerden ziyade tamamen amaca özel tasarlanmış CSS Tema Motoru) & HTML5.
*   **Fonksiyonellik:** DOM manipülasyonu, asenkron modal işlemleri için Modern Vanilla JS. E-posta hizmetleri (SMTP) için PHPMailer.

---
