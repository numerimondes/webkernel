<!DOCTYPE html>
<html lang="fr">
<head>
    @include('main-numerimondes-com.partials.meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* empêche le scroll horizontal */
        }

        main {
            max-width: 100%;  /* empêche tout élément de dépasser */
            overflow-x: hidden;
        }

        * {
            box-sizing: border-box;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <main>
        @yield('content')
    </main>
</body>
</html>
