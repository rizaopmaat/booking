@extends('layouts.app')

@section('title', __('Boeking bewerken'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">{{ __('Boeking bewerken') }}</h1>
        <a href="{{ route('admin.bookings') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            {{ __('Terug naar boekingen') }}
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Gebruiker selecteren -->
                <div>
                    <label for="user_id" class="block text-gray-700 font-medium mb-2">{{ __('Gast') }}</label>
                    <select id="user_id" name="user_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $booking->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kamer selecteren -->
                <div>
                    <label for="room_id" class="block text-gray-700 font-medium mb-2">{{ __('Kamer') }}</label>
                    <select id="room_id" name="room_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} (€{{ $room->price }}/nacht)
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Check-in datum -->
                <div>
                    <label for="check_in" class="block text-gray-700 font-medium mb-2">{{ __('Check-in') }}</label>
                    <input type="date" id="check_in" name="check_in" value="{{ $booking->check_in }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error('check_in')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Check-out datum -->
                <div>
                    <label for="check_out" class="block text-gray-700 font-medium mb-2">{{ __('Check-out') }}</label>
                    <input type="date" id="check_out" name="check_out" value="{{ $booking->check_out }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error('check_out')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Aantal gasten -->
                <div>
                    <label for="guests" class="block text-gray-700 font-medium mb-2">{{ __('Aantal gasten') }}</label>
                    <input type="number" id="guests" name="guests" value="{{ $booking->guests }}" min="1" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error('guests')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-gray-700 font-medium mb-2">{{ __('Status') }}</label>
                    <select id="status" name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>{{ __('In afwachting') }}</option>
                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>{{ __('Bevestigd') }}</option>
                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>{{ __('Geannuleerd') }}</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Totale prijs -->
                <div>
                    <label for="total_price" class="block text-gray-700 font-medium mb-2">{{ __('Totale prijs') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">€</span>
                        <input type="number" id="total_price" name="total_price" value="{{ $booking->total_price }}" min="0" step="0.01" class="w-full pl-8 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    @error('total_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    {{ __('Boeking bijwerken') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 