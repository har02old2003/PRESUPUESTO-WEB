<?php

namespace App\Http\Controllers;

use App\Models\MonthlyBudget;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return view('settings.index', [
            'user' => $request->user(),
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        $budgets = MonthlyBudget::query()
            ->where('user_id', $user->id)
            ->orderByDesc('month_key')
            ->get();

        $fileName = 'haxx-reporte-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($transactions, $budgets): void {
            $handle = fopen('php://output', 'w');
            if (! $handle) {
                return;
            }

            fputcsv($handle, ['HAXX COORP - MOVIMIENTOS']);
            fputcsv($handle, ['Tipo', 'Monto', 'Categoria', 'Fecha', 'Nota']);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction->type,
                    $transaction->amount,
                    $transaction->category,
                    $transaction->transaction_date,
                    $transaction->note,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['HAXX COORP - PRESUPUESTOS']);
            fputcsv($handle, ['Mes', 'Monto']);

            foreach ($budgets as $budget) {
                fputcsv($handle, [$budget->month_key, $budget->amount]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = $request->user();

        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(150)
            ->get();

        $budgets = MonthlyBudget::query()
            ->where('user_id', $user->id)
            ->orderByDesc('month_key')
            ->limit(24)
            ->get();

        $income = (float) $transactions->where('type', 'income')->sum('amount');
        $expense = (float) $transactions->where('type', 'expense')->sum('amount');

        $pdf = Pdf::loadView('exports.pdf', [
            'user' => $user,
            'transactions' => $transactions,
            'budgets' => $budgets,
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'logoDataUri' => $this->toDataUri(public_path('images/icono-haxx.png')),
            'brandDataUri' => $this->toDataUri(public_path('images/fondo-haxx.png')),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('haxx-reporte-'.now()->format('Ymd-His').'.pdf');
    }

    private function toDataUri(string $path): ?string
    {
        if (! file_exists($path)) {
            return null;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($content);
    }
}
