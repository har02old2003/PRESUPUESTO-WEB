<?php

namespace Tests\Feature;

use App\Models\MonthlyBudget;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WeeklyGoal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_auth_pages_load(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_register_creates_account_and_redirects_to_verification_notice(): void
    {
        $response = $this->post(route('register.store'), [
            'display_name' => 'Harold',
            'username' => 'HAXX',
            'email' => 'harold@example.com',
            'password' => 'Passw0rd!23',
            'password_confirmation' => 'Passw0rd!23',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'harold@example.com',
            'username' => 'haxx',
        ]);
    }

    public function test_login_with_username_works(): void
    {
        $user = User::factory()->create([
            'username' => 'haxxuser',
            'password' => 'Passw0rd!23',
            'email_verified_at' => now(),
            'credential_setup_completed' => true,
            'onboarding_completed' => true,
        ]);

        $this->post(route('login.attempt'), [
            'login' => $user->username,
            'password' => 'Passw0rd!23',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_transactions_budgets_settings_and_exports_work(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'credential_setup_completed' => true,
            'onboarding_completed' => true,
            'password' => 'Passw0rd!23',
        ]);

        $this->actingAs($user);

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('transactions.index'))->assertOk();
        $this->get(route('budgets.index'))->assertOk();
        $this->get(route('settings.index'))->assertOk();

        $this->post(route('transactions.store'), [
            'type' => 'expense',
            'amount' => '25.50',
            'category' => 'Servicios',
            'note' => 'Pago internet',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect();

        $transaction = Transaction::query()->firstOrFail();

        $this->delete(route('transactions.destroy', $transaction))
            ->assertRedirect();

        $this->post(route('budgets.store'), [
            'month_key' => now()->format('Y-m'),
            'amount' => '800.00',
        ])->assertRedirect();

        $budget = MonthlyBudget::query()->firstOrFail();

        $this->delete(route('budgets.destroy', $budget))
            ->assertRedirect();

        $this->post(route('budgets.weekly.store'), [
            'week_start' => now()->startOfWeek()->toDateString(),
            'amount' => '300.00',
        ])->assertRedirect();

        $weeklyGoal = WeeklyGoal::query()->firstOrFail();

        $this->delete(route('budgets.weekly.destroy', $weeklyGoal))
            ->assertRedirect();

        $this->get(route('exports.csv'))->assertOk();
        $this->get(route('exports.pdf'))->assertOk();
    }

    public function test_onboarding_complete_button_works(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'credential_setup_completed' => true,
            'onboarding_completed' => false,
        ]);

        $this->actingAs($user);

        $this->get(route('onboarding.show'))->assertOk();

        $this->post(route('onboarding.complete'))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'onboarding_completed' => 1,
        ]);
    }

    public function test_logout_button_works(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_verification_resend_button_works_without_500(): void
    {
        $user = User::factory()->unverified()->create([
            'credential_setup_completed' => true,
            'onboarding_completed' => false,
        ]);

        $this->actingAs($user);

        $this->get(route('verification.notice'))->assertOk();

        $this->post(route('verification.send'))
            ->assertRedirect();
    }
}
