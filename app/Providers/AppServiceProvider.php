<?php

namespace App\Providers;

use App\Models\MonthlyBudget;
use App\Models\Transaction;
use App\Models\WeeklyGoal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        View::composer('layouts.app', function ($view): void {
            $user = Auth::user();
            if (! $user) {
                $view->with('limitAlerts', []);

                return;
            }

            $alerts = [];

            $monthKey = now()->format('Y-m');
            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd = now()->endOfMonth()->toDateString();

            $monthlyBudget = (float) MonthlyBudget::query()
                ->where('user_id', $user->id)
                ->where('month_key', $monthKey)
                ->value('amount');
            $monthlyExpense = (float) Transaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            if ($monthlyBudget > 0 && $monthlyExpense > $monthlyBudget) {
                $alerts[] = [
                    'key' => 'monthly-'.$monthKey,
                    'title' => 'HAXX COORP - Limite mensual superado',
                    'body' => 'Tus gastos del mes (S/ '.number_format($monthlyExpense, 2).') superaron tu limite mensual (S/ '.number_format($monthlyBudget, 2).').',
                ];
            }

            $weekStartCarbon = now()->startOfWeek(Carbon::MONDAY);
            $weekEndCarbon = now()->endOfWeek(Carbon::SUNDAY);
            $weekKey = $weekStartCarbon->format('Y-m-d');

            $weeklyGoal = (float) WeeklyGoal::query()
                ->where('user_id', $user->id)
                ->whereDate('week_start', $weekStartCarbon->toDateString())
                ->value('amount');
            $weeklyExpense = (float) Transaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$weekStartCarbon->toDateString(), $weekEndCarbon->toDateString()])
                ->sum('amount');

            if ($weeklyGoal > 0 && $weeklyExpense > $weeklyGoal) {
                $alerts[] = [
                    'key' => 'weekly-'.$weekKey,
                    'title' => 'HAXX COORP - Limite semanal superado',
                    'body' => 'Tus gastos semanales (S/ '.number_format($weeklyExpense, 2).') superaron tu meta semanal (S/ '.number_format($weeklyGoal, 2).').',
                ];
            }

            $view->with('limitAlerts', $alerts);
        });
    }
}
