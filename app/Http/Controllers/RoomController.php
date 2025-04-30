<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\BookingOption;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $isInitialLoad = !$request->has('check_in') && !$request->has('check_out');

        if ($isInitialLoad) {
            $checkInValueForView = Carbon::today()->toDateString();
            $checkOutValueForView = Carbon::tomorrow()->toDateString();
            $numGuestsValueForView = 2;
        } else {
            $checkInValueForView = $request->input('check_in_date');
            $checkOutValueForView = $request->input('check_out_date');
            $numGuestsValueForView = $request->input('num_guests');
        }

        $isReturningCustomer = false;
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            $isReturningCustomer = Booking::where('user_id', Auth::id())
                                          ->where('status', 'confirmed')
                                          ->exists();
        }

        $checkIn = $request->filled('check_in') ? Carbon::parse($request->input('check_in')) : null;
        $checkOut = $request->filled('check_out') ? Carbon::parse($request->input('check_out')) : null;
        $searchNumGuests = $request->input('num_guests');

        $roomsResult = collect();
        $availability = [];
        $paginatedRooms = null;
        $numberOfNights = null;
        $totalPricePerRoom = [];

        if ($checkIn && $checkOut && $checkOut->isAfter($checkIn)) {
            $numberOfNights = $checkIn->diffInDays($checkOut);
            
            $query = Room::where('is_available', true);

            if ($searchNumGuests) {
                $query->where('capacity', '>=', $searchNumGuests);
            }

            $allMatchingRooms = $query->get();

            foreach ($allMatchingRooms as $room) {
                $bookedCount = Booking::where('room_id', $room->id)
                                      ->where('status', 'confirmed')
                                      ->where(function ($q) use ($checkIn, $checkOut) {
                                          $q->where('check_in_date', '<', $checkOut)
                                            ->where('check_out_date', '>', $checkIn);
                                      })
                                      ->count();

                $roomInventory = $room->total_inventory ?? 1;
                $availableCount = max(0, $roomInventory - $bookedCount);
                $availability[$room->id] = $availableCount;
                
                if ($availableCount > 0) { 
                    $roomsResult->push($room);

                    $subtotal = $room->price * $numberOfNights;
                    $durationDiscount = 0;
                    $loyaltyDiscount = 0;
                    $finalTotal = $subtotal;

                    if ($numberOfNights >= 3) {
                        $durationDiscount = $subtotal * 0.15;
                        $finalTotal -= $durationDiscount;
                    }
                    if ($isReturningCustomer) {
                        $loyaltyDiscount = 5;
                        $finalTotal -= $loyaltyDiscount;
                    }
                    $finalTotal = max(0, $finalTotal);

                    $totalPricePerRoom[$room->id] = [
                        'subtotal' => $subtotal,
                        'duration_discount' => $durationDiscount,
                        'loyalty_discount' => $loyaltyDiscount,
                        'total' => $finalTotal
                    ];
                }
            }

            $perPage = 9;
            $currentPage = $request->input('page', 1);
            $pagedRooms = $roomsResult->slice(($currentPage - 1) * $perPage, $perPage);
            $paginatedRooms = new LengthAwarePaginator(
                $pagedRooms,
                $roomsResult->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        return view('rooms.index', [
            'rooms' => $paginatedRooms,
            'availability' => $availability,
            'check_in' => $checkInValueForView,
            'check_out' => $checkOutValueForView,
            'numGuests' => $numGuestsValueForView,
            'isReturningCustomer' => $isReturningCustomer,
            'numberOfNights' => $numberOfNights,
            'totalPricePerRoom' => $totalPricePerRoom
        ]);
    }
    
    public function show(Request $request, Room $room)
    {
        $isReturningCustomer = false;
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            $isReturningCustomer = Booking::where('user_id', Auth::id())
                                          ->where('status', 'confirmed')
                                          ->exists();
        }

        $activeOptions = BookingOption::where('is_active', true)->get();

        $locale = App::getLocale();
        $translatedOptions = $activeOptions->map(function ($option) use ($locale) {
            return [
                'id' => $option->id,
                'name' => $option->getTranslation('name', $locale),
                'description' => $option->getTranslation('description', $locale),
                'price' => $option->price,
                'price_type' => $option->price_type,
            ];
        });

        return view('rooms.show', [
            'room' => $room,
            'isReturningCustomer' => $isReturningCustomer,
            'bookingOptions' => $translatedOptions
        ]);
    }
} 