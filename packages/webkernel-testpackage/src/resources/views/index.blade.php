<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $package_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #007cba; margin: 20px 0; }
        .version { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $package_name }}</h1>
        <div class="info">
            <p><strong>{{ __('{webkernel-testpackage}::translations.description') }}</strong></p>
            <p class="version">{{ __('{webkernel-testpackage}::translations.version') }}: {{ $version }}</p>
        </div>
        <p>{{ $description }}</p>

        <h2>Getting Started</h2>
        <p>Welcome to your new Webkernel package! You can now customize this package according to your needs.</p>

        <h2>Next Steps</h2>
        <ul>
            <li>Customize the configuration in <code>config/</code></li>
            <li>Add your models in <code>src/Models/</code></li>
            <li>Create controllers in <code>src/Http/Controllers/</code></li>
            <li>Add views in <code>src/resources/views/</code></li>
            <li>Write tests in <code>tests/</code></li>
        </ul>
    </div>
</body>
</html>
