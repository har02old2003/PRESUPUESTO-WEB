<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Correo de confirmacion reenviado.');
    }
}
