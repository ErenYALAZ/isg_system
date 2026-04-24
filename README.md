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



---

<div align="center">

[![Typing SVG](https://readme-typing-svg.demolab.com?font=Fira+Code&size=24&pause=1000&color=E74C3C&center=true&vCenter=true&width=700&lines=ISG+Management+Portal;Occupational+Health+%26+Safety+System;PHP+%7C+MySQL+%7C+RBAC+%7C+Multi-Module)](https://git.io/typing-svg)

</div>

---

## 🌍 English

### 🏭 About

A comprehensive **Occupational Health & Safety (OHS/ISG) Management Portal** built from scratch for corporate environments. Actively running in a **live production environment**, digitizing all internal OHS operations.

Built on a custom **MVC (Model-View-Controller)** architecture, the system automates complex corporate workflows, inter-department coordination, and document approval processes.

### ✨ Key Features

- **🔐 Role-Based Access Control (RBAC)** — Admin, HR, Department Manager, and Employee roles with automatic permission filtering
- **📊 Dynamic Analytics Dashboard** — Monthly stats, risk assessments, and open/closed request counts in real-time
- **📋 Suggestion, Notification & Near-Miss Management** — Advanced reporting pool for employees and managers
- **🔄 Multi-Stage Approval Workflows** — Hierarchical approval chains with automatic email notifications at each stage
- **📄 Dynamic PDF Report Generation** — One-click export of monthly reports and documents in corporate format
- **📱 100% Responsive UI** — Premium mobile and tablet-compatible interface
- **💬 Internal Chat Module** — End-to-end closed messaging system between staff and HR department

### 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.0+ (OOP, PDO) |
| Architecture | Custom MVC Framework |
| Database | MySQL (optimized with indexes) |
| Frontend | Vanilla CSS3, HTML5, Vanilla JS |
| Email | PHPMailer (SMTP) |

### 📋 Installation

1. Clone the repo and place it in your web server directory
2. Import `database_schema.sql`
3. Rename `secrets.php.example` → `secrets.php` and fill in credentials
4. Access via `http://localhost/isg_system`

### 🔒 Security Notice

Sensitive files (`secrets.php`) are excluded via `.gitignore`. Use the provided template.

---

<div align="center">
<sub>Built with ❤️ for corporate safety by <a href="https://github.com/ErenYALAZ">ErenYALAZ</a></sub>
</div>