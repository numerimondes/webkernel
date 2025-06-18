# A production-ready Laravel foundation that transforms development workflow from day one

<div align="center" style="padding: 20px;">
  <a href="https://github.com/numerimondes/WebKernel" target="_blank" rel="noopener noreferrer">
    <img src="https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/projects/webkernel/identity/preview/laravel-filament-webkernel.svg" width="400" alt="WebKernel AND Filament AND Laravel" />
  </a>
  <br>
  <br>
<p style="max-width: 600px; margin-top: auto; line-height: 1.6;">WebKernel is a comprehensive Laravel framework extension that eliminates the repetitive setup phase of modern web applications. Built specifically for developers who want to focus on building features rather than configuring infrastructure, it provides a robust foundation with enterprise-grade capabilities from installation.</p>

  <a href="https://github.com/numerimondes/webkernel/actions" rel="noopener noreferrer">
    <img src="https://github.com/numerimondes/webkernel/actions/workflows/laravel.yml/badge.svg" alt="" />
  </a>
  <a href="https://packagist.org/packages/webkernel/webkernel" rel="noopener noreferrer" style="margin-left: 15px;">
    <img src="https://img.shields.io/packagist/dt/webkernel/webkernel" alt="Total Downloads" />
  </a>
  <a href="https://packagist.org/packages/webkernel/webkernel" rel="noopener noreferrer" style="margin-left: 15px;">
    <img src="https://img.shields.io/packagist/v/webkernel/webkernel" alt="Latest Stable Version" />
  </a>
  <a href="https://packagist.org/packages/webkernel/webkernel" rel="noopener noreferrer" style="margin-left: 15px;">
    <img src="https://img.shields.io/packagist/l/webkernel/webkernel" alt="License" />
  </a>

  <h4>Explore the Full Technical Documentation</h4>
  
<p style="max-width: 600px; margin-top: auto; line-height: 1.6;">
    Unlock the full potential of <strong>WebKernel</strong>. Discover everything you need to get started and go beyond.
    <strong>Click below to explore the official technical docs and get inspired!</strong>
  </p>
  <a href="https://deepwiki.com/numerimondes/webkernel" target="_blank" rel="noopener noreferrer" style="display: inline-block; text-decoration: none; margin-top: 10px;">
    <img src="https://deepwiki.com/badge.svg" alt="Read The Documentation" />
  </a>
  <br>
  <h4 style="max-width: 600px; margin-top: 10px; line-height: 1.6;">Quick Start</h4>
  <pre><code>composer create-project webkernel/webkernel</code></pre>
  <p>This single command sets up a complete Laravel + FilamentPHP environment with pre-configured modules, translations, and development tools.
</p>
<p style="max-width: 600px; margin-top: auto; line-height: 1.6;">
  <img src="packages/webkernel/src/resources/repo-assets/credits/yassine.png" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;"><br>
  Created by <a href="https://www.linkedin.com/in/elmoumenyassine/" target="_blank" rel="noopener noreferrer">
  El Moumen Yassine</a>, for <a href="https://numerimondes.com" target="_blank" rel="noopener noreferrer">Numerimondes</a>
</p>

</div>

