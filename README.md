# 🌉 TextBridge Website

<div align="center">

[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)

</div>

---

**TextBridge** ist die offizielle Website für ein innovatives Diplomarbeitsprojekt an der HTL Ungargasse (2026/27). Das Projekt zielt darauf ab, Sprachbarrieren für gehörlose Menschen durch intelligente Brillentechnologie zu überwinden, die Sprache-zu-Text, Gebärdensprache-zu-Text und Echtzeit-Übersetzungsfunktionen bietet.

## 🎯 Projektübersicht

Die TextBridge-Brille projiziert Text direkt in das Sichtfeld der Nutzer und ermöglicht:
- **Sprache-zu-Text**: Echtzeit-Spracherkennung mit dem Vosk-Modell
- **Gebärdensprache-zu-Text**: Computer-Vision-basierte Gebärdensprachenerkennung
- **Sprachübersetzung**: Mehrsprachige Übersetzung für internationale Kommunikation

## 👥 Team

<table>
<tr>
<td align="center"><b>Felix Horngacher</b><br>Lead Developer & KI-Spezialist</td>
<td align="center"><b>Oliver Kellner</b><br>Software Engineer & UX Designer</td>
<td align="center"><b>Raphael Klein</b><br>Hardware Engineer & Design Lead</td>
</tr>
</table>

**Projektbetreuer**: Dipl.-Ing. Mag. Dr. Martin Hasitschka

## 🌐 Website-Struktur

### Hauptseiten
- **Home** (`index.html`) - Einführung und Mission
- **Produkte** (`products.html`) - Detaillierte Features und Spezifikationen
- **Team** (`team.html`) - Teammitglieder-Profile mit LinkedIn-Links
- **Geschichte** (`history.html`) - Projekt-Timeline und Entwicklungsgeschichte
- **Kontakt** (`contact.html`) - Sicheres Kontaktformular

### Unterstützende Seiten
- **Barrierefreiheitserklärung** (`accessibility.html`) - WCAG 2.1 AA-konform
- **Datenschutzerklärung** (`privacy.html`) - DSGVO-konforme Datenschutzinformationen

## ✨ Features

### Design & Benutzererfahrung
- ✅ Modernes, professionelles Design mit konsistentem Branding
- ✅ Vollständig responsiv (Mobile, Tablet, Desktop)
- ✅ Flüssige Animationen und Übergänge
- ✅ Intuitive Navigation mit aktiver Seitenhervorhebung

### Barrierefreiheit
- ✅ WCAG 2.1 AA-konform
- ✅ Semantische HTML-Struktur
- ✅ ARIA-Labels und -Rollen
- ✅ Tastaturnavigation
- ✅ "Zum Hauptinhalt springen"-Link
- ✅ Ausreichender Farbkontrast
- ✅ Unterstützung für reduzierte Bewegung

### Sicherheit
- ✅ Sicherer PHP-Kontaktformular-Handler
- ✅ Eingabevalidierung und -bereinigung
- ✅ CSRF-Schutz
- ✅ Rate Limiting (5 Versuche pro Stunde)
- ✅ Honeypot-Spam-Schutz
- ✅ XSS-Prävention
- ✅ Sicherheits-Header

## 🚀 Erste Schritte

### Voraussetzungen
- Webserver mit PHP 7.4+ (Apache, Nginx, etc.)
- Moderner Webbrowser

### Installation

1. **Repository klonen**
   ```bash
   git clone https://github.com/felosaurier/textbridge-website.git
   cd textbridge-website
   ```

2. **Kontaktformular konfigurieren**
   
   Bearbeiten Sie `contact-handler.php` und aktualisieren Sie:
   ```php
   define('RECIPIENT_EMAIL', 'ihre-email@beispiel.com');
   ```

3. **Auf Webserver deployen**
   
   Laden Sie alle Dateien in das öffentliche Verzeichnis Ihres Webservers hoch (z.B. `/var/www/html` oder `public_html`).

4. **Berechtigungen setzen** (falls erforderlich)
   ```bash
   chmod 755 contact-handler.php
   ```

