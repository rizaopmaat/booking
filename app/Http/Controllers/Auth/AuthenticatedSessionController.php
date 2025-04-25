<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if the logged-in user is an admin
        if (Auth::user()->is_admin) {
            // Redirect admins to the admin dashboard
            // Ensure the route name 'admin.dashboard' exists
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        // Redirect regular users to the default dashboard (or home)
        // We use route('home') assuming you have a named route 'home' for the regular user landing page.
        // Adjust if your default route is named differently (e.g., 'dashboard').
        // The original code used route('dashboard'), which might not exist or be intended for users.
        // Let's default to home page '/' if 'home' route doesn't exist.
        $defaultRedirect = route('bookings.index', absolute: false);
        return redirect()->intended($defaultRedirect);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
