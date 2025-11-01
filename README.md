<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/faviconV2_Numerimondes.png" width="70" alt="Laravel Logo"></a>
</p>
<h1 align="center">Webkernel</h1>


<p align="center">
<a href="https://packagist.org/packages/webkernel/webkernel"><img src="https://img.shields.io/packagist/dt/webkernel/webkernel" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/webkernel/webkernel"><img src="https://img.shields.io/packagist/v/webkernel/webkernel" alt="Latest Stable Version"></a>
<a href="https://numerimondes.com"><img src="https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg" alt="License: MPL 2.0"></a>
<a href="https://deepwiki.com/numerimondes/webkernel"><img src="https://deepwiki.com/badge.svg" alt="Ask DeepWiki"></a>
</p>

## The modular foundation for advanced PHP applications.

WebKernel is a high-performance meta-framework built on Laravel, designed to streamline module development, integration, and management for large-scale enterprise systems.

It features:

- ⚡ Ultra-fast module management
- 🏢 Multi-tenant architecture
- 🧩 Visual site building tools
- 🛠️ Dynamic administrative interfaces

Powered by the **Arcanes module system**, WebKernel delivers sub-millisecond module discovery and instant configuration reloads—even under massive module loads.

Developed and maintained by **Numerimondes**, a company founded by **El Moumen Yassine**, a seasoned full-stack architect and business systems designer, WebKernel empowers organizations to build tailored, modular software solutions with speed and precision.

> It addresses the complexities of building scalable systems by automating module discovery, registration, and orchestration, allowing developers to prioritize business logic over infrastructure challenges.

```bash 
1 x Booting (66.73%)	         232ms
1 x Application (33.26%)         116ms
1 x Preparing Response (2.86%)   9.96ms
1 x Routing (0.72%)	             2.50ms
1 x View (0%)	                 0μs
```


WebKernel introduces two primary architectural layers: **Arcanes**, the core abstraction layer for automated module handling, and **Aptitudes**, extensible plug-and-play capabilities for domain-specific concerns such as internationalization, performance optimization, multitenancy, and customer relationship management. This structure enables seamless integration across diverse business needs, from managing a training center to operating an audit firm or hospital—all within a single instance.

## Philosophical Overview

WebKernel exists to tackle the inherent difficulties in constructing modular, scalable Laravel applications, where conventional methods often result in repetitive configurations, fragile integrations, and elevated maintenance costs. Developers in intricate projects commonly face issues like manual module registration, inconsistent discovery processes, and disjointed capability implementations, which impede efficiency and elevate error susceptibility.

The framework resolves these challenges through a "simplicity first" philosophy, automating the discovery, registration, and booting of modules without necessitating manual setups. This eliminates configuration burdens, enabling focus on core business functionalities. Arcanes functions as the foundational layer, delivering imperative, convention-driven tools (e.g., Discover, Register, Boot) that guarantee transparent, resilient module management irrespective of project organization.

Aptitudes extend Arcanes as specialized, composable plugins encapsulating targeted functionalities. Collectively, they constitute an evolutionary architecture: Arcanes manages the "how" of module integration, whereas Aptitudes handle the "what" by offering pre-built capabilities orchestrated through events, workflows, and dependencies. This division facilitates infinite recursion in module hierarchies (e.g., clusters with sub-modules) and promotes graceful failure mechanisms, ensuring robustness in vast ecosystems supporting thousands of modules.

## WebKernel Advantages

WebKernel offers numerous benefits tailored to modern business requirements:

- **Modular Architecture on Demand**: Select only the essential features for your operations. Each module installs independently yet communicates seamlessly with others, reducing friction and enhancing flexibility. Discover the WebKernel structure for detailed insights.

- **Tailored Business Visualization**: Access data segmented by activity, with specific KPIs and filtered views, allowing you to oversee your enterprise as envisioned. View an example dashboard to see this in action.