5. **Website testen**
   
   Navigieren Sie in Ihrem Webbrowser zu Ihrer Domain.

### Lokale Entwicklung

Für lokales Testen mit PHP:

```bash
php -S localhost:8000
```

Besuchen Sie dann `http://localhost:8000` in Ihrem Browser.

## 📁 Projektstruktur

```
textbridge-website/
├── index.html              # Startseite
├── products.html           # Produkte & Features
├── team.html              # Team-Profile
├── history.html           # Projekt-Timeline
├── contact.html           # Kontaktformular
├── accessibility.html     # Barrierefreiheitserklärung
├── privacy.html           # Datenschutzerklärung
├── contact-handler.php    # Sicherer Formular-Handler
├── csrf.php               # CSRF-Token-Verwaltung
├── css/
│   └── style.css         # Haupt-Stylesheet
├── js/
│   └── main.js           # JavaScript-Funktionalität
├── images/
│   ├── logo.svg          # TextBridge-Logo
│   └── ...               # Weitere Bilder
├── vendor/
│   └── PHPMailer/        # E-Mail-Bibliothek
└── README.md             # Diese Datei
```

## 🛠️ Technologien

### Frontend
- **HTML5** - Semantisches Markup
- **CSS3** - Modernes Styling mit CSS-Variablen
- **JavaScript (ES6+)** - Interaktive Funktionalität

### Backend
- **PHP** - Kontaktformular-Verarbeitung
- **PHPMailer** - E-Mail-Versand

### Design-Prinzipien
- Mobile-First Responsive Design
- Progressive Enhancement
- Graceful Degradation
- Performance-Optimierung

## 🔒 Sicherheitsfeatures

Das Kontaktformular umfasst mehrere Sicherheitsebenen:

1. **Eingabevalidierung** - Client- und serverseitige Validierung
2. **Bereinigung** - XSS-Prävention durch Eingabebereinigung
3. **CSRF-Schutz** - Token-basierte Anforderungsverifizierung
4. **Rate Limiting** - Verhindert Spam (5 Übermittlungen/Stunde pro IP)
5. **Honeypot** - Bot-Erkennungsfeld
6. **Sicherheits-Header** - X-Frame-Options, X-XSS-Protection, etc.

## ♿ Barrierefreiheit

Die TextBridge-Website wurde mit Fokus auf Barrierefreiheit entwickelt:

- Semantisches HTML für Screenreader
- Korrekte Überschriftenhierarchie
- Alternativtexte für Bilder
- Tastaturnavigation
- Focus-Indikatoren
- ARIA-Labels und Landmarks
- Farbkontrast-Konformität
- Unterstützung für reduzierte Bewegung

## 📱 Browser-Unterstützung

- Chrome (neueste Version)
- Firefox (neueste Version)
- Safari (neueste Version)
- Edge (neueste Version)
- Mobile Browser (iOS Safari, Chrome Mobile)

## 🤝 Mitwirken

Dies ist ein Diplomarbeitsprojekt für die HTL Ungargasse. Für Anfragen oder Kooperationsmöglichkeiten verwenden Sie bitte das Kontaktformular auf der Website.

## 📄 Lizenz

Copyright © 2026 TextBridge Team. Alle Rechte vorbehalten.

Dieses Projekt ist eine Diplomarbeit an der HTL Ungargasse für das Schuljahr 2026/27.

## 📞 Kontakt

- **Website**: [textbridge.example](https://textbridge.example)
- **E-Mail**: contact@textbridge.example
- **Institution**: HTL Ungargasse, Wien, Österreich

## 🙏 Danksagungen

- **Betreuer**: Dipl.-Ing. Mag. Dr. Martin Hasitschka
- **Institution**: HTL Ungargasse
- **Vosk-Team**: Für das Spracherkennungsmodell
- **Gehörlosen-Community**: Für wertvolles Feedback und Einblicke

---

<div align="center">

**Brücken bauen durch Technologie - ein Gespräch nach dem anderen.**

*Building bridges through technology - one conversation at a time.*

</div>
