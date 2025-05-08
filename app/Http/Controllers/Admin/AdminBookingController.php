<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Booking::with(['user', 'room'])->latest(); 

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Apply date range overlap filter
            $from = $request->filled('check_in_from') ? $request->check_in_from : null;
            $to = $request->filled('check_in_to') ? $request->check_in_to : null;

            // Filter by EXACT check-in date from 'from' field
            if ($from) {
                $query->whereDate('check_in_date', '=', $from);
            }

            // Filter by check-out date <= 'to' field
            if ($to) {
                $query->whereDate('check_out_date', '<=', $to);
            }

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

    public function edit(Booking $booking)
    {
        $rooms = Room::orderBy('name->' . app()->getLocale())->get();
        $users = User::where('is_admin', false)->orderBy('name')->get();
        return view('admin.bookings.edit', compact('booking', 'rooms', 'users'));
    }

    public function update(Request $request, Booking $booking)
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

        try {
            $booking->update($validated);
            return redirect()->route('admin.bookings.index')->with('success', __('messages.booking_updated'));
        } catch (\Exception $e) {
            Log::error('Error updating booking ' . $booking->id . ': ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update booking. Please check the logs.');
        }
    }

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
                    // Restore original locale if mail fails
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
