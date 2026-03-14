<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private array $categories = [
        'Alimentacion',
        'Transporte',
        'Servicios',
        'Ocio',
        'Salud',
        'Ropa',
        'Educacion',
        'Otros',
    ];

    public function index(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));
        if (! preg_match('/^\\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $month = now()->format('Y-m');
        }

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $transactions = Transaction::query()
            ->where('user_id', $request->user()->id)
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
            'month' => $month,
            'categories' => $this->categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'category' => ['required', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        Transaction::create([
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'category' => trim($data['category']),
            'note' => isset($data['note']) ? trim((string) $data['note']) : null,
            'transaction_date' => $data['transaction_date'],
        ]);

        return back()->with('success', 'Movimiento guardado correctamente.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);

        $transaction->delete();

        return back()->with('success', 'Movimiento eliminado.');
    }
}
