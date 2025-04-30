<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $mockData = [
            'totalRooms' => 10,
            'activeBookings' => 5,
            'totalGuests' => 25,
            'occupancyRate' => 60,
            'recentBookings' => []
        ];
        
        return view('admin.dashboard', $mockData);
    }
    
    public function bookings(Request $request)
    {
        try {
            $query = Booking::with(['user', 'room'])->latest();
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('check_in_from')) {
                $query->whereDate('check_in', '>=', $request->check_in_from);
            }
            
            if ($request->filled('check_in_to')) {
                $query->whereDate('check_in', '<=', $request->check_in_to);
            }
            
            $bookings = $query->paginate(10);
            
            return view('admin.bookings.index', compact('bookings'));
        } catch (\Exception $e) {
            return view('admin.bookings.index', [
                'error' => __('messages.error_loading_bookings', ['error' => $e->getMessage()]),
                'bookings' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ]);
        }
    }
    
    public function stats()
    {
        return view('admin.stats');
    }
    
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
        
        return redirect()->route('admin.bookings')->with('success', __('messages.booking_updated'));
    }
    
    public function destroyBooking(Booking $booking)
    {
        try {
            $booking->delete();
            return redirect()->route('admin.bookings')->with('success', __('messages.booking_deleted'));
        } catch (\Exception $e) {
            return redirect()->route('admin.bookings')->with('error', __('messages.booking_delete_error', ['error' => $e->getMessage()]));
        }
    }
    
    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);
        
        $booking->update(['status' => $validated['status']]);
        
        return back()->with('success', __('messages.booking_status_updated'));
    }
} 