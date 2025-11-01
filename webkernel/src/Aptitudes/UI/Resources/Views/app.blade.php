<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App</title>
    <!---- getCSS --->

    @filamentStyles
    @filamentScripts
    <!---- getHTML --->
    {{ filament()->getTheme()->getHtml() }}
    <!---- getFontHtml --->
    {{ filament()->getFontHtml() }}
    {{ filament()->getMonoFontHtml() }}
    <!---- getSerifFontHtml --->
    {{ filament()->getSerifFontHtml() }}

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <x-base::UIQuery module="ui-module" include="css,js" recursive />

    <!-- Sound Manager -->
    <script src="{{ asset('webkernel/src/Aptitudes/UI/Resources/js/sound-manager.js') }}"></script>

    <!----->
    <script>
        // Force dark mode
        document.documentElement.classList.remove('light');
        document.documentElement.classList.add('dark');
    </script>

</head>

<body>

    <div class="flex justify-center items-center h-screen shadow-2xl">

    </div>
</body>

</html>
