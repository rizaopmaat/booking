<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;    // Import Room model
use App\Models\User;    // Import User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Facades\Mail; // Import Mail facade
use Illuminate\Support\Facades\App; // Import App facade

class AdminBookingController extends Controller
{
    /**
     * Display a listing of the bookings.
     */
    public function index(Request $request)
    {
        try {
            $query = Booking::with(['user', 'room'])->latest(); // Eager load relations

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('check_in_from')) {
                $query->whereDate('check_in_date', '>=', $request->check_in_from);
            }
            if ($request->filled('check_in_to')) {
                $query->whereDate('check_in_date', '<=', $request->check_in_to);
            }
            // Add more filters if needed (e.g., search by user name, room name)

            $bookings = $query->paginate(10);

            return view('admin.bookings.index', compact('bookings'));
        } catch (\Exception $e) {
            Log::error('Error loading admin bookings: ' . $e->getMessage());
            return view('admin.bookings.index', [
                'error' => __('messages.error_loading_bookings', ['error' => 'Database connection failed or query error.']), // Generic error
                'bookings' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ]);
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        // Eager load relations for the view
        $booking->load(['user', 'room']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        // Fetch rooms and users for dropdowns in the edit form
        $rooms = Room::orderBy('name->' . app()->getLocale())->get(); // Order by translated name
        $users = User::where('is_admin', false)->orderBy('name')->get();
        return view('admin.bookings.edit', compact('booking', 'rooms', 'users'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        // TODO: Add validation for room capacity based on guests and dates
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'user_id' => 'required|exists:users,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'status' => 'required|in:pending,confirmed,cancelled',
            'total_price' => 'required|numeric|min:0',
        ]);

        try {
            $booking->update($validated);
            return redirect()->route('admin.bookings.index')->with('success', __('messages.booking_updated'));
        } catch (\Exception $e) {
            Log::error('Error updating booking ' . $booking->id . ': ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update booking. Please check the logs.');
        }
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        try {
            $booking->delete();
            return redirect()->route('admin.bookings.index')->with('success', __('messages.booking_deleted'));
        } catch (\Exception $e) {
            Log::error('Error deleting booking ' . $booking->id . ': ' . $e->getMessage());
            return back()->with('error', __('messages.booking_delete_error', ['error' => 'Operation failed.'])); // Generic error for user
        }
    }

     /**
     * Update the status of the specified booking.
     * This is a custom action, not part of the standard resource controller.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        try {
            // Store original locale
            $originalLocale = App::getLocale();
            $newStatus = $validated['status'];

            // Update booking status first
            $booking->update(['status' => $newStatus]);

            // Send email based on new status
            $booking->loadMissing('user');
            if ($booking->user && $booking->user->language) {
                App::setLocale($booking->user->language);
                try {
                    if ($newStatus === 'confirmed') {
                        Mail::to($booking->user)->send(new \App\Mail\BookingConfirmed($booking));
                    } elseif ($newStatus === 'cancelled') {
                        Mail::to($booking->user)->send(new \App\Mail\BookingCancelled($booking));
                    }
                     // Restore original locale after potentially sending mail
                    App::setLocale($originalLocale); 
                } catch (\Exception $e) {
                    Log::error("Failed to send booking status update email for booking {$booking->id} (status: {$newStatus}): " . $e->getMessage());
                    // Restore original locale even if mail fails
                    App::setLocale($originalLocale);
                }
            } else {
                 Log::warning("Could not send status update email for booking {$booking->id}: User or user language not found.");
            }

            return back()->with('success', __('messages.booking_status_updated'));

        } catch (\Exception $e) {
            Log::error('Error updating booking status ' . $booking->id . ': ' . $e->getMessage());
            return back()->with('error', 'Failed to update booking status. Please check the logs.');
        }
    }
}
