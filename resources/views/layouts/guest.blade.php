<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{-- Language Switcher - Top Right --}}
        <div class="absolute top-0 right-0 mt-4 mr-4 flex space-x-1 z-10">
            <a href="{{ route('language', 'nl') }}" class="px-2 py-1 rounded text-sm {{ app()->getLocale() == 'nl' ? 'bg-red-100 text-red-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">NL</a>
            <a href="{{ route('language', 'en') }}" class="px-2 py-1 rounded text-sm {{ app()->getLocale() == 'en' ? 'bg-red-100 text-red-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">EN</a>
        </div>

        {{-- Main container for centering and background --}}
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-100">

            {{-- White card container --}}
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{-- Render the slot content directly (logo and form will be inside $slot now) --}}
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
