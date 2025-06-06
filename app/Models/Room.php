<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'total_inventory',
        'capacity',
        'image',
        'is_available'
    ];

    /**
     * Accessor for the main featured image URL.
     * Adjusted to handle potential path inconsistencies.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            // Try getting the first gallery image if no main image
            $firstImage = $this->images()->orderBy('order')->first();
            if ($firstImage) {
                return $firstImage->image_url; // Use the accessor from RoomImage
            }
            return asset('/images/placeholder.png'); // Default placeholder if no images at all
        }

        // Correctly generate URL using the public disk for the main image path
        // The stored path (e.g., 'rooms/main/xyz.png') is relative to the disk's root.
        return Storage::disk('public')->url($this->image);
    }

    /**
     * Get the gallery images for the room.
     */
    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class)->orderBy('order'); // Order by the 'order' column
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
} 