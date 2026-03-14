<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Throwable;

class AuthController extends Controller
{
    private const USERNAME_REGEX = '/^[A-Za-z0-9._-]+$/';

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', 'max:120'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $login = mb_strtolower(trim($credentials['login']));
        $field = str_contains($login, '@') ? 'email' : 'username';

        $remember = (bool) ($credentials['remember'] ?? false);

        if (! Auth::attempt([$field => $login, 'password' => $credentials['password']], $remember)) {
            return back()
                ->withErrors(['login' => 'Usuario o clave incorrectos.'])
                ->withInput($request->only('login'));
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('warning', 'Confirma tu correo para ingresar.');
        }

        return redirect()->to($this->redirectAfterAuth($user));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'min:2', 'max:60'],
            'username' => ['required', 'string', 'min:3', 'max:20', 'regex:'.self::USERNAME_REGEX, Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'string', 'min:8', 'max:120'],
        ], [
            'display_name.required' => 'Ingresa tu nombre visible.',
            'username.regex' => 'El usuario solo puede contener letras, numeros, punto (.), guion (-) o guion bajo (_).',
            'username.unique' => 'Ese nombre de usuario ya esta en uso.',
            'email.email' => 'Ingresa un correo valido.',
            'email.unique' => 'Ese correo ya esta registrado. Inicia sesion o usa otro correo.',
            'password.confirmed' => 'La confirmacion de contraseña no coincide.',
        ]);

        $data['username'] = mb_strtolower(trim($data['username']));
        $data['email'] = mb_strtolower(trim($data['email']));

        $user = User::create([
            'display_name' => trim($data['display_name']),
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'auth_provider' => 'local',
            'credential_setup_completed' => true,
            'onboarding_completed' => false,
        ]);

        $verificationNotificationSent = false;
        try {
            event(new Registered($user));
            $verificationNotificationSent = true;
        } catch (Throwable $exception) {
            report($exception);
        }

        Auth::login($user);

        if ($user->hasVerifiedEmail()) {
            return redirect()->to($this->redirectAfterAuth($user))->with('success', 'Cuenta creada correctamente.');
        }

        if (! $verificationNotificationSent) {
            return redirect()->route('verification.notice')->with(
                'warning',
                'Cuenta creada, pero no se pudo enviar el correo de confirmacion. Revisa SMTP y reenvia la verificacion.'
            );
        }

        if (! $this->mailerCanDeliverInboxMessages()) {
            return redirect()->route('verification.notice')->with(
                'warning',
                'Cuenta creada. El correo se genero en modo local (MAIL_MAILER=log/array). Configura SMTP para recibirlo en tu bandeja.'
            );
        }

        return redirect()->route('verification.notice')->with('success', 'Cuenta creada. Revisa tu correo para confirmar.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesion cerrada correctamente.');
    }

    public function showCredentialSetup(Request $request)
    {
        return view('auth.credential-setup', [
            'user' => $request->user(),
        ]);
    }

    public function storeCredentialSetup(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'display_name' => ['required', 'string', 'min:2', 'max:60'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:'.self::USERNAME_REGEX,
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'password' => ['required', 'confirmed', 'string', 'min:8', 'max:120'],
        ], [
            'username.regex' => 'El usuario solo puede contener letras, numeros, punto (.), guion (-) o guion bajo (_).',
            'username.unique' => 'Ese nombre de usuario ya esta en uso.',
        ]);

        $user->fill([
            'display_name' => trim($data['display_name']),
            'username' => mb_strtolower(trim($data['username'])),
            'password' => $data['password'],
            'credential_setup_completed' => true,
        ])->save();

        return redirect()->to($this->redirectAfterAuth($user))->with('success', 'Credenciales actualizadas correctamente.');
    }

    private function redirectAfterAuth(User $user): string
    {
        if (! $user->credential_setup_completed) {
            return route('credential.show');
        }

        if (! $user->onboarding_completed) {
            return route('onboarding.show');
        }

        return route('dashboard');
    }

    private function mailerCanDeliverInboxMessages(): bool
    {
        $mailer = (string) config('mail.default');
        if (in_array($mailer, ['log', 'array'], true)) {
            return false;
        }

        if ($mailer !== 'smtp') {
            return true;
        }

        $host = (string) config('mail.mailers.smtp.host');
        $username = (string) config('mail.mailers.smtp.username');
        $password = (string) config('mail.mailers.smtp.password');

        return $host !== '' && $username !== '' && $password !== '';
    }
}
