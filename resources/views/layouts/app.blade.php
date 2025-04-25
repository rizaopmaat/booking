<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') - {{ __('Hotel ROC') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    </head>
    <body class="bg-gray-50 pt-16">
        <nav x-data="{ open: false }" class="bg-white shadow-lg fixed w-full top-0 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="flex-shrink-0 flex items-center">
                            <span class="text-2xl font-bold text-red-600">{{ __('Hotel ROC') }}</span>
                        </a>
                    </div>
                    
                    <div class="hidden md:flex md:items-center md:space-x-2">
                        <div class="mr-4 flex space-x-1">
                            <a href="{{ route('language', 'nl') }}" class="px-2 py-1 rounded text-sm {{ app()->getLocale() == 'nl' ? 'bg-red-100 text-red-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">NL</a>
                            <a href="{{ route('language', 'en') }}" class="px-2 py-1 rounded text-sm {{ app()->getLocale() == 'en' ? 'bg-red-100 text-red-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">EN</a>
                        </div>
                        
                        @auth
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('Admin') }}</a>
                                <a href="{{ route('admin.rooms.index') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('Rooms') }}</a>
                                <a href="{{ route('admin.bookings.index') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('Bookings') }}</a>
                            @else
                                <a href="{{ route('rooms.index') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('Rooms') }}</a>
                                <a href="{{ route('bookings.index') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('navigation.my_bookings') }}</a>
                            @endif
                            {{-- Profile Icon Link --}}
                            <a href="{{ route('profile.edit') }}" class="p-2 text-gray-500 hover:text-red-600 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 ml-2" title="{{ __('Profile') }}">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </a>
                            {{-- End Profile Icon Link --}}
                            <form method="POST" action="{{ route('logout') }}" class="my-auto ml-2">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white hover:bg-red-700 px-3 py-2 rounded-md text-sm font-medium">{{ __('Logout') }}</button>
                            </form>
                        @else
                            <a href="{{ route('rooms.index') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('Rooms') }}</a>
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('Login') }}</a>
                            <a href="{{ route('register') }}" class="bg-red-600 text-white hover:bg-red-700 px-3 py-2 rounded-md text-sm font-medium">{{ __('Register') }}</a>
                        @endauth
                    </div>

                    <div class="-mr-2 flex items-center md:hidden">
                        <button @click="open = !open" type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg x-show="!open" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="open" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="md:hidden" id="mobile-menu" style="display: none;">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <div class="flex justify-center space-x-2 pb-2 border-b border-gray-200 mb-2">
                         <a href="{{ route('language', 'nl') }}" class="px-3 py-1 rounded text-sm {{ app()->getLocale() == 'nl' ? 'bg-red-100 text-red-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">NL</a>
                         <a href="{{ route('language', 'en') }}" class="px-3 py-1 rounded text-sm {{ app()->getLocale() == 'en' ? 'bg-red-100 text-red-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">EN</a>
                    </div>
                    @auth
                         @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:bg-gray-50 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">{{ __('Admin') }}</a>
                            <a href="{{ route('admin.rooms.index') }}" class="text-gray-700 hover:bg-gray-50 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">{{ __('Rooms') }}</a>
                            <a href="{{ route('admin.bookings.index') }}" class="text-gray-700 hover:bg-gray-50 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">{{ __('Bookings') }}</a>
                        @else
                            <a href="{{ route('rooms.index') }}" class="text-gray-700 hover:bg-gray-50 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">{{ __('Rooms') }}</a>
                            <a href="{{ route('bookings.index') }}" class="text-gray-700 hover:bg-gray-50 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">{{ __('navigation.my_bookings') }}</a>
                            {{-- Add Profile Link for Mobile if not already present --}}
                            <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:bg-gray-50 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">{{ __('Profile') }}</a>
                        @endif
                         <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full text-left bg-red-600 text-white hover:bg-red-700 block px-3 py-2 rounded-md text-base font-medium">{{ __('Logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('rooms.index') }}" class="text-gray-700 hover:bg-gray-50 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">{{ __('Rooms') }}</a>
                        <a href="{{ route('login') }}" class="text-gray-700 hover:bg-gray-50 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">{{ __('Login') }}</a>
                        <a href="{{ route('register') }}" class="bg-red-600 text-white hover:bg-red-700 block px-3 py-2 rounded-md text-base font-medium">{{ __('Register') }}</a>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Verification Notice --}}
        @auth
            @if (! Auth::user()->hasVerifiedEmail())
            <div class="fixed top-16 w-full z-40 bg-yellow-100 border-b border-yellow-300 p-3 text-center text-sm text-yellow-800">
                {{ __('Your email address is unverified.') }} 
                <form class="inline-block ml-2" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="underline font-medium hover:text-yellow-900">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </form>
            </div>
            {{-- Adjust body padding if notice is shown --}}
            <script>
                document.body.style.paddingTop = (document.querySelector('nav').offsetHeight + document.querySelector('.fixed.top-16').offsetHeight) + 'px';
            </script>
            @endif
        @endauth

        <main class="max-w-7xl mx-auto py-6 px-4">
            @yield('content')
            {{ $slot ?? '' }}
        </main>

        <footer class="bg-gray-800 text-white mt-12">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-xl font-bold mb-4">{{ __('Hotel ROC') }}</h3>
                        <p>{{ __('Book your dream vacation at our exclusive hotel') }}</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-4">{{ __('Contact') }}</h3>
                        <p>Email: info@hotelroc.nl</p>
                        <p>{{ __('Tel') }}: +31 (0)20 123 4567</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-4">{{ __('Adres') }}</h3>
                        <p>{{ __('Hotelstraat') }} 1</p>
                        <p>1234 AB Amsterdam</p>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
