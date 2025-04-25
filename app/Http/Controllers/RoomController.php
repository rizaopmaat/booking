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
        // Haal input op of bepaal standaardwaarden voor de VIEW
        $isInitialLoad = !$request->has('check_in') && !$request->has('check_out'); // Controleer of er *geen* datums zijn meegegeven

        if ($isInitialLoad) {
            // Standaardwaarden voor eerste bezoek
            $checkInValueForView = Carbon::today()->toDateString();
            $checkOutValueForView = Carbon::tomorrow()->toDateString();
            $numGuestsValueForView = 2;
        } else {
            // Gebruik waarden uit de request (kunnen leeg zijn)
            $checkInValueForView = $request->input('check_in_date');
            $checkOutValueForView = $request->input('check_out_date');
            $numGuestsValueForView = $request->input('num_guests'); // Hernoemd voor consistentie
        }

        // Bepaal of ingelogde gebruiker een terugkerende klant is EN geverifieerd
        $isReturningCustomer = false;
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            $isReturningCustomer = Booking::where('user_id', Auth::id())
                                          ->where('status', 'confirmed')
                                          ->exists();
        }

        // Parse datums voor de zoeklogica alleen als ze daadwerkelijk zijn ingevuld
        $checkIn = $request->filled('check_in') ? Carbon::parse($request->input('check_in')) : null;
        $checkOut = $request->filled('check_out') ? Carbon::parse($request->input('check_out')) : null;
        $searchNumGuests = $request->input('num_guests'); // Hernoemd voor consistentie

        $roomsResult = collect();
        $availability = [];
        $paginatedRooms = null;
        $numberOfNights = null; // Initialiseer aantal nachten
        $totalPricePerRoom = []; // Array voor berekende totaalprijzen

        // Voer zoekopdracht EN prijsberekening uit als BEIDE datums geldig zijn
        if ($checkIn && $checkOut && $checkOut->isAfter($checkIn)) {
            $numberOfNights = $checkIn->diffInDays($checkOut); // Bereken aantal nachten
            
            $query = Room::where('is_available', true);

            // Filter op gasten alleen als deze in de request is meegegeven
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

                $availableCount = max(0, $room->total_inventory - $bookedCount);
                $availability[$room->id] = $availableCount;
                $roomsResult->push($room);

                // Totaalprijs berekenen voor deze kamer en deze periode
                $subtotal = $room->price * $numberOfNights;
                $durationDiscount = 0;
                $loyaltyDiscount = 0;
                $finalTotal = $subtotal;

                // Verblijfsduurkorting
                if ($numberOfNights >= 3) {
                    $durationDiscount = $subtotal * 0.15;
                    $finalTotal -= $durationDiscount;
                }
                // Loyaliteitskorting
                if ($isReturningCustomer) {
                    $loyaltyDiscount = 5; // Vaste waarde
                    $finalTotal -= $loyaltyDiscount;
                }
                $finalTotal = max(0, $finalTotal); // Voorkom negatieve prijs

                // Sla berekende prijzen op
                $totalPricePerRoom[$room->id] = [
                    'subtotal' => $subtotal,
                    'duration_discount' => $durationDiscount,
                    'loyalty_discount' => $loyaltyDiscount,
                    'total' => $finalTotal
                ];
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

        // Geef de waarden voor de view door, INCLUSIEF $isReturningCustomer
        return view('rooms.index', [
            'rooms' => $paginatedRooms, // Blijft null als er niet is gezocht
            'availability' => $availability,
            'check_in' => $checkInValueForView, // Gebruik de mogelijk standaard waarde
            'check_out' => $checkOutValueForView, // Gebruik de mogelijk standaard waarde
            'numGuests' => $numGuestsValueForView, // Hernoemd voor consistentie
            'isReturningCustomer' => $isReturningCustomer, // Geef status door
            'numberOfNights' => $numberOfNights, // Geef aantal nachten door
            'totalPricePerRoom' => $totalPricePerRoom // Geef berekende prijzen door
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

        // Haal actieve booking options op
        $activeOptions = BookingOption::where('is_active', true)->get();

        // Transformeer de opties om de juiste vertalingen te krijgen
        $locale = App::getLocale(); // Krijg huidige locale
        $translatedOptions = $activeOptions->map(function ($option) use ($locale) {
            return [
                'id' => $option->id,
                'name' => $option->getTranslation('name', $locale),
                'description' => $option->getTranslation('description', $locale),
                'price' => $option->price,
                'price_type' => $option->price_type,
                // Voeg andere velden toe indien nodig in Alpine.js
            ];
        });

        // Geef kamer, loyaliteitsstatus en de *vertaalde* opties door
        return view('rooms.show', [
            'room' => $room,
            'isReturningCustomer' => $isReturningCustomer,
            'bookingOptions' => $translatedOptions // Geef de getransformeerde data door
        ]);
    }
} 