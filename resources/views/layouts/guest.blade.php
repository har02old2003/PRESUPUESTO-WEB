<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HAXX COORP</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icono-haxx.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icono-haxx.png') }}">
    @include('partials.assets')
</head>
<body>
    <div class="auth-shell">
        <div class="glass-card auth-card">
            <div class="brand-line">
                <img src="{{ asset('images/icono-haxx.png') }}" alt="HAXX">
                <div>
                    <p class="brand-text">HAXX COORP</p>
                    <p style="margin:0; color:var(--muted); font-size:.8rem;">Sistema financiero inteligente</p>
                </div>
            </div>

            @include('partials.flash')

            @yield('content')
        </div>
    </div>
</body>
</html>
