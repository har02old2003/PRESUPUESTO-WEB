<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
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
            'username' => ['required', 'string', 'min:3', 'max:20', 'regex:/^[a-z0-9._-]+$/', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'string', 'min:8', 'max:120'],
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

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', 'Cuenta creada. Revisa tu correo para confirmar.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesion cerrada correctamente.');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = mb_strtolower(trim((string) $googleUser->getEmail()));

            if ($email === '') {
                return redirect()->route('login')->withErrors(['oauth' => 'Google no devolvio correo valido.']);
            }

            $user = User::query()
                ->where('google_id', $googleUser->getId())
                ->orWhere('email', $email)
                ->first();

            if (! $user) {
                $seed = $googleUser->getNickname() ?: Str::before($email, '@');
                $username = $this->generateUniqueUsername($seed);

                $user = User::create([
                    'display_name' => $googleUser->getName() ?: ucfirst($username),
                    'username' => $username,
                    'email' => $email,
                    'password' => null,
                    'auth_provider' => 'google',
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'credential_setup_completed' => false,
                    'onboarding_completed' => false,
                ]);
            } else {
                $provider = $user->auth_provider;
                if (! str_contains($provider, 'google')) {
                    $provider = $provider === 'local' ? 'google_linked' : $provider.'_google';
                }

                $user->fill([
                    'google_id' => $googleUser->getId(),
                    'auth_provider' => $provider,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                    'display_name' => $user->display_name ?: ($googleUser->getName() ?: $user->username),
                ])->save();
            }

            Auth::login($user, true);

            return redirect()->to($this->redirectAfterAuth($user));
        } catch (Throwable) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'No se pudo iniciar con Google. Verifica configuracion OAuth y vuelve a intentar.',
            ]);
        }
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
                'regex:/^[a-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'password' => ['required', 'confirmed', 'string', 'min:8', 'max:120'],
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

    private function generateUniqueUsername(string $seed): string
    {
        $normalized = preg_replace('/[^a-z0-9._-]/', '', mb_strtolower($seed));
        $base = trim($normalized ?: 'usuario', '._-');
        $base = $base === '' ? 'usuario' : $base;
        $base = Str::limit($base, 16, '');

        $candidate = $base;
        $counter = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $suffix = (string) $counter;
            $candidate = Str::limit($base, 20 - strlen($suffix), '').$suffix;
            $counter++;
        }

        return $candidate;
    }
}
