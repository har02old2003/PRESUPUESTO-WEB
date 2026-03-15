@php
    $cssPath = public_path('css/app.css');
    $jsPath = public_path('js/app.js');
    $cssVersion = is_file($cssPath) ? filemtime($cssPath) : time();
    $jsVersion = is_file($jsPath) ? filemtime($jsPath) : time();
@endphp

<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ $cssVersion }}">
<script src="{{ asset('js/app.js') }}?v={{ $jsVersion }}" defer></script>
