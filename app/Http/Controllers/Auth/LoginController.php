<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identity' => ['required', 'string'],
            'secret' => ['required', 'string'],
        ]);

        $identity = $credentials['identity'];
        $secret = $credentials['secret'];

        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $authenticated = Auth::attempt(
                ['email' => $identity, 'password' => $secret],
                $request->boolean('remember')
            );
        } else {
            $user = User::where('phone', $identity)->first();
            $authenticated = $user !== null
                && $user->pin !== null
                && Hash::check($secret, $user->pin);
            if ($authenticated) {
                Auth::login($user, $request->boolean('remember'));
            }
        }

        if (! $authenticated) {
            return back()
                ->withErrors(['identity' => 'These credentials do not match our records.'])
                ->onlyInput('identity');
        }

        $request->session()->regenerate();

        return redirect()->intended(
            Auth::user()->isAdmin() ? route('admin.dashboard') : route('sales.index')
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
