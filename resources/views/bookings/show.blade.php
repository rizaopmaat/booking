@extends('layouts.app')

@section('title', __('bookings.meta_title') . ' - ' . __('bookings.actions.details'))

@section('content')
<div class="bg-gradient-to-br from-red-50 to-red-100 py-12 px-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="px-6 py-8 sm:px-10">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold font-playfair text-red-800">{{ __('bookings.details') }}</h1>
                    <p class="text-gray-500 mt-1">Booking #{{ $booking->id }}</p>
                </div>
                <a href="{{ route('bookings.index') }}" class="text-red-600 hover:text-red-800 inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    {{ __('Back') }}
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                {{-- Room Details --}}
                <div>
                    <h2 class="text-xl font-semibold font-playfair text-gray-700 mb-3">{{ __('bookings.table.room') }}</h2>
                    <p class="text-gray-600">{{ $booking->room->getTranslation('name', App::getLocale()) }}</p>
                    {{-- Optional: Add room description or link --}}
                    {{-- <p class="text-sm text-gray-500 mt-1">{{ $booking->room->getTranslation('description', App::getLocale()) }}</p> --}}
                </div>

                {{-- Status --}}
                <div>
                    <h2 class="text-xl font-semibold font-playfair text-gray-700 mb-3">{{ __('bookings.table.status') }}</h2>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        @if($booking->status === 'confirmed') bg-green-100 text-green-800 @endif
                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 @endif
                        @if($booking->status === 'cancelled') bg-red-100 text-red-800 @endif">
                        {{ __('bookings.status.' . $booking->status) }}
                    </span>
                </div>

                {{-- Check-in/Check-out Dates --}}
                <div>
                    <h2 class="text-xl font-semibold font-playfair text-gray-700 mb-3">{{ __('bookings.table.check_in') }}</h2>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('l, F j, Y') }}</p>
                </div>
                <div>
                    <h2 class="text-xl font-semibold font-playfair text-gray-700 mb-3">{{ __('bookings.table.check_out') }}</h2>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('l, F j, Y') }}</p>
                </div>

                {{-- Number of Guests --}}
                <div>
                    <h2 class="text-xl font-semibold font-playfair text-gray-700 mb-3">{{ __('bookings.table.guests') }}</h2>
                    <p class="text-gray-600">{{ $booking->num_guests }} {{ trans_choice('Guest|Guests', $booking->num_guests) }}</p>
                </div>

                {{-- Number of Nights --}}
                <div>
                    <h2 class="text-xl font-semibold font-playfair text-gray-700 mb-3">{{ __('Aantal nachten') }}</h2>
                    @php $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date); @endphp
                    <p class="text-gray-600">{{ $nights }} {{ trans_choice('night|nights', $nights) }}</p>
                </div>

                 {{-- Selected Options --}}
                @if($booking->options->isNotEmpty())
                <div class="md:col-span-2 pt-4 mt-4 border-t border-gray-200">
                    <h2 class="text-xl font-semibold font-playfair text-gray-700 mb-3">{{ __('emails.details.options_title') }}</h2>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        @foreach($booking->options as $option)
                        <li>
                            {{ $option->pivot->quantity }}x {{ $option->getTranslation('name', App::getLocale()) }}
                            <span class="text-sm text-gray-500">(€{{ number_format($option->pivot->price_at_booking, 2) }} @if($option->price_type == 'per_person') {{ __('per person') }} @endif)</span>
                             - Totaal: €{{ number_format($option->pivot->price_at_booking * $option->pivot->quantity, 2) }}
                        </li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-sm font-semibold text-gray-700">{{ __('Packages Subtotal') }}: €{{ number_format($booking->options_total, 2) }}</p>
                </div>
                @endif

                {{-- Total Price --}}
                <div class="md:col-span-2 pt-6 mt-6 border-t border-gray-200">
                    <h2 class="text-2xl font-bold font-playfair text-red-800 text-right">{{ __('bookings.table.total') }}: €{{ number_format($booking->total_price, 2) }}</h2>
                </div>
            </div>

            {{-- Cancellation Button (Optional) --}}
            @php
                // Determine if the booking can be cancelled by the user
                $canCancel = false;
                if ($booking->status !== 'cancelled') {
                    // Always allow cancellation if pending
                    if ($booking->status === 'pending') {
                        $canCancel = true;
                    } 
                    // Allow cancellation if confirmed ONLY if a cancellation option was booked
                    elseif ($booking->status === 'confirmed' && $booking->options->contains(function($option) { 
                        // You might need a specific way to identify the cancellation option, 
                        // e.g., by a dedicated column `is_cancellation_option` = true, or by name/ID.
                        // Let's assume an `is_cancellation_option` boolean field on BookingOption model for this example.
                        return $option->is_cancellation_option ?? false; // Check if the option allows cancellation
                    })) {
                         $canCancel = true;
                    }
                    // Add other conditions if necessary (e.g., check_in_date > now()->addDays(X))
                }
            @endphp

            @if($canCancel)
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('bookings.actions.cancel_confirm') }}')">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-gray-200 hover:bg-red-100 text-red-700 font-semibold py-2 px-5 rounded-md transition duration-300">
                        {{ __('bookings.actions.cancel') }}
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection