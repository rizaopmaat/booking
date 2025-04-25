<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('bookings.table.room') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('bookings.table.check_in') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('bookings.table.check_out') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('bookings.table.guests') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('bookings.table.status') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('bookings.table.total') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('bookings.table.actions') }}
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($bookings as $booking)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $booking->room->getTranslation('name', App::getLocale()) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->num_guests }}</td>
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
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">€{{ number_format($booking->total_price, 2, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    {{-- Always show details link --}}
                    <a href="{{ route('bookings.show', $booking) }}" class="text-red-600 hover:text-red-900 mr-3">{{ __('bookings.actions.details') }}</a>
                    
                    {{-- Conditionally show cancel button based on flag from parent view --}}
                    @if($showCancelButton ?? false)
                        @php
                            // Determine if the booking can be cancelled by the user
                            $canCancel = false;
                            if ($booking->status !== 'cancelled') {
                                if ($booking->status === 'pending') {
                                    $canCancel = true;
                                } 
                                elseif ($booking->status === 'confirmed' && $booking->options->contains(function($option) { 
                                    // Assuming an `is_cancellation_option` boolean field on BookingOption model
                                    return $option->is_cancellation_option ?? false;
                                })) {
                                     $canCancel = true;
                                }
                                // Add other conditions if necessary (e.g., check_in_date > now()->addDays(X))
                            }
                        @endphp
                        @if($canCancel)
                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('bookings.actions.cancel_confirm') }}')">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="text-gray-500 hover:text-red-600">{{ __('bookings.actions.cancel') }}</button>
                        </form>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div> 