---
![](https://img.plantuml.biz/plantuml/svg/T9J1SjCm48RlVWfD3zpQmMdF34cRq43211peiQFQYxD4bZohTKjWU7ViI9D8hZXvjF-dtN-rijvRbiHTepAxHzrAaeqWA5dgMe7uamana-M-9jFfwjOeG-9XXmn3t505v68prKEds7Q7vLw3jIBlOr_TLj90ucs_NXZDdsK3OaeebGyK-1j4VkDNOTA_MNRsS_40JtiW3KfiUb7MS26a5TE8wMk1A82UiGIn8VEC5T2HcoLJUJGcyoJQMweWoK-I_1fhaen69_TS9_TyHtX9uZUZBlKFh3kNNXG7oz0SkTkGUW6VzQhTjKwEEhDCNSaTmTLFQxG3ebPSOb40C-hQEk9wxCGQ1M8b3zxgNImtZTILTKfKVQajIwLy2oLXonwBFFXHYxNK_QS1pGwARTnljvl0mppK-Zl8O5qySh36gbBm8WcSE1aTtkdOvd6nEydoITgmwKMSrm_UEYqgMW1KG5wPHDjC1SeBSS45AZbqBfQQmUS-SX-YFi6QZagmJeelCLOri5T6vrk3ZyT5joIrLQzks7t8hFflDqodwDhM43iY_r-QkQvHWtLKvE9B9nNoOJpb-z6KGRL0ePjbAE-EiZsDRPadrCTACZ0syFWAdwHmrQyV-Tp46O-z5L1sDBJmHcoCmX9zdnz3A5Ron9yol5aCvL39GP6fRnP8p7ynQDi3Firrbhm5lP7XDN7Gfn0vXiKT3Y531mVaoRakK87FCCdIVszyFLOXX0uw7TagAbAQfZKMwFpwh2QltXQMsb_RTIXDsrPX6GzadMVPL5nUlXEpRFQlnZgtGI0AWY3gLpOzGm18Uny_2t39tsIbLblrGYXNGGKTlC2Qc5m55JHOCxEhe886Qs5n5LJGOAsihe8A6goDpLLGGOErixiAAcYm3WvNGGKDridfAgYWmRguN0K5ritjAYYm1iCLx-_lypp67m00)

*System architecture diagram*

## 🏗️ Architecture & Components

| Component | Description | Features |
|-----------|-------------|----------|
| **Translation Engine** | Advanced multi-language support | 53 languages, AI-ready pipeline, RTL handling |
| **Widget System** | Optimized FilamentPHP widgets | Intelligent loading, performance optimization |
| **Command Protection** | Built-in safeguards | Prevents destructive operations in production |
| **User Management** | Extended user profiles | Subscription handling, role management |
| **Security Layer** | Database protection | Update management, access control |

WebKernel follows a modular architecture with multiple specialized service providers, ensuring clean separation of concerns and maximum maintainability.

<img src="packages/webkernel/src/resources/repo-assets/readme/v2/mermaid-diagram-2025-05-31-141023.png" width="1500px">

## 🌍 Enterprise Translation System

WebKernel's sophisticated translation infrastructure stands as one of its most powerful features:

| Feature | Capability | Benefit |
|---------|------------|---------|
| **Language Support** | 53 languages with RTL handling | Global application deployment |
| **Translation Engines** | Google, Bing, Yandex with fallback | High availability and accuracy |
| **AI Integration** | OpenAI, Claude, Gemini ready | Future-proof translation capabilities |
| **Quality Assessment** | Multi-engine comparison scoring | Optimal translation selection |
| **Database Integration** | Priority-based source management | Efficient translation workflow |

The system provides intelligent fallback mechanisms, quality scoring across multiple engines, and seamless integration with modern AI translation services.

## 🛠️ Developer Experience

### Artisan Commands
| Command | Purpose | Usage |
|---------|---------|-------|
| `webkernel:cc` | Interactive component creation | Guided development assistant |
| `webkernel:init` | Complete installation setup | One-click environment configuration |
| `webkernel:loadviews` | Dynamic view loading | Automatic component discovery |

### Security & Protection
WebKernel includes comprehensive protection against destructive commands (`migrate:fresh`, `db:wipe`, `migrate:reset`), preventing accidental data loss in production environments through intelligent command interception.

## 📈 Future Marketing Funnel Implementation

WebKernel's roadmap includes sophisticated marketing automation capabilities designed to transform user engagement and conversion optimization:

| Stage                | Feature                                      |
|----------------------|----------------------------------------------|
| **Lead Capture**     | Advanced form builders with A/B testing      |
| **Lead Nurturing**   | Email automation with behavioral triggers    |
| **Conversion Optimization** | Dynamic landing pages with analytics    |
| **Customer Retention**| Loyalty programs and engagement tracking    |

The marketing funnel system will integrate seamlessly with the existing user management and subscription capabilities, providing end-to-end customer lifecycle management.

## 🔮 Roadmap & Vision

### Current Features  
As of 31 May 2025, here are the current features and their statuses:

| Category       | Features                                      | Status            |
|----------------|-----------------------------------------------|-------------------|
| **Core**       | Multi-language translation system, User profiles, System dashboard | ✅ Production Ready |
| **Layout system** | Give each Filament resource multiple possible layouts           | ✅ Production Ready |
| **Security**   | Command protection, Database safeguards                     | ✅ Production Ready |
| **Business**   | Business capabilities, Role management                      | ⚠️ In Progress      |


### Coming Soon
| Feature                | Impact                          |
|------------------------|--------------------------------|
| **E-commerce Integration** | Complete online store capabilities |
| **Forum Systems**           | Public/private/paid community features |
| **Video Conferencing**      | Real-time communication platform |
| **Dynamic Website Builder** | Drag-and-drop site creation       |
| **Event Management**        | Comprehensive event planning tools |
| **Multi-tenancy Support**   | SaaS-ready architecture           |
| **SSO/Social Authentication** | Enterprise identity integration    |


## 📊 Technical Specifications

| Requirement | Version | License |
|-------------|---------|---------|
| **PHP** | 8.2+ | - |
| **Laravel** | 12.0+ | - |
| **FilamentPHP** | 3.3+ | - |
| **WebKernel** | Latest | Mozilla Public License 2.0 |

## 🎯 Philosophy

WebKernel operates on seven fundamental principles that guide every development decision:

| Principle | Implementation | Benefit |
|-----------|----------------|---------|
| **Minimal Dependencies** | Clean, maintainable codebase | Reduced complexity and conflicts |
| **Smooth Experience** | One-click installation and setup | Immediate productivity |
| **Maximum Customization** | CSS and global variable control | Brand-specific implementations |
| **Future-Proof Design** | Never modify core Laravel/FilamentPHP | Seamless framework updates |
| **Self-Hosted Architecture** | Complete infrastructure control | Data sovereignty and security |
| **Open Source Forever** | MPL-2.0 licensing | Maximum flexibility without vendor lock-in |
| **Operation-Centric** | Business logic as first-class citizen | Real-world application focus |

## 🤝 Contributing

WebKernel welcomes contributions across all development areas, with particular emphasis on security-focused implementations and collaborative security testing. The project values both feature development and infrastructure improvements.

## 📞 Support & Community

| Contact | Information |
|---------|-------------|
| **Author** | El Moumen Yassine |
| **Repository** | https://github.com/numerimondes/webkernel |
| **License** | MPL-2.0 |
| **Documentation** | https://deepwiki.com/numerimondes/webkernel |

---

*WebKernel transforms Laravel development by providing enterprise-grade capabilities from the moment of installation, enabling developers to build sophisticated applications without the traditional setup overhead.*
