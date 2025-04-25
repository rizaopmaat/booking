<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminRoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index(Request $request)
    {
        try {
            $query = Room::query();

            // Filter by availability
            if ($request->filled('is_available')) {
                $query->where('is_available', $request->boolean('is_available'));
            }

            // Search by name (translatable)
            if ($request->filled('search')) {
                $locale = app()->getLocale();
                $query->where('name->' . $locale, 'like', '%' . $request->search . '%');
            }

            $rooms = $query->latest()->paginate(10);

            return view('admin.rooms.index', compact('rooms'));
        } catch (\Exception $e) {
             Log::error('Error loading admin rooms: ' . $e->getMessage());
             return view('admin.rooms.index', [
                'error' => __('messages.error_loading_rooms', ['error' => 'Database connection failed or query error.']),
                'rooms' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ]);
        }
    }

    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        return view('admin.rooms.create');
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.nl' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'description' => 'required|array',
            'description.nl' => 'required|string',
            'description.en' => 'required|string',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'is_available' => 'boolean',
        ]);

        $roomData = $validated;
        unset($roomData['images']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('rooms/main', 'public');
            $roomData['image'] = $imagePath;
        } else {
            $roomData['image'] = null;
        }

        $roomData['is_available'] = $request->boolean('is_available');

        try {
            $room = Room::create($roomData);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $galleryImage) {
                    $galleryImagePath = $galleryImage->store('rooms/gallery', 'public');
                    $room->images()->create([
                        'path' => $galleryImagePath,
                        'order' => $index + 1,
                    ]);
                }
            }

            if (is_null($room->image) && $room->images()->exists()) {
                $firstGalleryImage = $room->images()->orderBy('order')->first();
                if ($firstGalleryImage) {
                    $room->image = $firstGalleryImage->path;
                    $room->save();
                }
            }

            return redirect()->route('admin.rooms.index')->with('success', __('messages.room_created'));
        } catch (\Exception $e) {
            Log::error('Error storing room: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create room. Please check the logs.');
        }
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.nl' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'description' => 'required|array',
            'description.nl' => 'required|string',
            'description.en' => 'required|string',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:room_images,id',
            'is_available' => 'boolean',
        ]);

        $roomData = $validated;
        unset($roomData['images'], $roomData['delete_images']);

        try {
            if ($request->hasFile('image')) {
                if ($room->image) {
                    Storage::disk('public')->delete($room->image);
                }
                $imagePath = $request->file('image')->store('rooms/main', 'public');
                $roomData['image'] = $imagePath;
            }

            $roomData['is_available'] = $request->boolean('is_available');

            $room->update($roomData);

            if ($request->has('delete_images')) {
                $imagesToDelete = RoomImage::whereIn('id', $request->input('delete_images'))
                                          ->where('room_id', $room->id)
                                          ->get();
                foreach ($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }

            if ($request->hasFile('images')) {
                $lastOrder = $room->images()->max('order') ?? 0;
                foreach ($request->file('images') as $index => $galleryImage) {
                    $galleryImagePath = $galleryImage->store('rooms/gallery', 'public');
                    $room->images()->create([
                        'path' => $galleryImagePath,
                        'order' => $lastOrder + $index + 1,
                    ]);
                }
            }

            if (is_null($room->fresh()->image) && $room->images()->exists()) {
                $firstGalleryImage = $room->images()->orderBy('order')->first();
                if ($firstGalleryImage) {
                    $room->image = $firstGalleryImage->path;
                    $room->save();
                }
            }

            return redirect()->route('admin.rooms.index')->with('success', __('messages.room_updated'));

        } catch (\Exception $e) {
            Log::error('Error updating room ' . $room->id . ': ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update room. Please check the logs.');
        }
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Room $room)
    {
        try {
            if ($room->bookings()->exists()) {
                return back()->with('error', __('messages.room_delete_has_bookings'));
            }

            foreach ($room->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }

            if ($room->image) {
                 Storage::disk('public')->delete($room->image);
            }

            $room->delete();

            return redirect()->route('admin.rooms.index')->with('success', __('messages.room_deleted'));

        } catch (\Exception $e) {
            Log::error('Error deleting room ' . $room->id . ': ' . $e->getMessage());
            return back()->with('error', 'Failed to delete room. Please check the logs.');
        }
    }
}
