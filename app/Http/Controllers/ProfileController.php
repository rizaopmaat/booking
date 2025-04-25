<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        $request->user()->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'street' => $validated['street'] ?? null,
            'house_number' => $validated['house_number'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'language' => $validated['language']
        ]);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        session(['locale' => $request->user()->language]);
        app()->setLocale($request->user()->language);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required'],
        ]);

        $user = $request->user();

        // Check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Het wachtwoord is onjuist.']);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the profile completion form after registration.
     */
    public function showComplete(Request $request): View
    {
        return view('profile.complete', [
            'user' => $request->user(),
            'new_registration' => session('new_registration', false),
        ]);
    }

    /**
     * Save the completed profile information.
     */
    public function saveComplete(Request $request): RedirectResponse
    {
        // If user chose to skip, just redirect to bookings
        if ($request->has('skip')) {
            return redirect()->route('bookings.index');
        }

        // Otherwise validate and save the data
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'street' => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone_number' => $validated['phone_number'],
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'postal_code' => $validated['postal_code'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
        ]);

        return redirect()->route('bookings.index')
            ->with('success', __('messages.profile_completed_successfully'));
    }
}
