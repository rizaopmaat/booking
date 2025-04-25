<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'language' => ['required', 'string', 'in:nl,en'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Extract first and last name from the full name
        $nameParts = explode(' ', $request->name, 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        $user = User::create([
            'name' => $request->name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'language' => $request->language,
            'password' => Hash::make($request->password),
            // Set optional fields to null or empty as appropriate
            'phone_number' => null,
            'street' => null,
            'house_number' => null,
            'postal_code' => null,
            'city' => null,
            'country' => null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to a page asking if they want to complete their profile now
        return redirect(route('profile.complete', absolute: false))->with('new_registration', true);
    }
}
