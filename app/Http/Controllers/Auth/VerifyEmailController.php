<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $redirectRoute = route('bookings.index', absolute: false);

        if ($request->user()->hasVerifiedEmail()) {
            return redirect($redirectRoute)->with('info', __('messages.email_already_verified'));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect($redirectRoute.'?verified=1')->with('success', __('messages.email_verified_successfully'));
    }
}
