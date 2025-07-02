@php
    use Illuminate\Support\Facades\File;

    $partsPath = resource_path('views/components/webkernel/ui/organism/universal-badge/parts');
    $files = File::files($partsPath);

    $views = [];

    foreach ($files as $file) {
        $filename = pathinfo($file, PATHINFO_FILENAME);

        // Exclure ce fichier s'il est dans parts (éviter inclusion récursive)
        if ($filename === 'all-parts') {
            continue;
        }

        $views[] = 'webkernel::components.webkernel.ui.organism.universal-badge.parts.' . $filename;
    }
@endphp

@foreach ($views as $view)
    @includeIf($view)
@endforeach
