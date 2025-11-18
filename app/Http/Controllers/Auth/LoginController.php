<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Display the login form.
     */
    public function create(Request $request): View
    {
        $status = $request->session()->pull('auth.status');

        if (! $status && $request->boolean('expired')) {
            $status = [
                'type' => 'warning',
                'message' => 'Tu sesión expiró. Inicia sesión nuevamente para continuar.',
            ];
        } elseif (! $status && $request->boolean('logged_out')) {
            $status = [
                'type' => 'success',
                'message' => 'Has cerrado sesión correctamente.',
            ];
        } elseif (! $status && $request->boolean('auth_required')) {
            $status = [
                'type' => 'info',
                'message' => 'Necesitas iniciar sesión para acceder a esa sección.',
            ];
        }

        return view('auth.login', [
            'status' => $status,
        ]);
    }

    /**
     * Handle an authentication attempt.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('auth.status', [
            'type' => 'success',
            'message' => 'Has cerrado sesión correctamente.',
        ]);
    }
}