- **System Ownership**: Your data remains under your control. The system is autonomous, sovereign, open-source, reliable, and modifiable. Learn more about the model.

- **Accompanied Installation**: Numerimondes analyzes your needs and delivers a ready-to-use environment, independent of generic tools. Request your customized specifications.

- **On-Demand Automation**: Automate business actions across multiple destinations, including API integrations, third-party services, and internal modules—all operating cohesively and orchestrated efficiently.

Beyond these, WebKernel is a modular platform enabling each enterprise to possess its bespoke business system. It installs once, with independent, interoperable components, serving as a technical foundation for operational structuring. Targeted at founders, independents, SMEs, institutions, and cabinets in sectors like HR, health, audit, training, and services, it is delivered by Numerimondes with personalized accompaniment.

WebKernel supports managing multiple entities or clients in one instance via its multitenant architecture. Each tenant enjoys a dedicated, secure, isolated functional perimeter—ideal for cabinets, platforms, or agency networks.

## A Simple Process for Your Customized Software System

Obtain your personalized WebKernel system through this straightforward process:

1. **Free Account Creation**: Start by creating a WebKernel account. This helps us understand your business and challenges. You will receive your tailored specifications via email or WhatsApp.

2. **Needs Analysis**: Using the provided details, we structure your system: proposed modules, adapted software architecture, and sector-specific options. You receive a clear, modular, deliverable proposal.

3. **Your WebKernel Deployment**: A dedicated installation launches your operations with a custom stack. Each module is independent, integrated, and designed for your profession. You retain control, with ongoing support for evolution.

Every client receives a technical and functional response exclusively crafted for their activity.

## Module Maps

### Arcanes: Core Abstractions

Arcanes provides the meta-framework's foundational components for module management, adhering to imperative English naming conventions for clarity and directivity.

- **Discover**: Automated module scanning via `ModuleScanner`, analyzing PSR-4 namespaces and declared classes regardless of directory position.
- **Register**: Handles Laravel element registration (e.g., providers, routes, views) through `Webkernel\ArcanessServiceProvider`.
- **Boot**: Manages startup procedures and middleware via `ModuleBooter`.
- **Contracts**: Defines standardized interfaces like `ModuleContract` for ecosystem consistency.
- **Make**: Tools for module creation, including `ModuleCreateCommand` and enums like `EnumArcanesModuleTypes` for categorization and priority-based loading.

These enable flexible module placement, recursive cluster architectures, error resilience, comprehensive logging, and evolutionary extensibility (e.g., potential additions like Validate, Monitor, Secure).

### Aptitudes: Capabilities/Plugins

Aptitudes are extensible modules leveraging Arcanes to deliver specific functionalities. Each extends `WebkernelApp`, includes auto-discovery for Filament resources, models, controllers, and commands, and incorporates metadata for branding, licensing, and integration.

The following table details all Aptitudes, their descriptions, and key features:

| Module Name    | Description                                                                 | Key Features |
|----------------|-----------------------------------------------------------------------------|--------------|
| Testing       | Quality assurance with unit, integration, and context-driven testing.       | Unit/Integration/E2E testing harnesses; CI/CD workflows; Test context abstractions. |
| I18n          | Internationalization supporting dynamic translations and intelligent fallbacks. | Dynamic translation discovery; Fallback mechanisms; Cache management; Workflow integrations. |
| DX            | Developer experience tools for scaffolding, validation, and documentation.  | CLI tools; Validation abstractions; Scaffolding generators; Documentation workflows. |
| Performance   | Profiling and optimization with automated benchmarking and budgets.         | Profiling tools; Optimization strategies; Benchmarking abstractions; Performance budgets. |
| Multitenancy  | Advanced tenant isolation with one-to-many and many-to-many strategies.     | Tenant scopes; Isolation mechanisms; Strategy patterns; Workflow management. |
| Events        | Central event bus for aptitude orchestration and event-driven workflows.    | Event bus; Persistence and replay; Choreography patterns; Scheduling abstractions. |
| CRM           | Configurable sales workflows, automation, and analytics for customer data.  | Workflow automation; Analytics dashboards; Third-party integrations; Pipelines. |

