<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'num_guests',
        'status',
        'total_price',
        'options_total',
        'payment_method',
        'reference',
        'notes',
        'guest_first_name',
        'guest_last_name',
        'guest_email',
        'guest_phone',
        'street',
        'house_number',
        'postal_code',
        'city',
        'country',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_price' => 'decimal:2',
        'options_total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($booking) {
            $booking->reference = (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the options associated with the booking.
     */
    public function options()
    {
        return $this->belongsToMany(BookingOption::class, 'booking_booking_option')
                    ->withPivot('quantity', 'price_at_booking')
                    ->withTimestamps();
    }

    // Helper to check if the cancellation option was chosen
    public function hasCancellationOption()
    {
        return $this->options()->where('is_cancellation_option', true)->exists();
    }
} 