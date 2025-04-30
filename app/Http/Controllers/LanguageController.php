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
        if (!in_array($lang, ['en', 'nl'])) {
            $lang = 'nl';
        }
        
        Session::put('locale', $lang);
        
        App::setLocale($lang);
        
        return Redirect::back();
    }
} 