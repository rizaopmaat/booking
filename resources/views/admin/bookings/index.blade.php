@extends('layouts.app')

@section('title', __('Manage Bookings'))

@section('content')
<div class="relative py-8">
    <div class="absolute inset-0 bg-gradient-to-r from-gray-50 to-gray-100 h-64"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('Manage Bookings') }}</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">{{ __('View, edit and manage all guest bookings') }}</p>
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
        
        <div x-data="{ guestModalOpen: false, guestDetails: null, infoModalOpen: false }" class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Filter bookings') }}</h3>
                <form action="{{ route('admin.bookings.index') }}" method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                        <select id="status" name="status" class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">{{ __('All statuses') }}</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('bookings.status.confirmed') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('bookings.status.pending') }}</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('bookings.status.cancelled') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="check_in_from" class="block text-sm font-medium text-gray-700">{{ __('Check-in from') }}</label>
                            <button @click.prevent="infoModalOpen = true" type="button" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <input type="date" id="check_in_from" name="check_in_from" value="{{ request('check_in_from') }}" class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="check_in_to" class="block text-sm font-medium text-gray-700">{{ __('Check-in to') }}</label>
                            <button @click.prevent="infoModalOpen = true" type="button" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <input type="date" id="check_in_to" name="check_in_to" value="{{ request('check_in_to') }}" class="block w-full bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 mr-2">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            {{ __('Filter') }}
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
                                {{ __('Reference') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Room') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Dates') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Nights') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Guests') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Total') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Actions') }}
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
                                <div class="text-sm text-gray-900">{{ $booking->room->getTranslation('name', app()->getLocale()) }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->room->type ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ __('Check In') }}: {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</div>
                                <div class="text-sm text-gray-900">{{ __('Check Out') }}: {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date); @endphp
                                <div class="text-sm text-gray-900">{{ $nights }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $booking->num_guests }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($booking->status === 'confirmed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ __('bookings.status.confirmed') }}
                                </span>
                                @elseif($booking->status === 'pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ __('bookings.status.pending') }}
                                </span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ __('bookings.status.cancelled') }}
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
                                        <button type="submit" class="text-green-600 hover:text-green-900 font-semibold">{{ __('Approve') }}</button>
                                    </form>
                                    <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="inline-block mr-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 font-semibold">{{ __('Reject') }}</button>
                                    </form>
                                @endif
                                
                                <button type="button"
                                        data-guest-details="{{ 
                                            base64_encode(json_encode([
                                                'name' => $booking->user->name,
                                                'email' => $booking->user->email,
                                                'phone' => $booking->user->phone_number ?? 'N/A',
                                                'street' => $booking->user->street,
                                                'houseNumber' => $booking->user->house_number,
                                                'postalCode' => $booking->user->postal_code,
                                                'city' => $booking->user->city,
                                                'country' => $booking->user->country,
                                                'language' => $booking->user->language ?? 'N/A'
                                            ]))
                                        }}"
                                        @click="guestDetails = JSON.parse(atob($el.dataset.guestDetails)); guestModalOpen = true;"
                                        class="text-indigo-600 hover:text-indigo-900 mr-2 font-semibold">
                                    {{ __('Details') }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No bookings found matching the filter criteria.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $bookings->withQueryString()->links() }}
            </div>

            <div x-show="guestModalOpen" 
                 style="display: none;" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed z-10 inset-0 overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="guestModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                         @click="guestModalOpen = false" 
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="guestModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                        {{ __('admin.bookings.guest_details') }}
                                    </h3>
                                    <div class="mt-2">
                                        <dl class="space-y-2" x-show="guestDetails">
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">{{ __('admin.bookings.guest_name') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900" x-text="guestDetails?.name || 'N/A'"></dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">{{ __('admin.bookings.guest_email') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900" x-text="guestDetails?.email || 'N/A'"></dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">{{ __('Language') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900 uppercase" x-text="guestDetails?.language || 'N/A'"></dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">{{ __('admin.bookings.guest_phone') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900" x-text="guestDetails?.phone || 'N/A'"></dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">{{ __('admin.bookings.guest_address') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900">
                                                    <span x-text="guestDetails?.street || ''"></span> 
                                                    <span x-text="guestDetails?.houseNumber || ''"></span><br>
                                                    <span x-text="guestDetails?.postalCode || ''"></span> 
                                                    <span x-text="guestDetails?.city || ''"></span><br>
                                                    <span x-text="guestDetails?.country || ''"></span>
                                                    <span x-show="!(guestDetails?.street || guestDetails?.houseNumber || guestDetails?.postalCode || guestDetails?.city || guestDetails?.country)">N/A</span>
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button @click="guestModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ __('admin.bookings.close_modal') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal voor filter uitleg --}}
            <div x-show="infoModalOpen" 
                 style="display: none;" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed z-10 inset-0 overflow-y-auto" 
                 aria-labelledby="info-modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    {{-- Achtergrond overlay --}}
                    <div x-show="infoModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                         @click="infoModalOpen = false" 
                         aria-hidden="true"></div>

                    {{-- Centrerings element --}}
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    {{-- Modal paneel --}}
                    <div x-show="infoModalOpen" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="info-modal-title">
                                        {{ __('admin.bookings.filter_info_title') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-gray-600 space-y-3 whitespace-pre-line">
                                        {{ __('admin.bookings.filter_info_explanation') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button @click="infoModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ __('admin.bookings.filter_info_close') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 