<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $totalRooms = Room::count();
        $today = Carbon::today();

        // Actieve boekingen (check-in vandaag of eerder, check-out vandaag of later, status confirmed)
        $activeBookings = Booking::where('status', 'confirmed')
                                ->whereDate('check_in_date', '<=', $today)
                                ->whereDate('check_out_date', ' > ', $today) // Nog niet uitgecheckt
                                ->count();

        // Pending boekingen (status pending)
        $pendingBookings = Booking::where('status', 'pending')->count();

        // Bezettingsgraad voor vandaag (simpel)
        // Aantal unieke kamers die vandaag bezet zijn (actieve, bevestigde boeking)
        $occupiedRoomsToday = Booking::where('status', 'confirmed')
                                    ->whereDate('check_in_date', '<=', $today)
                                    ->whereDate('check_out_date', ' > ', $today)
                                    ->distinct('room_id')
                                    ->count('room_id');

        $occupancyRate = ($totalRooms > 0) ? round(($occupiedRoomsToday / $totalRooms) * 100) : 0;

        // Recente activiteit (kan uitgebreider, bv. laatste 5 boekingen/updates)
        $recentBookings = Booking::with(['user', 'room'])->latest()->take(5)->get(); // Voorbeeld

        return view('admin.dashboard', compact(
            'totalRooms',
            'activeBookings',
            'pendingBookings',
            'occupancyRate',
            'recentBookings'
        ));
    }

    /**
     * Display statistics (if this is a separate page).
     */
    public function stats()
    {
        // TODO: Fetch actual statistics
        // Rename stats method if it represents the index of a stats resource
        return view('admin.stats');
    }
}
