<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function show()
    {
        return view('onboarding.index');
    }

    public function complete(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->onboarding_completed = true;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Bienvenido a HAXX COORP.');
    }
}
