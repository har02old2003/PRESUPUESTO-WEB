@extends('layouts.app')

@section('title', 'Inicio - HAXX COORP')
@section('page_title', 'Panel financiero')
@section('page_subtitle', 'Resumen mensual de tus finanzas con enfoque rápido y claro')

@section('content')
    <section class="glass-card section-block" style="margin-bottom:.86rem;">
        <form method="get" class="form-inline">
            <div class="field form-grow" style="max-width:240px; margin:0;">
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

    <section class="grid-4" style="margin-bottom:.86rem;">
        <article class="glass-card stat-card section-block">
            <p class="stat-label">Balance disponible</p>
            <p class="stat-value stat-money">S/ {{ number_format($balance, 2) }}</p>
        </article>
        <article class="glass-card stat-card section-block">
            <p class="stat-label">Ingresos</p>
            <p class="stat-value stat-money text-success">+S/ {{ number_format($income, 2) }}</p>
        </article>
        <article class="glass-card stat-card section-block">
            <p class="stat-label">Gastos</p>
            <p class="stat-value stat-money text-danger">-S/ {{ number_format($expense, 2) }}</p>
        </article>
        <article class="glass-card stat-card section-block">
            <p class="stat-label">Uso de presupuesto</p>
            <p class="stat-value">{{ number_format($usagePercent, 0) }}%</p>
            <p class="muted" style="margin:.24rem 0 0; font-size:.82rem;">
                @if ($budgetAmount > 0)
                    S/ {{ number_format($expense, 2) }} de S/ {{ number_format($budgetAmount, 2) }}
                @else
                    Sin presupuesto configurado
                @endif
            </p>
        </article>
    </section>

    <section class="glass-card section-block" style="margin-bottom:.86rem;">
        <h2 class="section-title">Meta semanal</h2>
        <p class="section-note" style="margin-bottom:.68rem;">
            Semana: {{ $currentWeekStart->translatedFormat('d M') }} - {{ $currentWeekEnd->translatedFormat('d M Y') }}
        </p>
        @if ($weeklyGoalAmount > 0)
            <div style="display:flex; flex-wrap:wrap; gap:.95rem; align-items:baseline; margin-top:.1rem;">
                <p style="margin:0;"><strong>Gasto semanal:</strong> <span class="text-danger">S/ {{ number_format($weeklyExpense, 2) }}</span></p>
                <p style="margin:0;"><strong>Meta:</strong> S/ {{ number_format($weeklyGoalAmount, 2) }}</p>
                <p style="margin:0;"><strong>Avance:</strong> {{ number_format($weeklyUsagePercent, 0) }}%</p>
            </div>
        @else
            <p class="muted" style="margin:0 0 .4rem;">Aun no definiste meta semanal para la semana actual.</p>
        @endif
        <a href="{{ route('budgets.index') }}" class="auth-link" style="display:inline-block; margin-top:.55rem;">Configurar meta semanal</a>
    </section>

    <section class="grid-2" style="margin-bottom:.86rem;">
        <article class="glass-card section-block">
            <h2 class="section-title">Gastos por categoría</h2>
            @if ($categoryBreakdown->isEmpty())
                <p class="muted" style="margin:0;">Aún no hay gastos en este mes.</p>
            @else
                <div class="soft-list">
                    @foreach ($categoryBreakdown as $item)
                        <article class="soft-item">
                            <div class="soft-item-body">
                                <p class="soft-item-title">{{ $item['category'] }}</p>
                                <p class="soft-item-sub">Categoría de gasto</p>
                            </div>
                            <span class="amount-pill expense">S/ {{ number_format($item['total'], 2) }}</span>
                        </article>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="glass-card section-block">
            <h2 class="section-title">Movimientos recientes</h2>
            @if ($recentTransactions->isEmpty())
                <p class="muted" style="margin:0;">No tienes movimientos recientes.</p>
            @else
                <div class="soft-list">
                    @foreach ($recentTransactions as $row)
                        <article class="soft-item">
                            <div class="soft-item-body">
                                <p class="soft-item-title">{{ $row->category }}</p>
                                <p class="soft-item-sub">{{ \Carbon\Carbon::parse($row->transaction_date)->translatedFormat('d M Y') }}</p>
                            </div>
                            <span class="amount-pill {{ $row->type === 'income' ? 'income' : 'expense' }}">
                                {{ $row->type === 'income' ? '+' : '-' }}S/ {{ number_format((float) $row->amount, 2) }}
                            </span>
                        </article>
                    @endforeach
                </div>
            @endif
            <a href="{{ route('transactions.index') }}" class="auth-link" style="display:inline-block; margin-top:.6rem;">Ver historial completo</a>
        </article>
    </section>
@endsection
