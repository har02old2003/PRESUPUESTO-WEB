<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HAXX COORP</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icono-haxx.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icono-haxx.png') }}">
    @include('partials.assets')
    <style>
        /* Fallback critico: evita modal sin estilos si hay cache/build viejo */
        .quick-modal {
            position: fixed;
            inset: 0;
            z-index: 130;
            display: grid;
            place-items: center;
            padding: 1rem;
            background: rgba(2, 8, 22, 0.78);
            backdrop-filter: blur(8px);
        }

        .quick-modal[hidden] {
            display: none !important;
        }

        .quick-modal-card {
            width: min(520px, 100%) !important;
            max-height: min(88vh, 760px);
            overflow: auto;
            padding: 1.2rem !important;
            border-radius: 20px !important;
        }

        .quick-form {
            display: grid !important;
            gap: .68rem !important;
        }

        .quick-label {
            margin: 0;
            color: var(--muted);
            font-size: .79rem;
            font-weight: 760;
        }

        .quick-input {
            width: 100% !important;
            padding: .7rem .85rem !important;
            border-radius: 12px !important;
            border: 1px solid rgba(121, 157, 236, .34) !important;
            background: rgba(4, 12, 29, .8) !important;
            color: var(--text) !important;
            outline: none;
        }

        .nav-plus-btn {
            width: 2.95rem;
            height: 2.95rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.32);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 1.72rem;
            font-weight: 900;
            line-height: 1;
            cursor: pointer;
            background:
                radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.3), transparent 42%),
                linear-gradient(160deg, rgba(255, 116, 116, 0.95), rgba(162, 30, 30, 0.95));
        }

        .nav-plus-btn span {
            transform: translateY(-2px);
        }

        .nav-plus-desktop {
            position: absolute;
            left: calc(100% - 1.48rem);
            top: calc(50% - 1.34rem);
            z-index: 12;
        }

        @media (max-width: 1024px) {
            .nav-plus-desktop {
                display: none !important;
            }

            .mobile-plus-btn {
                display: grid !important;
                place-items: center;
                width: 2.92rem;
                height: 2.92rem;
                margin-top: -0.95rem;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: #fff;
                font-size: 1.72rem;
                font-weight: 900;
                line-height: 1;
                background:
                    radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.3), transparent 42%),
                    linear-gradient(160deg, rgba(255, 116, 116, 0.95), rgba(162, 30, 30, 0.95));
            }
        }
    </style>
</head>
<body data-limit-alerts='@json($limitAlerts ?? [])' data-auth-user="{{ auth()->id() }}">
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
                <button type="button" class="nav-plus-btn nav-plus-desktop" id="openQuickTxDesktop" aria-label="Agregar movimiento">
                    <span>+</span>
                </button>
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
        <button type="button" class="mobile-plus-btn" id="openQuickTxMobile" aria-label="Agregar movimiento"><span>+</span></button>
        <a href="{{ route('budgets.index') }}" class="{{ str_starts_with($routeName, 'budgets') ? 'active' : '' }}">Presupuesto</a>
        <a href="{{ route('settings.index') }}" class="{{ str_starts_with($routeName, 'settings') || str_starts_with($routeName, 'exports') ? 'active' : '' }}">Ajustes</a>
    </nav>

    <div class="confirm-modal" data-confirm-modal hidden>
        <div class="glass-card confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
            <h3 id="confirm-title" class="confirm-title">Confirmar eliminación</h3>
            <p class="confirm-message" data-confirm-message>¿Deseas eliminar este registro?</p>
            <div class="confirm-actions">
                <button class="btn btn-outline" type="button" data-confirm-cancel>Cancelar</button>
                <button class="btn btn-danger-solid" type="button" data-confirm-accept>Eliminar</button>
            </div>
        </div>
    </div>

    <div id="quickTxModal" class="quick-modal" hidden aria-hidden="true">
        <div class="glass-card quick-modal-card" role="dialog" aria-modal="true" aria-labelledby="quickTxTitle">
            <div class="quick-modal-head">
                <h3 id="quickTxTitle" class="quick-modal-title">Agregar movimiento</h3>
                <button type="button" id="closeQuickTx" class="quick-close" aria-label="Cerrar">&times;</button>
            </div>

            <form method="POST" action="{{ route('transactions.store') }}" class="quick-form" id="quickTxForm">
                @csrf

                <p class="quick-label">Tipo</p>
                <div class="quick-type-group">
                    <label class="quick-type-option">
                        <input type="radio" name="type" value="expense" checked>
                        <span>Gasto</span>
                    </label>
                    <label class="quick-type-option">
                        <input type="radio" name="type" value="income">
                        <span>Ingreso</span>
                    </label>
                </div>

                <label class="quick-label" for="quick_amount">Monto</label>
                <div class="quick-amount-wrap">
                    <span class="quick-currency">S/</span>
                    <input id="quick_amount" type="number" name="amount" step="0.01" min="0.01" class="quick-input" required>
                </div>

                <label class="quick-label" for="quick_date">Fecha</label>
                <input id="quick_date" type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}" class="quick-input" required>

                <label class="quick-label" for="quick_category">Categoría</label>
                <select id="quick_category" name="category" class="quick-input" required>
                    <option value="Alimentacion">Alimentacion</option>
                    <option value="Transporte">Transporte</option>
                    <option value="Servicios">Servicios</option>
                    <option value="Ocio">Ocio</option>
                    <option value="Salud">Salud</option>
                    <option value="Ropa">Ropa</option>
                    <option value="Educacion">Educacion</option>
                    <option value="Otros">Otros</option>
                </select>

                <label class="quick-label" for="quick_note">Nota</label>
                <textarea id="quick_note" name="note" rows="3" class="quick-input"></textarea>

                <button type="submit" class="btn btn-primary quick-submit" id="quickSubmitBtn">Guardar movimiento</button>
            </form>
        </div>
    </div>
</body>
</html>
