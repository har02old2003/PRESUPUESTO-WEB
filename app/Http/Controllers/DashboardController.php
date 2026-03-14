<?php

namespace App\Http\Controllers;

use App\Models\MonthlyBudget;
use App\Models\Transaction;
use App\Models\WeeklyGoal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));
        if (! preg_match('/^\\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $month = now()->format('Y-m');
        }

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        /** @var \App\Models\User $user */
        $user = $request->user();

        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        $income = (float) $transactions->where('type', 'income')->sum('amount');
        $expense = (float) $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        /** @var Collection<int, array{category:string, total:float}> $categoryBreakdown */
        $categoryBreakdown = $transactions
            ->where('type', 'expense')
            ->groupBy('category')
            ->map(fn (Collection $rows, string $category) => [
                'category' => $category,
                'total' => (float) $rows->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values();

        $budgetAmount = (float) MonthlyBudget::query()
            ->where('user_id', $user->id)
            ->where('month_key', $month)
            ->value('amount');

        $usagePercent = $budgetAmount > 0 ? min(100, ($expense / $budgetAmount) * 100) : 0;

        $currentWeekStart = now()->startOfWeek(Carbon::MONDAY);
        $currentWeekEnd = now()->endOfWeek(Carbon::SUNDAY);
        $weeklyExpense = (float) Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$currentWeekStart->toDateString(), $currentWeekEnd->toDateString()])
            ->sum('amount');

        $weeklyGoalAmount = (float) WeeklyGoal::query()
            ->where('user_id', $user->id)
            ->whereDate('week_start', $currentWeekStart->toDateString())
            ->value('amount');

        $weeklyUsagePercent = $weeklyGoalAmount > 0 ? min(100, ($weeklyExpense / $weeklyGoalAmount) * 100) : 0;

        $months = collect(range(0, 5))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset)->format('Y-m'))
            ->all();

        return view('dashboard.index', [
            'month' => $month,
            'months' => $months,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'budgetAmount' => $budgetAmount,
            'usagePercent' => $usagePercent,
            'currentWeekStart' => $currentWeekStart,
            'currentWeekEnd' => $currentWeekEnd,
            'weeklyGoalAmount' => $weeklyGoalAmount,
            'weeklyExpense' => $weeklyExpense,
            'weeklyUsagePercent' => $weeklyUsagePercent,
            'recentTransactions' => $transactions->take(8),
            'categoryBreakdown' => $categoryBreakdown,
        ]);
    }
}
