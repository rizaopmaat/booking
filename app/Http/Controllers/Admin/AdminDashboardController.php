<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // COUNT TOTAL INVENTORY
        $totalInventory = Room::sum('total_inventory');
        $today = Carbon::today();

        $activeBookings = Booking::where('status', 'confirmed')
                                ->whereDate('check_in_date', '<=', $today)
                                ->whereDate('check_out_date', '>', $today)
                                ->count();

        $pendingBookings = Booking::where('status', 'pending')->count();

        $occupiedRoomsToday = Booking::where('status', 'confirmed')
                                    ->whereDate('check_in_date', '<=', $today)
                                    ->whereDate('check_out_date', '>', $today)
                                    ->distinct('room_id')
                                    ->count('room_id');

        // Calculate number of available rooms today
        $availableRoomsToday = max(0, $totalInventory - $activeBookings);

        // Calculate occupancy rate based on total inventory
        $occupancyRate = ($totalInventory > 0) ? round(($activeBookings / $totalInventory) * 100) : 0;

        $recentBookings = Booking::with(['user', 'room'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'availableRoomsToday',
            'activeBookings',
            'pendingBookings',
            'occupancyRate',
            'recentBookings'
        ));
    }

    public function stats()
    {
        return view('admin.stats');
    }
}
