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
    <div class="onboarding-shell">
        <section class="glass-card onboarding-card" data-slider data-total="3">
            <div class="brand-line">
                <img src="{{ asset('images/icono-haxx.png') }}" alt="HAXX">
                <div>
                    <p class="brand-text">HAXX COORP</p>
                    <p style="margin:0; color:var(--muted); font-size:.82rem;">Guía rápida del sistema</p>
                </div>
            </div>

            <div class="slider">
                <div class="slides">
                    <article class="slide">
                        <h3>1. Panel de inicio</h3>
                        <p>
                            Verás tu balance mensual, ingresos, gastos y movimientos recientes en tiempo real.
                            Cambia de mes para revisar histórico sin perder velocidad.
                        </p>
                    </article>
                    <article class="slide">
                        <h3>2. Movimientos y presupuesto</h3>
                        <p>
                            Registra gastos/ingresos con fecha y categoría. Luego define presupuesto por mes para
                            controlar porcentaje consumido y evitar sobrecostos.
                        </p>
                    </article>
                    <article class="slide">
                        <h3>3. Respaldo y reportes</h3>
                        <p>
                            Desde ajustes puedes exportar PDF y CSV con tu información. El sistema está optimizado
                            para móvil y escritorio, ideal para usar en iPhone desde marcador.
                        </p>
                    </article>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; gap:.6rem;">
                <div class="progress-dots">
                    <span data-dot class="active"></span>
                    <span data-dot></span>
                    <span data-dot></span>
                </div>
            </div>

            <div class="slide-nav">
                <button class="btn btn-outline" type="button" data-prev>Anterior</button>
                <button class="btn btn-primary" type="button" data-next>Siguiente</button>
                <form method="post" action="{{ route('onboarding.complete') }}" style="margin:0;" data-finish hidden>
                    @csrf
                    <button class="btn btn-primary" type="submit">Entrar al sistema</button>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
