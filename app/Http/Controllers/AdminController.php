<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; // Added for Auth::user() check, though not directly used here for flash messages

class AdminController extends Controller
{
    public function dashboard()
    {
        // Mock data voor het dashboard (geen database nodig)
        $mockData = [
            'totalRooms' => 10,
            'activeBookings' => 5,
            'totalGuests' => 25,
            'occupancyRate' => 60,
            'recentBookings' => []
        ];
        
        return view('admin.dashboard', $mockData);
    }
    
    public function rooms(Request $request)
    {
        try {
            $query = Room::query();
            
            // Eventuele filters toepassen
            if ($request->filled('is_available')) {
                $query->where('is_available', $request->is_available);
            }
            
            // Zoeken op naam - needs update for translatable fields
            if ($request->filled('search')) {
                // Get current locale
                $locale = app()->getLocale();
                // Search in the JSON field for the current locale
                $query->where('name->' . $locale, 'like', '%' . $request->search . '%');
            }
            
            // Pagineren van resultaten
            $rooms = $query->paginate(10);
            
            return view('admin.rooms.index', compact('rooms'));
        } catch (\Exception $e) {
            return view('admin.rooms.index', [
                'error' => __('messages.error_loading_rooms', ['error' => $e->getMessage()]), // Use translation
                'rooms' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ]);
        }
    }
    
    public function bookings(Request $request)
    {
        try {
            $query = Booking::with(['user', 'room'])->latest();
            
            // Toepassen van filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('check_in_from')) {
                $query->whereDate('check_in', '>=', $request->check_in_from);
            }
            
            if ($request->filled('check_in_to')) {
                $query->whereDate('check_in', '<=', $request->check_in_to);
            }
            
            // Pagineren van resultaten
            $bookings = $query->paginate(10);
            
            return view('admin.bookings.index', compact('bookings'));
        } catch (\Exception $e) {
            return view('admin.bookings.index', [
                'error' => __('messages.error_loading_bookings', ['error' => $e->getMessage()]), // Use translation
                'bookings' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ]);
        }
    }
    
    public function stats()
    {
        // Get statistics for the admin dashboard
        return view('admin.stats');
    }
    
    // Room CRUD methods
    public function createRoom()
    {
        return view('admin.rooms.create');
    }
    
    public function storeRoom(Request $request)
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
            // Validate main image (optional)
            'image' => 'nullable|image|max:2048',
            // Validate gallery images (array, each must be an image)
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048', // Validate each file in the array
            'is_available' => 'boolean',
        ]);

        $roomData = $validated;
        unset($roomData['images']); // Remove images array before creating the room

        // Handle main image upload
        if ($request->hasFile('image')) {
            // Store in 'rooms/main' subdirectory for clarity
            $imagePath = $request->file('image')->store('rooms/main', 'public');
            // Store path relative to disk root, e.g., 'rooms/main/...'
            $roomData['image'] = $imagePath;
        } else {
            $roomData['image'] = null; // Ensure it's null if not uploaded
        }


        $roomData['is_available'] = $request->boolean('is_available');

        // Create the room first
        $room = Room::create($roomData);

        // Handle gallery images upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $galleryImage) {
                // Store in 'rooms/gallery' subdirectory
                $galleryImagePath = $galleryImage->store('rooms/gallery', 'public');
                $room->images()->create([
                    'path' => $galleryImagePath, // Store path relative to disk root
                    'order' => $index + 1, // Simple ordering based on upload order
                    // Add caption handling here if needed
                ]);
            }
        }

        // If no main image was uploaded, but gallery images were,
        // set the first gallery image as the main image automatically.
        if (is_null($room->image) && $room->images()->exists()) {
             $firstGalleryImage = $room->images()->orderBy('order')->first();
             if ($firstGalleryImage) {
                 $room->image = $firstGalleryImage->path; // Use the path relative to the disk
                 $room->save();
             }
        }


        return redirect()->route('admin.rooms')->with('success', __('messages.room_created'));
    }
    
    public function editRoom(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }
    
    public function updateRoom(Request $request, Room $room)
    {
        // Validation needs update for translatable fields
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
            'is_available' => 'boolean',
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('rooms', 'public');
            $validated['image'] = '/storage/' . $imagePath;
            // Optionally delete old image if needed
        }

        // Ensure 'is_available' is set
        $validated['is_available'] = $request->boolean('is_available');

        // Update the room
        $room->update($validated);
        
        return redirect()->route('admin.rooms')->with('success', __('messages.room_updated')); // Use translation
    }
    
    public function destroyRoom(Room $room)
    {
        // Check if room has bookings
        if ($room->bookings()->exists()) {
            return back()->with('error', __('messages.room_delete_has_bookings')); // Use translation
        }
        
        // Optionally delete image file
        // ...

        $room->delete();
        
        return redirect()->route('admin.rooms')->with('success', __('messages.room_deleted')); // Use translation
    }
    
    // Booking management
    public function showBooking(Booking $booking)
    {
        return view('admin.bookings.show', compact('booking'));
    }
    
    public function editBooking(Booking $booking)
    {
        $rooms = Room::all();
        $users = User::where('is_admin', false)->get();
        return view('admin.bookings.edit', compact('booking', 'rooms', 'users'));
    }
    
    public function updateBooking(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'user_id' => 'required|exists:users,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'status' => 'required|in:pending,confirmed,cancelled',
            'total_price' => 'required|numeric|min:0',
        ]);
        
        $booking->update($validated);
        
        return redirect()->route('admin.bookings')->with('success', __('messages.booking_updated')); // Use translation
    }
    
    public function destroyBooking(Booking $booking)
    {
        try {
            $booking->delete();
            return redirect()->route('admin.bookings')->with('success', __('messages.booking_deleted')); // Use translation
        } catch (\Exception $e) {
            return redirect()->route('admin.bookings')->with('error', __('messages.booking_delete_error', ['error' => $e->getMessage()])); // Use translation
        }
    }
    
    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);
        
        $booking->update(['status' => $validated['status']]);
        
        return back()->with('success', __('messages.booking_status_updated')); // Use translation
    }
} 