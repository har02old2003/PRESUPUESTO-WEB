@extends('layouts.app')

@section('title', 'Presupuestos - HAXX COORP')
@section('page_title', 'Presupuestos')
@section('page_subtitle', 'Define límites mensuales y controla tu ritmo financiero')

@section('content')
    <section class="glass-card section-block" style="margin-bottom:.8rem;">
        <h2 class="section-title" style="margin-bottom:.7rem;">Crear o actualizar presupuesto</h2>
        <form method="post" action="{{ route('budgets.store') }}" class="grid-3">
            @csrf
            <div class="field">
                <label for="month_key">Mes</label>
                <input type="month" id="month_key" name="month_key" class="input" value="{{ old('month_key', $defaultMonth) }}" required>
            </div>

            <div class="field">
                <label for="amount">Monto objetivo</label>
                <input type="number" id="amount" name="amount" step="0.01" min="1" class="input" required>
            </div>

            <div style="display:flex; align-items:flex-end;">
                <button class="btn btn-primary" type="submit" style="width:100%;">Guardar presupuesto</button>
            </div>
        </form>
    </section>

    <section class="glass-card section-block" style="margin-bottom:.8rem;">
        <h2 class="section-title" style="margin-bottom:.7rem;">Meta semanal</h2>
        <form method="post" action="{{ route('budgets.weekly.store') }}" class="grid-3">
            @csrf
            <div class="field">
                <label for="week_start">Semana (inicio)</label>
                <input type="date" id="week_start" name="week_start" class="input" value="{{ old('week_start', $defaultWeekStart) }}" required>
            </div>

            <div class="field">
                <label for="weekly_amount">Monto meta semanal</label>
                <input type="number" id="weekly_amount" name="amount" step="0.01" min="1" class="input" required>
            </div>

            <div style="display:flex; align-items:flex-end;">
                <button class="btn btn-primary" type="submit" style="width:100%;">Guardar meta semanal</button>
            </div>
        </form>

        <div style="margin-top:.9rem;">
            <h3 class="section-title" style="margin:0 0 .55rem; font-size:1rem;">Historial semanal</h3>
            @if ($weeklyGoals->isEmpty())
                <p class="muted" style="margin:0;">Aún no tienes metas semanales registradas.</p>
            @else
                <div class="soft-list">
                    @foreach ($weeklyGoals as $weeklyGoal)
                        <article class="soft-item">
                            <div class="soft-item-body">
                                <p class="soft-item-title">
                                        Semana {{ \Carbon\Carbon::parse($weeklyGoal->week_start)->translatedFormat('d M') }}
                                        - {{ \Carbon\Carbon::parse($weeklyGoal->week_start)->addDays(6)->translatedFormat('d M Y') }}
                                </p>
                                <p class="soft-item-sub">S/ {{ number_format((float) $weeklyGoal->amount, 2) }}</p>
                            </div>
                            <div style="display:flex; gap:.48rem; align-items:center;">
                                <span class="amount-pill expense">Meta</span>
                                <form method="post" action="{{ route('budgets.weekly.destroy', $weeklyGoal) }}" data-confirm data-confirm-message="¿Eliminar esta meta semanal? Esta accion no se puede deshacer.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit" style="padding:.42rem .62rem; font-size:.76rem;">Eliminar</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="glass-card section-block">
        <h2 class="section-title" style="margin:0 0 .7rem;">Historial de presupuestos</h2>

        @if ($budgets->isEmpty())
            <p class="muted" style="margin:0;">Aún no tienes presupuestos registrados.</p>
        @else
            <div class="soft-list">
                @foreach ($budgets as $budget)
                    <article class="soft-item">
                        <div class="soft-item-body">
                            <p class="soft-item-title">{{ \Carbon\Carbon::createFromFormat('Y-m', $budget->month_key)->translatedFormat('F Y') }}</p>
                            <p class="soft-item-sub">S/ {{ number_format((float) $budget->amount, 2) }}</p>
                        </div>
                        <div style="display:flex; gap:.48rem; align-items:center;">
                            <span class="amount-pill income">Presupuesto</span>
                            <form method="post" action="{{ route('budgets.destroy', $budget) }}" data-confirm data-confirm-message="¿Eliminar este presupuesto? Esta accion no se puede deshacer.">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit" style="padding:.42rem .62rem; font-size:.76rem;">Eliminar</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top:.8rem;">
                {{ $budgets->links() }}
            </div>
        @endif
    </section>
@endsection
