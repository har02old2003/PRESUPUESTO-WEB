@extends('layouts.app')

@section('title', 'Inicio - HAXX COORP')
@section('page_title', 'Panel financiero')
@section('page_subtitle', 'Resumen mensual de tus finanzas con enfoque rápido y claro')

@section('content')
    <section class="glass-card" style="padding:1rem; margin-bottom:.8rem;">
        <form method="get" style="display:flex; gap:.6rem; flex-wrap:wrap; align-items:flex-end;">
            <div class="field" style="max-width:220px; margin:0;">
                <label for="month">Mes a analizar</label>
                <select id="month" name="month" class="select">
                    @foreach ($months as $monthOption)
                        <option value="{{ $monthOption }}" @selected($monthOption === $month)>
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $monthOption)->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-outline" type="submit">Actualizar</button>
        </form>
    </section>

    <section class="grid-4" style="margin-bottom:.8rem;">
        <article class="glass-card" style="padding:1rem;">
            <p class="stat-label">Balance disponible</p>
            <p class="stat-value">S/ {{ number_format($balance, 2) }}</p>
        </article>
        <article class="glass-card" style="padding:1rem;">
            <p class="stat-label">Ingresos</p>
            <p class="stat-value text-success">+S/ {{ number_format($income, 2) }}</p>
        </article>
        <article class="glass-card" style="padding:1rem;">
            <p class="stat-label">Gastos</p>
            <p class="stat-value text-danger">-S/ {{ number_format($expense, 2) }}</p>
        </article>
        <article class="glass-card" style="padding:1rem;">
            <p class="stat-label">Uso de presupuesto</p>
            <p class="stat-value">{{ number_format($usagePercent, 0) }}%</p>
            <p class="muted" style="margin:.2rem 0 0; font-size:.8rem;">
                @if ($budgetAmount > 0)
                    S/ {{ number_format($expense, 2) }} de S/ {{ number_format($budgetAmount, 2) }}
                @else
                    Sin presupuesto configurado
                @endif
            </p>
        </article>
    </section>

    <section class="grid-2" style="margin-bottom:.8rem;">
        <article class="glass-card" style="padding:1rem;">
            <h2 style="margin:0 0 .55rem; font-size:1.08rem;">Gastos por categoría</h2>
            @if ($categoryBreakdown->isEmpty())
                <p class="muted" style="margin:0;">Aún no hay gastos en este mes.</p>
            @else
                <div style="display:grid; gap:.45rem;">
                    @foreach ($categoryBreakdown as $item)
                        <div style="display:flex; justify-content:space-between; gap:.5rem;">
                            <span>{{ $item['category'] }}</span>
                            <strong>S/ {{ number_format($item['total'], 2) }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="glass-card" style="padding:1rem;">
            <h2 style="margin:0 0 .55rem; font-size:1.08rem;">Movimientos recientes</h2>
            @if ($recentTransactions->isEmpty())
                <p class="muted" style="margin:0;">No tienes movimientos recientes.</p>
            @else
                <div style="display:grid; gap:.5rem;">
                    @foreach ($recentTransactions as $row)
                        <div style="display:flex; justify-content:space-between; gap:.5rem; border-bottom:1px solid rgba(117,155,255,.16); padding-bottom:.4rem;">
                            <div>
                                <strong>{{ $row->category }}</strong>
                                <p class="muted" style="margin:0; font-size:.78rem;">{{ \Carbon\Carbon::parse($row->transaction_date)->translatedFormat('d M Y') }}</p>
                            </div>
                            <strong class="{{ $row->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $row->type === 'income' ? '+' : '-' }}S/ {{ number_format((float) $row->amount, 2) }}
                            </strong>
                        </div>
                    @endforeach
                </div>
            @endif
            <a href="{{ route('transactions.index') }}" class="auth-link" style="display:inline-block; margin-top:.6rem;">Ver historial completo</a>
        </article>
    </section>
@endsection
