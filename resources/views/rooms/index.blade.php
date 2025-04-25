@extends('layouts.app')

@section('title', __('Rooms'))

@section('content')
<div class="bg-gradient-to-br from-red-50 to-red-100 py-16 px-4">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold font-playfair text-center text-red-800 mb-6">{{ __('Our Luxury Rooms') }}</h1>
        <p class="text-lg text-center text-gray-600 mb-12 max-w-3xl mx-auto">{{ __('Discover our collection of exquisite rooms and suites, designed for ultimate comfort and style.') }}</p>

        {{-- Restore Search Form directly --}}
        <form action="{{ route('rooms.index') }}" method="GET" class="bg-white p-6 rounded-lg shadow-lg mb-12 grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Check In') }}</label>
                <input type="date" id="check_in" name="check_in" value="{{ request('check_in', $check_in ?? '') }}" min="{{ date('Y-m-d') }}" required 
                       class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
            </div>
            <div>
                <label for="check_out" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Check Out') }}</label>
                <input type="date" id="check_out" name="check_out" value="{{ request('check_out', $check_out ?? '') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required 
                       class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
            </div>
            <div>
                <label for="num_guests" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Guests') }}</label>
                <select id="num_guests" name="num_guests" class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">{{ __('Any') }}</option>
                    @for ($i = 1; $i <= 6; $i++) {{-- Assuming max 6 guests possible filter --}}
                        <option value="{{ $i }}" {{ request('num_guests', $numGuests ?? '') == $i ? 'selected' : '' }}>
                            {{ $i }} {{ trans_choice('Guest|Guests', $i) }}
                        </option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md transition duration-300 w-full md:w-auto h-10">
                {{ __('Search Available Rooms') }}
            </button>
        </form>

        {{-- Loyalty Discount Banner --}}
        @auth
            @if(Auth::user()->hasVerifiedEmail() && Auth::user()->bookings()->where('status', 'confirmed')->exists())
                <div class="mt-8 mb-6 p-4 bg-green-100 border border-green-300 rounded-md text-center">
                    <p class="text-green-800 font-semibold">
                        <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        {{ __('Als ROC Vriend ontvangt u €5 korting op deze boeking!') }} {{-- Add Translation --}}
                    </p>
                </div>
            @elseif(!Auth::user()->hasVerifiedEmail() || !(Auth::user()->bookings()->where('status', 'confirmed')->exists()))
                 <div class="mt-8 mb-6 p-4 bg-yellow-100 border border-yellow-300 rounded-md text-center">
                    <p class="text-yellow-800 font-semibold">
                        <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        @if(!Auth::user()->hasVerifiedEmail())
                            {{ __('Tip: Verifieer uw e-mail om in aanmerking te komen voor €5 ROC Vriendenkorting!') }} {{-- Add Translation --}}
                        @else
                             {{ __('Tip: Na uw eerste verblijf ontvangt u €5 ROC Vriendenkorting op toekomstige boekingen!') }} {{-- Add Translation --}}
                        @endif
                    </p>
                </div>
            @endif
        @endauth
        {{-- End Loyalty Discount Banner --}}

        {{-- Room Grid --}}
        @if($rooms === null)
             <div class="text-center mt-12 p-6 bg-white rounded-lg shadow">
                 <p class="text-lg text-gray-600">{{ __('Please select your check-in and check-out dates to see available rooms.') }}</p>
            @else
                @if($rooms->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($rooms as $room)
                            @php
                                $isAvailable = isset($availability[$room->id]) && $availability[$room->id] > 0;
                            @endphp
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 {{ $isAvailable ? 'hover:scale-105 hover:shadow-xl' : 'opacity-60 cursor-not-allowed' }}">
                                <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="w-full h-48 object-cover {{ !$isAvailable ? 'filter grayscale' : '' }}">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold font-playfair text-gray-800 mb-2">{{ $room->name }}</h3>
                                    
                                    {{-- Prijs sectie --}}
                                    <div class="mb-2 text-right">
                                        @if($numberOfNights)
                                            {{-- Toon totaalprijs als datums geselecteerd zijn --}}
                                            @php 
                                                $priceInfo = $totalPricePerRoom[$room->id] ?? null; 
                                                $hasDiscount = $priceInfo && ($priceInfo['duration_discount'] > 0 || $priceInfo['loyalty_discount'] > 0);
                                            @endphp
                                            @if($priceInfo)
                                                <span class="text-xs text-gray-500">{{ trans_choice('messages.total_for_nights', $numberOfNights, ['nights' => $numberOfNights]) }}</span>
                                                <div>
                                                    @if($hasDiscount)
                                                        <del class="text-sm text-gray-500 mr-1">€{{ number_format($priceInfo['subtotal'], 2) }}</del>
                                                    @endif
                                                    <span class="text-red-600 font-bold text-lg">€{{ number_format($priceInfo['total'], 2) }}</span>
                                                </div>
                                                {{-- Toon toegepaste kortingen --}}
                                                @if($priceInfo['duration_discount'] > 0)
                                                    <span class="block text-xs text-green-600">{{ __('Includes 15% stay discount') }}</span>
                                                @endif
                                                @if($priceInfo['loyalty_discount'] > 0)
                                                    <span class="block text-xs text-blue-600">{{ __('Includes €5 Friends Discount') }}</span>
                                                @endif
                                            @endif
                                        @else
                                            {{-- Toon prijs per nacht als geen datums geselecteerd --}}
                                            <span class="text-red-600 font-bold text-lg">€{{ number_format($room->price, 2) }}</span>
                                            <span class="text-xs text-gray-500">/ {{ __('per night') }}</span>
                                            {{-- Algemene korting indicaties --}}
                                            <span class="block text-xs text-green-600">{{ __('15% off for 3+ nights') }}</span>
                                            @if($isReturningCustomer)
                                                <span class="block text-xs text-blue-600">{{ __('€5 ROC Friends Discount applicable!') }}</span>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4 line-clamp-2">{{ $room->description }}</p>

                                    @if(isset($availability[$room->id]))
                                        @if($isAvailable)
                                            <p class="text-sm text-green-600 font-semibold mb-3">
                                                {{ $availability[$room->id] }} {{ $availability[$room->id] == 1 ? __('kamer beschikbaar') : __('kamers beschikbaar') }}
                                            </p>
                                        @else
                                            <p class="text-sm text-red-600 font-semibold mb-3">
                                                {{ __('Not available for these dates') }}
                                            </p>
                                        @endif
                                    @endif

                                    @if($isAvailable)
                                        <a href="{{ route('rooms.show', ['room' => $room, 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}" class="text-red-600 hover:text-red-800 font-semibold inline-flex items-center">
                                            {{ __('View Details & Book') }}
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-500 font-semibold inline-flex items-center cursor-not-allowed">
                                            {{ __('View Details') }}
                                             <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{ $rooms->links() }}
                    </div>

                @else
                    <div class="text-center py-12 bg-white rounded-lg shadow-md">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <p class="text-xl text-gray-600 mb-4">{{ __('No rooms found matching your criteria for the selected dates.') }}</p>
                        <p class="text-gray-500">{{ __('Try adjusting your dates or guest count.') }}</p>
                    </div>
                @endif
            @endif
        </div>

        <section class="mt-16 bg-gradient-to-r from-red-600 to-red-700 text-white py-12 px-8 rounded-lg shadow-xl">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold font-playfair mb-4">{{ __('Special Offer') }}</h2>
                <p class="text-lg mb-8">{{ __('Book 3 nights or more and get 15% off!') }}</p>
                <a href="{{ route('rooms.index') }}" class="bg-white text-red-700 hover:bg-gray-100 font-bold py-3 px-6 rounded-md transition duration-300">
                    {{ __('Explore Rooms') }}
                </a>
            </div>
        </section>
    </div>
</div>
@endsection