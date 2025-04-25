<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class BookingOption extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'price_type', // 'fixed', 'per_person'
        'is_cancellation_option',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_cancellation_option' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the bookings that have this option.
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_booking_option')
                    ->withPivot('quantity', 'price_at_booking')
                    ->withTimestamps();
    }
}
