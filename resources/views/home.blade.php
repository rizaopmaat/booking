@extends('layouts.app')

@section('title', __('Welcome to our luxury hotel'))

{{-- Hero Section - Placed OUTSIDE the main content section to avoid layout padding --}}
<div class="relative bg-gray-900 text-white overflow-hidden" style="height: 100vh;">
    <img src="{{ asset('images/hotel-hero.jpg') }}" alt="Luxury Hotel Lobby" class="absolute inset-0 w-full h-full object-cover z-0 opacity-50">
    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
        <h1 class="text-4xl md:text-6xl font-bold font-playfair mb-4">{{ __('Welcome to our luxury hotel') }}</h1>
        <p class="text-lg md:text-2xl font-montserrat mb-8">{{ __('Experience ultimate luxury and comfort') }}</p>
        <a href="{{ route('rooms.index') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300">
            {{ __('Discover our rooms') }}
        </a>
    </div>
</div>

{{-- The rest of the content goes into the default content section with padding --}}
@section('content')

<div id="about" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">{{ __('Luxury & Comfort') }}</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">{{ __('Our rooms are designed with attention to detail, offering a perfect blend of style, comfort and functionality') }}</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            {{-- Card 1: Luxe Kamers --}}
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transition duration-300 hover:shadow-2xl group">
                <div class="p-8">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4 group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900">{{ __('Luxe Kamers') }}</h3>
                    </div>
                    <p class="text-gray-600 mb-5">{{ __('Onze ruime kamers zijn voorzien van premium bedden, elegante interieurs en moderne voorzieningen voor ultiem comfort.') }}</p>
                    <a href="{{ route('rooms.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium group-hover:underline">
                        {{ __('View details') }}
                        <svg class="w-5 h-5 ml-1 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            
            {{-- Card 2: 24/7 Service --}}
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transition duration-300 hover:shadow-2xl group">
                <div class="p-8">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4 group-hover:bg-green-600 group-hover:text-white transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900">{{ __('24/7 Service') }}</h3>
                    </div>
                    <p class="text-gray-600 mb-5">{{ __('Ons deskundige personeel staat 24 uur per dag klaar om aan al uw wensen te voldoen en uw verblijf onvergetelijk te maken.') }}</p>
                    <a href="{{ route('rooms.index') }}" class="inline-flex items-center text-green-600 hover:text-green-800 font-medium group-hover:underline">
                        {{ __('Meer over services') }}
                        <svg class="w-5 h-5 ml-1 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            
            {{-- Card 3: Free WiFi --}}
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transition duration-300 hover:shadow-2xl group">
                <div class="p-8">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4 group-hover:bg-purple-600 group-hover:text-white transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900">{{ __('Free WiFi') }}</h3>
                    </div>
                    <p class="text-gray-600 mb-5">{{ __('Blijf verbonden met ons ultrasnelle WiFi-netwerk, ideaal voor zowel zakelijke als vrije tijd gebruik tijdens uw verblijf.') }}</p>
                    <a href="{{ route('rooms.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium group-hover:underline">
                        {{ __('Ontdek voorzieningen') }}
                        <svg class="w-5 h-5 ml-1 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-20 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-8">{{ __('Special Offer') }}</h2>
        <p class="text-xl mb-10 max-w-3xl mx-auto">{{ __('Book now and receive 15% off on stays of 3 nights or longer') }}</p>
        <a href="{{ route('rooms.index') }}" class="inline-flex items-center bg-white text-blue-600 hover:bg-blue-50 font-bold py-4 px-8 rounded-lg transition duration-300 transform hover:scale-105">
            {{ __('Search availability') }}
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
        </a>
    </div>
</div>

{{-- Loyalty Discount Teaser --}}
@guest
<section class="bg-red-100 py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold font-playfair text-red-800 mb-4">{{ __('Word ROC Vriend!') }}</h2>
        <p class="text-lg text-gray-700 mb-6">{{ __('Registreer, verifieer uw e-mail en ontvang €5 korting op uw volgende boeking na uw eerste verblijf!') }}</p>
        <a href="{{ route('register') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-md transition duration-300">{{ __('Registreer Nu') }}</a>
    </div>
</section>
@else {{-- User is logged in --}}
    @if(!Auth::user()->hasVerifiedEmail() || !(Auth::user()->bookings()->where('status', 'confirmed')->exists()))
    <section class="bg-red-100 py-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold font-playfair text-red-800 mb-4">{{ __('Bijna €5 Korting!') }}</h2>
            @if(!Auth::user()->hasVerifiedEmail())
            <p class="text-lg text-gray-700 mb-6">{{ __('Verifieer uw e-mailadres om in aanmerking te komen voor €5 ROC Vriendenkorting na uw eerste verblijf!') }}</p>
            <form class="inline-block" method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-md transition duration-300">
                    {{ __('Verificatie-e-mail Opnieuw Verzenden') }}
                </button>
            </form>
            @else
            <p class="text-lg text-gray-700 mb-6">{{ __('Nog één verblijf en u ontvangt €5 ROC Vriendenkorting op toekomstige boekingen!') }}</p>
            <a href="{{ route('rooms.index') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-md transition duration-300">{{ __('Bekijk Kamers') }}</a>
            @endif
        </div>
    </section>
    @endif
@endguest
@endsection 