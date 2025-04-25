@extends('layouts.app')

@section('title', __('bookings.meta_title'))

@section('content')
{{-- Add Alpine.js context for modals --}}
<div x-data="{ termsModalOpen: false, privacyModalOpen: false }" class="bg-gradient-to-br from-red-50 to-red-100 py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold font-playfair text-center text-red-800 mb-6">{{ __('bookings.title') }}</h1>
        <p class="text-lg text-center text-gray-600 mb-10 max-w-3xl mx-auto">{{ __('bookings.description') }}</p>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('warning') }}</span>
            </div>
        @endif

        @if($upcomingBookings->isEmpty() && $pendingBookings->isEmpty() && $pastBookings->isEmpty() && $cancelledBookings->isEmpty())
            {{-- Message if absolutely no bookings exist --}}
            <div class="text-center py-12 bg-white rounded-lg shadow-md">
                 <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6m4-6v6a2 2 0 002 2h2a2 2 0 002-2v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2z"></path></svg>
                 <p class="text-xl text-gray-600 mb-4">{{ __('bookings.no_bookings_found') }}</p>
                 <a href="{{ route('rooms.index') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                     {{ __('bookings.view_available_rooms') }}
                 </a>
             </div>
        @else

            {{-- Upcoming Bookings Section --}}
            <div class="mb-12 bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                    <h2 class="text-2xl font-semibold font-playfair text-green-800">{{ __('bookings.upcoming_stays') }}</h2>
                </div>
                @if($upcomingBookings->isEmpty())
                    <p class="px-6 py-4 text-gray-500">{{ __('Geen aankomende boekingen gevonden.') }}</p>
                @else
                    @include('bookings.partials.booking_table', ['bookings' => $upcomingBookings, 'showCancelButton' => true])
                @endif
            </div>

            {{-- Pending Bookings Section --}}
            <div class="mb-12 bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
                    <h2 class="text-2xl font-semibold font-playfair text-yellow-800">{{ __('bookings.status.pending') }}</h2>
                </div>
                @if($pendingBookings->isEmpty())
                    <p class="px-6 py-4 text-gray-500">{{ __('Geen boekingen in behandeling.') }}</p>
                @else
                    @include('bookings.partials.booking_table', ['bookings' => $pendingBookings, 'showCancelButton' => true]) {{-- Cancellation might be allowed for pending --}}
                @endif
            </div>

            {{-- Past Bookings Section --}}
            <div class="mb-12 bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-2xl font-semibold font-playfair text-gray-800">{{ __('Eerdere Verblijven') }}</h2> {{-- Add translation --}}
                </div>
                @if($pastBookings->isEmpty())
                    <p class="px-6 py-4 text-gray-500">{{ __('Geen eerdere verblijven gevonden.') }}</p>
                @else
                    @include('bookings.partials.booking_table', ['bookings' => $pastBookings, 'showCancelButton' => false])
                @endif
            </div>

            {{-- Cancelled Bookings Section --}}
            <div class="mb-12 bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                    <h2 class="text-2xl font-semibold font-playfair text-red-800">{{ __('bookings.status.cancelled') }}</h2>
                </div>
                @if($cancelledBookings->isEmpty())
                    <p class="px-6 py-4 text-gray-500">{{ __('Geen geannuleerde boekingen gevonden.') }}</p>
                @else
                    @include('bookings.partials.booking_table', ['bookings' => $cancelledBookings, 'showCancelButton' => false])
                @endif
            </div>

        @endif

        {{-- Help Section --}}
        <div class="mt-10 bg-white rounded-lg shadow p-6 text-center">
             <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('bookings.help.title') }}</h3>
             <p class="text-gray-600 mb-4">{{ __('bookings.help.description') }}</p>
             <div class="flex justify-center space-x-4">
                 {{-- Replaced link with button for terms modal --}}
                 <button type="button" @click="termsModalOpen = true" class="text-red-600 hover:text-red-800 font-medium underline cursor-pointer">
                    {{ __('bookings.help.change_policy') }}
                 </button>
                 <span class="text-gray-300">|</span>
                 {{-- Replaced link with button for privacy modal --}}
                 <button type="button" @click="privacyModalOpen = true" class="text-red-600 hover:text-red-800 font-medium underline cursor-pointer">
                    {{ __('bookings.help.cancellation_policy') }}
                 </button>
             </div>
         </div>

    </div>

    {{-- START: Modals (Copied from confirm.blade.php) --}}
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