Aptitudes support clustering (parents managing children) and Filament panel integration for administrative interfaces.

### Panels Module

The Panels module, integrated with Filament, offers customizable administrative interfaces for Aptitudes and Arcanes components. It supports dynamic panel creation with features like restricted access, custom icons, route prefixes, and auto-discovery for resources, pages, and widgets. This enables meta-panel oversight of module hierarchies, performance monitoring, and security.

## Interaction Diagram

The following ASCII diagram illustrates Arcanes and Aptitudes interactions within WebKernel:

```
+-------------------+          +-------------------+
|     Laravel       |          |     WebKernel     |
|   Application     |<---------|   Meta-Framework  |
+-------------------+          +-------------------+
           ^                                ^
           |                                |
           | Registers/Bootstraps           | Automates Discovery/Registration
           |                                |
+-------------------+          +-------------------+
|     Aptitudes     |          |     Arcanes      |
| (Capabilities)    |<---------| (Core Layer)     |
| - Testing        |          | - Discover       |
| - I18n           | Events/  | - Register       |
| - DX             | Workflows| - Boot           |
| - Performance    | -------->| - Contracts      |
| - Multitenancy   |          | - Make           |
| - Events         |          +-------------------+
| - CRM            |
+-------------------+
```

Aptitudes rely on Arcanes for integration, with events facilitating capability orchestration.

## Installation and Setup

To install WebKernel, a valid license is required for downloading the full project from numerimondes.com/webkernel.

1. Use Composer to create the project:  
   ```
   composer create-project webkernel/webkernel
   ```

2. Add the service provider to `bootstrap/providers.php` (Laravel 12+):  
   ```php
   return [
       App\Providers\AppServiceProvider::class,
       Webkernel\Webkernel\Arcaness\Providers\Webkernel\ArcanessServiceProvider::class,
   ];
   ```

3. Generate modules via Artisan:  
   ```
   php artisan webkernel:module-create app/MyModule
   ```

For production, activate OPCache preloading, cache configurations/routes, implement lazy-loading, and consider horizontal scaling.

## Usage

Extend `WebkernelApp` in your module and configure using the fluent builder in `configureModule()`. The framework automates discovery, registration, and booting.

Example:  
```php
protected function configureModule(): void {
    $this->setModuleConfig(
        $this->module()
            ->id('my_module')
            ->name('My Module')
            ->version('1.0.0')
            ->description('Module description')
            ->providers([/* ... */])
            ->viewNamespaces([/* ... */])
            ->build()
    );
}
```

Consult individual Aptitude directories for detailed usage.

## Performance Considerations

WebKernel is engineered for high-scale environments, accommodating up to 3,000 modules with approximately 600ms bootstrap overhead and over 2,500 requests per second in real conditions. Employ caching, horizontal scaling, and continuous monitoring via APM tools.

## Contribution Guidelines

- English-only documentation and code comments.
- Use clear class/function naming (PascalCase for classes, camelCase for methods).
- Follow PSR-12 code style (indentation, spacing).
- Avoid emojis, keep a professional tone.
- Document key classes and files explicitly by path with short summaries.
- Pull requests must explain rationale and reference related Arcanes/Aptitudes.

## Licensing and Contact

Secure your license at numerimondes.com/webkernel. Receive a free personalized technical dossier tailored to your sector—no commitment required.

- **WhatsApp & Networks**: Available 7/7 at +212 6 20 99 06 92 and on social platforms.
- **Online Portal**: Submit requests via our portal.
- **Email**: Contact rc@numerimondes.com—response within 24 hours guaranteed.

WebKernel is designed and maintained by Numerimondes, founded by Yassine El Moumen, ensuring expert, reliable support.
