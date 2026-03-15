@extends('layouts.app')

@section('title', 'Historial - HAXX COORP')
@section('page_title', 'Historial mensual')
@section('page_subtitle', 'Administra ingresos y gastos con control completo del mes.')

@section('content')
    <section class="glass-card section-block" style="margin-bottom:.8rem;">
        <form method="get" class="form-inline">
            <div class="field form-grow" style="max-width:220px; margin:0;">
                <label for="month">Filtrar por mes</label>
                <input type="month" id="month" name="month" class="input" value="{{ $month }}">
            </div>
            <button class="btn btn-outline" type="submit">Filtrar</button>
        </form>
    </section>

    <section class="glass-card section-block">
        <div class="section-header">
            <div>
                <h2 class="section-title">Movimientos del mes</h2>
                <p class="section-note">Registros de {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</p>
            </div>
        </div>

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
                                    <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}" style="white-space:nowrap;">
                                        {{ $transaction->type === 'income' ? '+' : '-' }}S/ {{ number_format((float) $transaction->amount, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <form action="{{ route('transactions.destroy', $transaction) }}" method="post" data-confirm data-confirm-message="¿Eliminar este movimiento? Esta accion no se puede deshacer.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit" style="padding:.42rem .62rem; font-size:.76rem; white-space:nowrap;">Eliminar</button>
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
