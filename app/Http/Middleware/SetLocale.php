<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = config('app.fallback_locale'); // Start with fallback

        // Priority 1: Locale explicitly set in the current session (by language switcher)
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, config('app.available_locales'))) {
                $locale = $sessionLocale;
            }
        }
        // Priority 2: User is logged in and has a language preference in their profile
        elseif (Auth::check() && Auth::user()->language) {
            $userLocale = Auth::user()->language;
            if (in_array($userLocale, config('app.available_locales'))) {
                $locale = $userLocale;
                // Store user preference in session if not already set by switcher
                Session::put('locale', $locale);
            }
        }
        // Priority 3: Browser preference (currently enabled)
        elseif ($request->server('HTTP_ACCEPT_LANGUAGE')) {
            $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
            if (in_array($browserLocale, config('app.available_locales'))) {
                $locale = $browserLocale;
                Session::put('locale', $locale); 
            }
        }

        // Final check: if determined locale is invalid, revert to fallback
        if (!in_array($locale, config('app.available_locales'))) {
            $locale = config('app.fallback_locale');
            Session::put('locale', $locale);
        }

        // Set the application locale for the current request
        App::setLocale($locale);

        return $next($request);
    }
} 