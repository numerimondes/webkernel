<!DOCTYPE html>
<html>
<head>
    <title>Test Button</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Test Button Component</h1>
    
    <div class="space-y-4">
        <div>
            <h2 class="text-lg font-semibold mb-2">Basic Button</h2>
            <x-button text="Click me" />
        </div>
        
        <div>
            <h2 class="text-lg font-semibold mb-2">Button with Icon</h2>
            <x-button text="Save" icon="save" />
        </div>
        
        <div>
            <h2 class="text-lg font-semibold mb-2">Button with Badge</h2>
            <x-button text="Notifications" badge="5" />
        </div>
        
        <div>
            <h2 class="text-lg font-semibold mb-2">Disabled Button</h2>
            <x-button text="Disabled" disabled="true" />
        </div>
        
        <div>
            <h2 class="text-lg font-semibold mb-2">Loading Button</h2>
            <x-button text="Loading..." loading="true" />
        </div>
    </div>
</body>
</html>
