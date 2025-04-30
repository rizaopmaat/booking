@extends('layouts.app')

@section('title', __('Boekingen beheren'))

@section('content')
<div class="relative py-8">
    <div class="absolute inset-0 bg-gradient-to-r from-gray-50 to-gray-100 h-64"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('Boekingen beheren') }}</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">{{ __('Bekijk, bewerk en beheer alle boekingen van gasten') }}</p>
        </div>
        
        @if(session('success'))
        <div class="rounded-lg bg-green-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Filter boekingen') }}</h3>
                <form action="{{ route('admin.bookings.index') }}" method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                        <select id="status" name="status" class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">{{ __('Alle statussen') }}</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Bevestigd') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('In behandeling') }}</option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>{{ __('Geannuleerd') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="check_in_from" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Check-in vanaf') }}</label>
                        <input type="date" id="check_in_from" name="check_in_from" value="{{ request('check_in_from') }}" class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="check_in_to" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Check-in tot') }}</label>
                        <input type="date" id="check_in_to" name="check_in_to" value="{{ request('check_in_to') }}" class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 mr-2">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            {{ __('Filteren') }}
                        </button>
                        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Referentie') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Gast') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Kamer') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Data') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Gasten') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Totaal') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Acties') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">BK-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->created_at->format('d-m-Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                </div>
                                <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $booking->room->name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->room->type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ __('Check-in') }}: {{ \Carbon\Carbon::parse($booking->check_in)->format('d-m-Y') }}</div>
                                <div class="text-sm text-gray-900">{{ __('Check-out') }}: {{ \Carbon\Carbon::parse($booking->check_out)->format('d-m-Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }} {{ __('nachten') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $booking->num_guests }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($booking->status === 'confirmed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ __('Bevestigd') }}
                                </span>
                                @elseif($booking->status === 'pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ __('In behandeling') }}
                                </span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ __('Geannuleerd') }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">€{{ number_format($booking->total_price, 2, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if ($booking->status === 'pending')
                                    <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="inline-block mr-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="text-green-600 hover:text-green-900 font-semibold">{{ __('Goedkeuren') }}</button>
                                    </form>
                                    <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="inline-block mr-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 font-semibold">{{ __('Afwijzen') }}</button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('Bewerken') }}</a>
                                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                            onclick="return confirm('{!! addslashes(__('Weet je zeker dat je deze boeking wilt verwijderen?')) !!}')">{{ __('Verwijderen') }}</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                {{ __('Geen boekingen gevonden die voldoen aan de filtercriteria.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $bookings->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 