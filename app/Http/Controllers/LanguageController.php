<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        // Validate lang
        if (!in_array($lang, ['en', 'nl'])) {
            $lang = 'nl'; // Default to Dutch
        }
        
        // Set the language in session
        Session::put('locale', $lang);
        
        // Also set it for the current request
        App::setLocale($lang);
        
        return Redirect::back();
    }
} 