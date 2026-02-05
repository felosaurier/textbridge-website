# TextBridge Website

[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)

**TextBridge** is a professional, multi-page website for an innovative diploma project at HTL Ungargasse (2026/27). The project aims to eliminate language barriers for deaf individuals through smart glasses that provide speech-to-text, sign-to-text, and real-time translation capabilities.

## 🎯 Project Overview

TextBridge glasses project text directly onto the user's field of vision, enabling:
- **Speech-to-Text**: Real-time voice recognition using the Vosk model
- **Sign-to-Text**: Computer vision-based sign language recognition
- **Language Translation**: Multi-language translation for international communication

## 👥 Team

- **Felix Horngacher** - Lead Developer & AI Specialist
- **Oliver Kellner** - Hardware Engineer & Design Lead  
- **Raphael Klein** - Software Engineer & UX Designer

**Project Mentor**: Dipl.-Ing. Mag. Dr. Martin Hasitschka

## 🌐 Website Structure

### Main Pages
- **Home** (`index.html`) - Introduction and mission
- **Products** (`products.html`) - Detailed features and specifications
- **Team** (`team.html`) - Team member profiles with LinkedIn links
- **History** (`history.html`) - Project timeline and development story
- **Contact** (`contact.html`) - Secure contact form

### Supporting Pages
- **Accessibility Statement** (`accessibility.html`) - WCAG 2.1 AA compliance
- **Privacy Policy** (`privacy.html`) - GDPR-compliant privacy information

## ✨ Features

### Design & User Experience
- ✅ Modern, professional design with consistent branding
- ✅ Fully responsive (mobile, tablet, desktop)
- ✅ Smooth animations and transitions
- ✅ Intuitive navigation with active page highlighting

### Accessibility
- ✅ WCAG 2.1 AA compliant
- ✅ Semantic HTML structure
- ✅ ARIA labels and roles
- ✅ Keyboard navigation support
- ✅ Skip to main content link
- ✅ Sufficient color contrast
- ✅ Reduced motion support

### Security
- ✅ Secure PHP contact form handler
- ✅ Input validation and sanitization
- ✅ CSRF protection
- ✅ Rate limiting (5 attempts per hour)
- ✅ Honeypot spam protection
- ✅ XSS prevention
- ✅ Security headers

## 🚀 Getting Started

### Prerequisites
- Web server with PHP 7.4+ (Apache, Nginx, etc.)
- Modern web browser

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/felosaurier/textbridge-website.git
   cd textbridge-website
   ```

2. **Configure the contact form**
   
   Edit `contact-handler.php` and update:
   ```php
   define('RECIPIENT_EMAIL', 'your-email@example.com');
   ```

3. **Deploy to web server**
   
   Upload all files to your web server's public directory (e.g., `/var/www/html` or `public_html`).

4. **Set permissions** (if needed)
   ```bash
   chmod 755 contact-handler.php
   ```

5. **Test the website**
   
   Navigate to your domain in a web browser.

### Local Development

For local testing with PHP:

```bash
php -S localhost:8000
```

Then visit `http://localhost:8000` in your browser.

## 📁 Project Structure

```
textbridge-website/
├── index.html              # Home page
├── products.html           # Products & features
├── team.html              # Team profiles
├── history.html           # Project timeline
├── contact.html           # Contact form
├── accessibility.html     # Accessibility statement
├── privacy.html           # Privacy policy
├── contact-handler.php    # Secure form handler
├── css/
│   └── style.css         # Main stylesheet
├── js/
│   └── main.js           # JavaScript functionality
├── images/
│   └── logo.svg          # TextBridge logo
└── README.md             # This file
```

## 🛠️ Technologies

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with CSS variables
- **JavaScript (ES6+)** - Interactive functionality

### Backend
- **PHP** - Contact form processing

### Design Principles
- Mobile-first responsive design
- Progressive enhancement
- Graceful degradation
- Performance optimization

## 🔒 Security Features

The contact form includes multiple security layers:

1. **Input Validation** - Client and server-side validation
2. **Sanitization** - XSS prevention through input sanitization
3. **CSRF Protection** - Token-based request verification
4. **Rate Limiting** - Prevents spam (5 submissions/hour per IP)
5. **Honeypot** - Bot detection field
6. **Security Headers** - X-Frame-Options, X-XSS-Protection, etc.

## ♿ Accessibility

TextBridge website is designed with accessibility as a priority:

- Semantic HTML for screen readers
- Proper heading hierarchy
- Alternative text for images
- Keyboard navigation support
- Focus indicators
- ARIA labels and landmarks
- Color contrast compliance
- Reduced motion support

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🤝 Contributing

This is a diploma project for HTL Ungargasse. For inquiries or collaboration opportunities, please use the contact form on the website.

## 📄 License

Copyright © 2026 TextBridge Team. All rights reserved.

This project is a diploma project at HTL Ungargasse for the academic year 2026/27.

## 📞 Contact

- **Website**: [textbridge.example](https://textbridge.example)
- **Email**: contact@textbridge.example
- **Institution**: HTL Ungargasse, Vienna, Austria

## 🙏 Acknowledgments

- **Mentor**: Dipl.-Ing. Mag. Dr. Martin Hasitschka
- **Institution**: HTL Ungargasse
- **Vosk Team**: For the speech recognition model
- **Deaf Community**: For valuable feedback and insights

---

*Building bridges through technology - one conversation at a time.*