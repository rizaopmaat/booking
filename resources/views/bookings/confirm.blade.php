@extends('layouts.app')

@section('title', __('Confirm Booking'))

@section('content')
{{-- Add Alpine.js context for modals --}}
<div x-data="{ termsModalOpen: false, privacyModalOpen: false }" class="bg-gradient-to-br from-red-50 to-red-100 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold font-playfair text-center text-red-800 mb-8">{{ __('Confirm Your Booking') }}</h1>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- START: Booking Summary Section --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold font-playfair text-gray-800 mb-4">{{ __('Booking Summary') }}</h2>
                
                {{-- Use individual variables instead of $confirmationData --}}
                <div class="mb-3">
                    <strong class="block text-sm text-gray-500">{{ __('Room') }}:</strong>
                    <span class="text-gray-800">{{ $room->getTranslation('name', app()->getLocale()) }}</span>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <strong class="block text-sm text-gray-500">{{ __('Check In') }}:</strong>
                        <span class="text-gray-800">{{ $checkInDate->format('d-m-Y') }}</span>
                    </div>
                    <div>
                        <strong class="block text-sm text-gray-500">{{ __('Check Out') }}:</strong>
                        <span class="text-gray-800">{{ $checkOutDate->format('d-m-Y') }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                     <div>
                        <strong class="block text-sm text-gray-500">{{ __('Nights') }}:</strong>
                        <span class="text-gray-800">{{ $numberOfNights }}</span>
                    </div>
                    <div>
                        <strong class="block text-sm text-gray-500">{{ __('Guests') }}:</strong>
                        <span class="text-gray-800">{{ $numGuests }}</span>
                    </div>
                </div>

                {{-- Use $selectedOptionsData --}}
                @if(!empty($selectedOptionsData))
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <strong class="block text-sm text-gray-500 mb-1">{{ __('Selected Options') }}:</strong>
                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                        @foreach($selectedOptionsData as $option)
                            <li>
                                {{ $option['quantity'] }}x {{ $option['name'] }}
                                (€{{ number_format($option['total_price'], 2, ',', '.') }})
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Use $priceDetails --}}
                <div class="mt-4 pt-3 border-t border-gray-200 font-semibold">
                    @if($priceDetails['duration_discount'] > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>{{ __('Stay Discount') }}:</span>
                            <span>- €{{ number_format($priceDetails['duration_discount'], 2, ',', '.') }}</span>
                        </div>
                    @endif
                     @if($priceDetails['loyalty_discount'] > 0)
                        <div class="flex justify-between text-sm text-blue-600">
                            <span>{{ __('Loyalty Discount') }}:</span>
                            <span>- €{{ number_format($priceDetails['loyalty_discount'], 2, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg text-red-700 mt-2">
                        <span>{{ __('Total Price') }}:</span>
                        <span>€{{ number_format($priceDetails['total'], 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            {{-- END: Booking Summary Section --}}

            {{-- START: User Details & Form Section --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold font-playfair text-gray-800 mb-4">{{ __('Your Details') }}</h2>

                {{-- Form starts here, encompasses user details inputs --}}
                <form method="POST" action="{{ route('bookings.store') }}" class="mt-6">
                    @csrf
                    {{-- Hidden fields for booking data remain at the top --}}
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="check_in_date" value="{{ $checkInDate->toDateString() }}">
                    <input type="hidden" name="check_out_date" value="{{ $checkOutDate->toDateString() }}">
                    <input type="hidden" name="num_guests" value="{{ $numGuests }}">
                    <input type="hidden" name="total_price" value="{{ $priceDetails['total'] }}">
                    @if(!empty($selectedOptions))
                        @if(is_array($selectedOptions) && array_is_list($selectedOptions))
                            {{-- Platte array met option IDs --}}
                            @php
                                // Tel hoe vaak elke optie voorkomt
                                $optionCounts = array_count_values($selectedOptions);
                            @endphp
                            @foreach($optionCounts as $optionId => $quantity)
                                <input type="hidden" name="options[{{ $optionId }}]" value="{{ $quantity }}">
                            @endforeach
                        @else
                            {{-- Associatieve array [optionId => quantity] --}}
                            @foreach($selectedOptions as $optionId => $quantity)
                                <input type="hidden" name="options[{{ $optionId }}]" value="{{ $quantity }}">
                            @endforeach
                        @endif
                    @endif

                    {{-- Conditional User Details Section --}}
                    @guest
                        {{-- Guest Checkout Fields --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">{{ __('First Name') }}</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">{{ __('Last Name') }}</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <h3 class="text-lg font-semibold mt-6 mb-3">{{ __('Address') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="street" class="block text-sm font-medium text-gray-700">{{ __('Street') }}</label>
                                <input type="text" name="street" id="street" value="{{ old('street') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('street') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="house_number" class="block text-sm font-medium text-gray-700">{{ __('House Number') }}</label>
                                <input type="text" name="house_number" id="house_number" value="{{ old('house_number') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('house_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700">{{ __('Postal Code') }}</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('postal_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">{{ __('City') }}</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700">{{ __('Country') }}</label>
                                <input type="text" name="country" id="country" value="{{ old('country') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('country') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Guest Registration Section --}}
                        <div class="mt-8 pt-6 border-t border-dashed border-gray-300">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ __('registration.create_account_title') }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ __('registration.create_account_info') }}</p>

                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('registration.email') }}</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">{{ __('registration.password') }}</label>
                                    <input type="password" name="password" id="password" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('registration.password_confirmation') }}</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                     {{-- No error message needed here, 'confirmed' rule handles it on 'password' field --}}
                                </div>
                            </div>
                        </div>

                    @else {{-- Authenticated User --}}
                        {{-- Logged-in User Details (Mostly Readonly) --}}
                        @php
                            // Check which fields are already filled in the user profile
                            $hasFirstName = !empty(Auth::user()->first_name);
                            $hasLastName = !empty(Auth::user()->last_name);
                            $hasPhone = !empty(Auth::user()->phone_number);
                            $hasStreet = !empty(Auth::user()->street);
                            $hasHouseNumber = !empty(Auth::user()->house_number);
                            $hasPostalCode = !empty(Auth::user()->postal_code);
                            $hasCity = !empty(Auth::user()->city);
                            $hasCountry = !empty(Auth::user()->country);
                            $hasAddress = $hasStreet && $hasHouseNumber && $hasPostalCode && $hasCity && $hasCountry;
                            $hasAllDetails = $hasFirstName && $hasLastName && $hasPhone && $hasAddress;
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">{{ __('First Name') }}</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', Auth::user()->first_name ?? '') }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" 
                                       {{ !$hasFirstName ? 'required' : '' }}>
                                 @error('first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">{{ __('Last Name') }}</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', Auth::user()->last_name ?? '') }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" 
                                       {{ !$hasLastName ? 'required' : '' }}>
                                 @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                            <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 sm:text-sm" 
                                   readonly> {{-- Email is always readonly here, managed via profile --}}
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', Auth::user()->phone_number ?? '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" 
                                   {{ !$hasPhone ? 'required' : '' }}>
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <h3 class="text-lg font-semibold mt-6 mb-3">{{ __('Address') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                             <div>
                                <label for="street" class="block text-sm font-medium text-gray-700">{{ __('Street') }}</label>
                                <input type="text" name="street" id="street" value="{{ old('street', Auth::user()->street ?? '') }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" 
                                       {{ !$hasStreet ? 'required' : '' }}>
                                @error('street') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                             <div>
                                <label for="house_number" class="block text-sm font-medium text-gray-700">{{ __('House Number') }}</label>
                                <input type="text" name="house_number" id="house_number" value="{{ old('house_number', Auth::user()->house_number ?? '') }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" 
                                       {{ !$hasHouseNumber ? 'required' : '' }}>
                                 @error('house_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700">{{ __('Postal Code') }}</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', Auth::user()->postal_code ?? '') }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" 
                                       {{ !$hasPostalCode ? 'required' : '' }}>
                                 @error('postal_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">{{ __('City') }}</label>
                                <input type="text" name="city" id="city" value="{{ old('city', Auth::user()->city ?? '') }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" 
                                       {{ !$hasCity ? 'required' : '' }}>
                                 @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700">{{ __('Country') }}</label>
                                <input type="text" name="country" id="country" value="{{ old('country', Auth::user()->country ?? '') }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" 
                                       {{ !$hasCountry ? 'required' : '' }}>
                                 @error('country') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if(!$hasAllDetails)
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md text-sm text-blue-700">
                            <p>{{ __('messages.complete_profile_info_booking') }}</p> {{-- Nieuwe vertaalsleutel nodig --}}
                        </div>
                        @endif

                    @endguest {{-- End Guest/Auth Conditional --}}

                    {{-- START: Payment Method Section (Common to both guests and auth users) --}}
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('payment.method_title') }}</h3>
                        <div class="space-y-3">
                            {{-- Pay at Accommodation (Default Checked) --}}
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" id="payment_at_accommodation" value="at_accommodation" checked
                                       class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300">
                                <label for="payment_at_accommodation" class="ml-3 block text-sm font-medium text-gray-700">
                                    {{ __('payment.at_accommodation') }}
                                </label>
                            </div>

                            {{-- iDEAL (Disabled) --}}
                            <div class="flex items-center opacity-50 cursor-not-allowed">
                                <input type="radio" name="payment_method" id="payment_ideal" value="ideal" disabled
                                       class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300">
                                <label for="payment_ideal" class="ml-3 flex items-center text-sm font-medium text-gray-500">
                                    {{-- Switched to SVG logo from GitHub - Consider downloading locally --}}
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/IDEAL_%28Bezahlsystem%29_logo.svg" alt="iDEAL Logo" class="h-5 mr-2">
                                    {{ __('payment.ideal') }}
                                </label>
                            </div>

                            {{-- Credit Card (Disabled) --}}
                            <div class="flex items-center opacity-50 cursor-not-allowed">
                                <input type="radio" name="payment_method" id="payment_credit_card" value="credit_card" disabled
                                       class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300">
                                <label for="payment_credit_card" class="ml-3 flex items-center text-sm font-medium text-gray-500">
                                    <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/main/flat-rounded/visa.svg" alt="Visa Logo" class="h-5 mr-1">
                                    <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/main/flat-rounded/mastercard.svg" alt="Mastercard Logo" class="h-5 mr-2">
                                    {{ __('payment.credit_card') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    {{-- END: Payment Method Section --}}

                    {{-- Submit button and Terms/Privacy links --}}
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-500 mb-4">
                            {!! str_replace([':terms_of_service', ':privacy_policy'], [
                                '<button type="button" @click="termsModalOpen = true" class="underline hover:text-red-700">' . __('Terms of Service') . '</button>',
                                '<button type="button" @click="privacyModalOpen = true" class="underline hover:text-red-700">' . __('Privacy Policy') . '</button>'
                            ], __('By clicking confirm, you agree to our :terms_of_service and :privacy_policy')) !!}
                        </p>
                        <button type="submit" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-md transition duration-300 text-lg">
                            {{ __('Confirm & Request Booking') }}
                        </button>
                    </div>

                </form> {{-- End of the main form --}}
            </div>
            {{-- END: User Details & Form Section --}}

        </div> {{-- End Grid --}}
    </div>

    {{-- START: Modals --}}
    {{-- Terms Modal --}}
    <div x-show="termsModalOpen" 
         style="display: none;" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center">
        {{-- Background overlay --}}
        <div x-show="termsModalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="termsModalOpen = false" 
             class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        {{-- Modal Content --}}
        <div x-show="termsModalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95" 
             class="bg-white rounded-lg shadow-xl overflow-hidden max-w-2xl w-full mx-4 my-8 relative z-60 transform transition-all">
            <div class="px-6 py-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('terms.title') }}</h3>
                <div class="mt-2 text-sm text-gray-600 max-h-96 overflow-y-auto pr-2">
                    <p class="whitespace-pre-line">{{ __('terms.content') }}</p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="termsModalOpen = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('modal.close') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Privacy Modal (similar structure) --}}
    <div x-show="privacyModalOpen" 
         style="display: none;" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center">
         <div x-show="privacyModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="privacyModalOpen = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
         <div x-show="privacyModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-lg shadow-xl overflow-hidden max-w-2xl w-full mx-4 my-8 relative z-60 transform transition-all">
            <div class="px-6 py-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('privacy.title') }}</h3>
                <div class="mt-2 text-sm text-gray-600 max-h-96 overflow-y-auto pr-2">
                    <p class="whitespace-pre-line">{{ __('privacy.content') }}</p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="privacyModalOpen = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('modal.close') }}
                </button>
            </div>
        </div>
    </div>
    {{-- END: Modals --}}

</div> {{-- End Alpine.js context --}}
@endsection 