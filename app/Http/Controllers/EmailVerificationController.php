<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return $request->user()->credential_setup_completed
            ? redirect()->route('onboarding.show')->with('success', 'Correo confirmado correctamente.')
            : redirect()->route('credential.show')->with('success', 'Correo confirmado correctamente.');
    }

    public function send(Request $request): RedirectResponse
    {
        if (! $this->mailerCanDeliverInboxMessages()) {
            return back()->with('warning', 'El correo no puede enviarse a bandeja con la configuracion actual. Configura SMTP real en .env.');
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'email' => 'No se pudo reenviar el correo de confirmacion. Revisa la configuracion SMTP.',
            ]);
        }

        return back()->with('success', 'Correo de confirmacion reenviado.');
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
