<x-mail::message>
# {{ __('emails.booking_cancelled.title') }}

{{ __('emails.greeting', ['name' => $booking->user->name]) }},

{{ __('emails.booking_cancelled.intro') }}

**{{ __('emails.details_title') }}**
*   **{{ __('emails.details.room') }}:** {{ $booking->room->name }}
*   **{{ __('emails.details.check_in') }}:** {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}
*   **{{ __('emails.details.check_out') }}:** {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}
*   **{{ __('emails.details.status') }}:** {{ __('bookings.status.cancelled') }}

{{ __('emails.booking_cancelled.outro') }}

{{ __('emails.regards') }},
{{ __('emails.team_name', ['name' => config('app.name')]) }}
</x-mail::message>
