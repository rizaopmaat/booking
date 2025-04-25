        {{-- Booking Summary - Use individual variables --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">{{ __('Booking Summary') }}</h2>
            <div class="space-y-3">
                <p><strong>{{ __('Room') }}:</strong> {{ $room->getTranslation('name', app()->getLocale()) }}</p>
                <p><strong>{{ __('Check-in') }}:</strong> {{ $checkInDate->format('d-m-Y') }}</p>
                <p><strong>{{ __('Check-out') }}:</strong> {{ $checkOutDate->format('d-m-Y') }}</p>
                <p><strong>{{ __('nights') }}:</strong> {{ $numberOfNights }}</p>
                <p><strong>{{ __('Guests') }}:</strong> {{ $numGuests }}</p>

                {{-- Display selected options using $selectedOptionsData --}}
                @if(!empty($selectedOptionsData))
                    <h3 class="text-lg font-semibold mt-4 mb-2">{{ __('Selected Options') }}</h3>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($selectedOptionsData as $option)
                            <li>{{ $option['quantity'] }}x {{ $option['name'] }} (€{{ number_format($option['total_price'], 2, ',', '.') }})</li>
                        @endforeach
                    </ul>
                @endif

                {{-- Display discounts and total using $priceDetails --}}
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    @if($priceDetails['duration_discount'] > 0)
                        <p><strong>{{ __('Stay Discount') }}:</strong> - €{{ number_format($priceDetails['duration_discount'], 2, ',', '.') }}</p>
                    @endif
                    @if($priceDetails['loyalty_discount'] > 0)
                        <p><strong>{{ __('Loyalty Discount') }}:</strong> - €{{ number_format($priceDetails['loyalty_discount'], 2, ',', '.') }}</p>
                    @endif
                    <p class="text-lg font-bold mt-2"><strong>{{ __('Total Price') }}:</strong> €{{ number_format($priceDetails['total'], 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- User Details --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">{{ __('Your Details') }}</h2>
             @auth
                {{-- Display existing user details --}}
                <div class="space-y-3 mb-4">
                    <p><strong>{{ __('First Name') }}:</strong> {{ Auth::user()->first_name ?? 'N/A' }}</p>
                    <p><strong>{{ __('Last Name') }}:</strong> {{ Auth::user()->last_name ?? 'N/A' }}</p>
                    <p><strong>{{ __('Email') }}:</strong> {{ Auth::user()->email }}</p>
                    <p><strong>{{ __('Phone Number') }}:</strong> {{ Auth::user()->phone_number ?? 'N/A' }}</p>
                    <h3 class="text-lg font-semibold mt-4 mb-2">{{ __('Address') }}</h3>
                    <p><strong>{{ __('Street') }}:</strong> {{ Auth::user()->street ?? 'N/A' }}</p>
                    <p><strong>{{ __('House Number') }}:</strong> {{ Auth::user()->house_number ?? 'N/A' }}</p>
                    <p><strong>{{ __('Postal Code') }}:</strong> {{ Auth::user()->postal_code ?? 'N/A' }}</p>
                    <p><strong>{{ __('City') }}:</strong> {{ Auth::user()->city ?? 'N/A' }}</p>
                    <p><strong>{{ __('Country') }}:</strong> {{ Auth::user()->country ?? 'N/A' }}</p>
                </div>
                 <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Your details are pre-filled from your profile. To change them, please go to your profile settings.') }}</p>
            @else
                 {{-- Show form for guest users if needed --}}
                <p>Please log in or register to complete your booking.</p>
            @endauth

            {{-- Form to finalize booking using individual variables --}}
            <form method="POST" action="{{ route('bookings.store') }}" class="mt-6">
                @csrf

                {{-- Display All Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <strong class="font-bold">{{ __('Whoops! Something went wrong.') }}</strong>
                        <ul class="mt-3 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Hidden fields for booking details --}}
                <input type="hidden" name="room_id" value="{{ $room->id }}">
                <input type="hidden" name="check_in_date" value="{{ $checkInDate->toDateString() }}">
                <input type="hidden" name="check_out_date" value="{{ $checkOutDate->toDateString() }}">
                <input type="hidden" name="num_guests" value="{{ $numGuests }}">
                <input type="hidden" name="total_price" value="{{ $priceDetails['total'] }}">
                <input type="hidden" name="payment_method" value="at_accommodation"> {{-- Assuming this is the only method for now --}}

                {{-- Loop through the $selectedOptions array [optionId => quantity] for hidden inputs --}}
                 @if(!empty($selectedOptions))
                    @foreach($selectedOptions as $optionId => $quantity)
                        <input type="hidden" name="options[{{ $optionId }}]" value="{{ $quantity }}">
                    @endforeach
                @endif

                {{-- Guest Details & Registration Form OR Hidden fields for Auth user --}}
                @guest
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ __('messages.complete_guest_details') }}</p>
                    <div class="mt-6 space-y-4">
                        {{-- <h2 class="text-xl font-semibold">{{ __('Your Details') }}</h2> --}} {{-- Title already shown above --}}
                        {{-- First Name --}}
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('first_name') border-red-500 @enderror">
                            @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        {{-- Last Name --}}
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('last_name') border-red-500 @enderror">
                            @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        {{-- Email --}}
                         <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('email') border-red-500 @enderror">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        {{-- Phone --}}
                         <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('phone') border-red-500 @enderror">
                            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                         {{-- Street --}}
                        <div>
                            <label for="street" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Street') }}</label>
                            <input type="text" name="street" id="street" value="{{ old('street') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('street') border-red-500 @enderror">
                            @error('street') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        {{-- House Number --}}
                        <div>
                            <label for="house_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('House Number') }}</label>
                            <input type="text" name="house_number" id="house_number" value="{{ old('house_number') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('house_number') border-red-500 @enderror">
                            @error('house_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        {{-- Postal Code --}}
                         <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Postal Code') }}</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('postal_code') border-red-500 @enderror">
                            @error('postal_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        {{-- City --}}
                         <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('City') }}</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('city') border-red-500 @enderror">
                            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        {{-- Country --}}
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Country') }}</label>
                             {{-- Consider using a dropdown for country --}}
                            <input type="text" name="country" id="country" value="{{ old('country') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('country') border-red-500 @enderror">
                             @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Create Account Section --}}
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                             <h3 class="text-lg font-semibold mb-4">{{ __('registration.create_account_title') }}</h3>
                             <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ __('registration.create_account_info') }}</p>
                             {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('registration.password') }}</label>
                                <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('password') border-red-500 @enderror">
                                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                             </div>
                             {{-- Confirm Password --}}
                            <div class="mt-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('registration.password_confirmation') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('password_confirmation') border-red-500 @enderror">
                                {{-- No need for specific password_confirmation error display, 'confirmed' rule handles it --}}
                            </div>
                        </div>
                    </div>
                @else {{-- If Auth --}}
                     {{-- Hidden fields for Auth user details (needed for validation consistency) --}}
                     <input type="hidden" name="first_name" value="{{ Auth::user()->first_name ?? '' }}">
                     <input type="hidden" name="last_name" value="{{ Auth::user()->last_name ?? '' }}">
                     <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                     <input type="hidden" name="phone" value="{{ Auth::user()->phone_number ?? '' }}">
                     <input type="hidden" name="street" value="{{ Auth::user()->street ?? '' }}">
                     <input type="hidden" name="house_number" value="{{ Auth::user()->house_number ?? '' }}">
                     <input type="hidden" name="postal_code" value="{{ Auth::user()->postal_code ?? '' }}">
                     <input type="hidden" name="city" value="{{ Auth::user()->city ?? '' }}">
                     <input type="hidden" name="country" value="{{ Auth::user()->country ?? '' }}">
                @endguest

                {{-- Submit Section --}}
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-6"> {{-- Adjusted margin --}}
                    {!! __('By clicking confirm, you agree to our :terms_of_service and :privacy_policy.', [
                        'terms_of_service' => '<a href="#" onclick="alert(\''.__('terms.popup_message').'\'); return false;" class="underline hover:text-red-700">'.__('Terms of Service').'</a>',
                        'privacy_policy' => '<a href="#" onclick="alert(\''.__('privacy.popup_message').'\'); return false;" class="underline hover:text-red-700">'.__('Privacy Policy').'</a>'
                    ]) !!}
                </p>

                <button type="submit" class="w-full mt-4 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    {{ __('Confirm & Request Booking') }}
                </button>
            </form>
        </div>