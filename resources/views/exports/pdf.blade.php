<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0c1733; font-size: 12px; }
        .header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .header img.logo { width: 44px; height: 44px; }
        h1 { font-size: 18px; margin: 0; }
        .muted { color: #516188; }
        .box { border: 1px solid #d2dbef; border-radius: 8px; padding: 10px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #dbe4f4; padding: 6px; text-align: left; }
        th { background: #f2f6ff; }
        .footer { margin-top: 18px; text-align: center; }
        .footer img { max-width: 300px; margin: 0 auto 6px; }
    </style>
</head>
<body>
    <div class="header">
        @if ($logoDataUri)
            <img class="logo" src="{{ $logoDataUri }}" alt="HAXX">
        @endif
        <div>
            <h1>Reporte Financiero - HAXX COORP</h1>
            <div class="muted">Usuario: {{ $user->display_name }} ({{ $user->username }}) | {{ $user->email }}</div>
            <div class="muted">Generado: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="box">
        <strong>Resumen</strong>
        <p style="margin:6px 0 0;">Ingresos: S/ {{ number_format($income, 2) }}</p>
        <p style="margin:4px 0 0;">Gastos: S/ {{ number_format($expense, 2) }}</p>
        <p style="margin:4px 0 0;">Balance: S/ {{ number_format($balance, 2) }}</p>
    </div>

    <div class="box">
        <strong>Movimientos</strong>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Categoría</th>
                    <th>Nota</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                        <td>{{ $transaction->type }}</td>
                        <td>{{ $transaction->category }}</td>
                        <td>{{ $transaction->note ?: 'Sin nota' }}</td>
                        <td>S/ {{ number_format((float) $transaction->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Sin movimientos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="box">
        <strong>Presupuestos</strong>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($budgets as $budget)
                    <tr>
                        <td>{{ $budget->month_key }}</td>
                        <td>S/ {{ number_format((float) $budget->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">Sin presupuestos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        @if ($brandDataUri)
            <img src="{{ $brandDataUri }}" alt="HAXX COORP">
        @endif
        <div class="muted">Copyright © {{ now()->year }} HAXX COORP</div>
    </div>
</body>
</html>
