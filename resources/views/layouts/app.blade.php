<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HAXX COORP')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php($routeName = request()->route()?->getName() ?? '')
    <div class="app-layout">
        <aside class="sidebar">
            <div class="brand-line">
                <img src="{{ asset('images/icono-haxx.png') }}" alt="HAXX">
                <div>
                    <p class="brand-text">HAXX COORP</p>
                    <p style="margin:0; color:var(--muted); font-size:.78rem;">Control financiero</p>
                </div>
            </div>

            <nav class="menu">
                <a href="{{ route('dashboard') }}" class="{{ str_starts_with($routeName, 'dashboard') ? 'active' : '' }}">Inicio</a>
                <a href="{{ route('transactions.index') }}" class="{{ str_starts_with($routeName, 'transactions') ? 'active' : '' }}">Historial</a>
                <a href="{{ route('budgets.index') }}" class="{{ str_starts_with($routeName, 'budgets') ? 'active' : '' }}">Presupuestos</a>
                <a href="{{ route('settings.index') }}" class="{{ str_starts_with($routeName, 'settings') || str_starts_with($routeName, 'exports') ? 'active' : '' }}">Ajustes</a>
            </nav>

            <form action="{{ route('logout') }}" method="post" style="margin-top:1rem;">
                @csrf
                <button class="btn btn-outline" type="submit" style="width:100%;">Cerrar sesión</button>
            </form>
        </aside>

        <main class="main">
            <div class="container">
                <header class="topbar">
                    <div>
                        <h1 class="page-title">@yield('page_title', 'Panel')</h1>
                        <p class="page-subtitle">@yield('page_subtitle', 'Gestiona tus finanzas en tiempo real')</p>
                    </div>
                    <span class="badge">{{ auth()->user()->username }}</span>
                </header>

                @include('partials.flash')

                @yield('content')
            </div>
        </main>
    </div>

    <nav class="mobile-nav">
        <a href="{{ route('dashboard') }}" class="{{ str_starts_with($routeName, 'dashboard') ? 'active' : '' }}">Inicio</a>
        <a href="{{ route('transactions.index') }}" class="{{ str_starts_with($routeName, 'transactions') ? 'active' : '' }}">Historial</a>
        <a href="{{ route('budgets.index') }}" class="{{ str_starts_with($routeName, 'budgets') ? 'active' : '' }}">Presupuesto</a>
        <a href="{{ route('settings.index') }}" class="{{ str_starts_with($routeName, 'settings') || str_starts_with($routeName, 'exports') ? 'active' : '' }}">Ajustes</a>
    </nav>
</body>
</html>
