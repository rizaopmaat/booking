<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RoomImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'path',
        'caption',
        'order',
    ];

    /**
     * Get the room that owns the image.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Accessor for the full image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->path) {
            return asset('/images/placeholder.png'); // Default placeholder
        }

        // Assuming path is stored relative to the public disk's root
        // e.g., 'rooms/gallery/imagename.jpg' which corresponds to storage/app/public/rooms/gallery/imagename.jpg
        return Storage::disk('public')->url($this->path);
    }

    // If you want the caption to be translatable:
    // use Spatie\Translatable\HasTranslations;
    // public $translatable = ['caption'];
    // protected $casts = ['caption' => 'array'];
}
