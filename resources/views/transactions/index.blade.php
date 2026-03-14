@extends('layouts.app')

@section('title', 'Historial - HAXX COORP')
@section('page_title', 'Historial mensual')
@section('page_subtitle', 'Registra y administra ingresos/gastos con fecha y categoría')

@section('content')
    <section class="glass-card" style="padding:1rem; margin-bottom:.8rem;">
        <form method="get" style="display:flex; gap:.6rem; align-items:flex-end; flex-wrap:wrap;">
            <div class="field" style="max-width:220px; margin:0;">
                <label for="month">Filtrar por mes</label>
                <input type="month" id="month" name="month" class="input" value="{{ $month }}">
            </div>
            <button class="btn btn-outline" type="submit">Filtrar</button>
        </form>
    </section>

    <section class="glass-card" style="padding:1rem; margin-bottom:.8rem;">
        <h2 style="margin:0 0 .7rem; font-size:1.05rem;">Nuevo movimiento</h2>
        <form method="post" action="{{ route('transactions.store') }}" class="grid-3">
            @csrf
            <div class="field">
                <label for="type">Tipo</label>
                <select id="type" name="type" class="select" required>
                    <option value="expense">Gasto</option>
                    <option value="income">Ingreso</option>
                </select>
            </div>

            <div class="field">
                <label for="amount">Monto</label>
                <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="input" required>
            </div>

            <div class="field">
                <label for="transaction_date">Fecha</label>
                <input type="date" id="transaction_date" name="transaction_date" class="input" value="{{ now()->toDateString() }}" required>
            </div>

            <div class="field">
                <label for="category">Categoría</label>
                <select id="category" name="category" class="select" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field" style="grid-column:1 / -1;">
                <label for="note">Nota (opcional)</label>
                <input id="note" name="note" class="input" maxlength="255" placeholder="¿En qué fue usado?">
            </div>

            <div style="grid-column:1 / -1; display:flex; justify-content:flex-end;">
                <button class="btn btn-primary" type="submit">Guardar movimiento</button>
            </div>
        </form>
    </section>

    <section class="glass-card" style="padding:1rem;">
        <h2 style="margin:0 0 .7rem; font-size:1.05rem;">Movimientos del mes</h2>

        @if ($transactions->isEmpty())
            <p class="muted" style="margin:0;">No se encontraron movimientos en este mes.</p>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Categoría</th>
                            <th>Nota</th>
                            <th>Monto</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                    </span>
                                </td>
                                <td>{{ $transaction->category }}</td>
                                <td class="muted">{{ $transaction->note ?: 'Sin nota' }}</td>
                                <td>
                                    <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }}S/ {{ number_format((float) $transaction->amount, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <form action="{{ route('transactions.destroy', $transaction) }}" method="post" onsubmit="return confirm('¿Eliminar este movimiento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit" style="padding:.42rem .62rem; font-size:.76rem;">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:.8rem;">
                {{ $transactions->links() }}
            </div>
        @endif
    </section>
@endsection
