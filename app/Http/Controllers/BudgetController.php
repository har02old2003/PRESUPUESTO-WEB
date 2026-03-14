<?php

namespace App\Http\Controllers;

use App\Models\MonthlyBudget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $budgets = MonthlyBudget::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('month_key')
            ->paginate(12);

        return view('budgets.index', [
            'budgets' => $budgets,
            'defaultMonth' => now()->format('Y-m'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'month_key' => ['required', 'regex:/^\\d{4}-(0[1-9]|1[0-2])$/'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999.99'],
        ]);

        MonthlyBudget::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'month_key' => $data['month_key'],
            ],
            [
                'amount' => $data['amount'],
            ]
        );

        return back()->with('success', 'Presupuesto guardado correctamente.');
    }

    public function destroy(Request $request, MonthlyBudget $budget): RedirectResponse
    {
        abort_unless($budget->user_id === $request->user()->id, 403);

        $budget->delete();

        return back()->with('success', 'Presupuesto eliminado.');
    }
}
