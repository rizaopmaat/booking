<x-mail::message>
# {{ __('emails.booking_requested.title') }}

{{ __('emails.greeting', ['name' => $booking->user->name]) }},

{{ __('emails.booking_requested.intro_received') }}
{{ __('emails.booking_requested.intro_review') }}

{{ __('emails.booking_requested.details_intro') }}

**{{ __('emails.details_title') }}**
*   **{{ __('emails.details.room') }}:** {{ $booking->room->name }}
*   **{{ __('emails.details.check_in') }}:** {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}
*   **{{ __('emails.details.check_out') }}:** {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}
*   **{{ __('emails.details.nights') }}:** {{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }}
*   **{{ __('emails.details.guests') }}:** {{ $booking->num_guests }}
*   **{{ __('emails.details.status') }}:** {{ __('bookings.status.' . $booking->status) }}

@if($booking->options->isNotEmpty())
**{{ __('emails.details.options_title') }}**
@foreach($booking->options as $option)
*   {{ $option->pivot->quantity }}x {{ $option->getTranslation('name', App::getLocale()) }} (€{{ number_format($option->pivot->price_at_booking * $option->pivot->quantity, 2) }})
@endforeach

@endif
**{{ __('emails.booking_requested.total_indicative') }}:** €{{ number_format($booking->total_price, 2) }}

{{ __('emails.booking_requested.outro') }}

{{ __('emails.regards') }},
{{ __('emails.team_name', ['name' => config('app.name')]) }}
</x-mail::message>
