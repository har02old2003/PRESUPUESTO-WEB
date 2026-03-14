@php
    $manifestPath = public_path('build/manifest.json');
    $manifest = null;

    if (is_file($manifestPath)) {
        $decoded = json_decode(file_get_contents($manifestPath), true);
        $manifest = is_array($decoded) ? $decoded : null;
    }

    $cssEntry = $manifest['resources/css/app.css']['file'] ?? null;
    $jsEntry = $manifest['resources/js/app.js']['file'] ?? null;
@endphp

@if ($cssEntry)
    <link rel="stylesheet" href="{{ asset('build/' . $cssEntry) }}">
@else
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endif

@if ($jsEntry)
    <script type="module" src="{{ asset('build/' . $jsEntry) }}"></script>
@else
    <script src="{{ asset('js/app.js') }}" defer></script>
@endif
