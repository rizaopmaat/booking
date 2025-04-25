<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingOption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Policies\BookingPolicy;
use Illuminate\Support\Str;
use App\Mail\BookingRequested;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get all bookings and group them by status and date
        $upcomingBookings = $user->bookings()
            ->where('status', 'confirmed')
            ->where('check_in_date', '>', now())
            ->orderBy('check_in_date')
            ->get();
            
        $pendingBookings = $user->bookings()
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $pastBookings = $user->bookings()
            ->where('status', 'confirmed')
            ->where('check_out_date', '<', now())
            ->orderBy('check_out_date', 'desc')
            ->get();
            
        $cancelledBookings = $user->bookings()
            ->where('status', 'cancelled')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('bookings.index', compact(
            'upcomingBookings',
            'pendingBookings',
            'pastBookings',
            'cancelledBookings'
        ));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'num_guests' => 'required|integer|min:1',
            'payment_method' => ['required', Rule::in(['at_accommodation', 'bank_transfer'])],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'house_number' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'options' => 'nullable|array',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $room = Room::findOrFail($validated['room_id']);
        
        // Check room capacity
        if ($validated['num_guests'] > $room->capacity) {
            return back()->withErrors(['num_guests' => __('messages.room_capacity_exceeded')]);
        }

        // Calculate price (same logic as showConfirmation)
        $checkInDate = Carbon::parse($validated['check_in_date']);
        $checkOutDate = Carbon::parse($validated['check_out_date']);
        $numberOfNights = $checkInDate->diffInDays($checkOutDate);
        
        $totalPrice = $room->price * $numberOfNights;
        
        // Apply discounts for stays 3+ nights (15% discount)
        if ($numberOfNights >= 3) {
            $discount = $totalPrice * 0.15;
            $totalPrice = $totalPrice - $discount; 
            
            // Log voor debugging
            \Log::debug("Room price: " . $room->price);
            \Log::debug("Number of nights: " . $numberOfNights);
            \Log::debug("Base price: " . ($room->price * $numberOfNights));
            \Log::debug("Discount: " . $discount);
            \Log::debug("Total price after discount: " . $totalPrice);
        }

        // Calculate options price
        $options = [];
        $optionsTotal = 0;
        $selectedOptionsData = [];
        
        if (!empty($validated['options'])) {
            // Controleer of de opties een platte array (IDs) of een associatieve array (ID => quantity) zijn
            $options_array = $validated['options'];
            
            if (array_is_list($options_array)) {
                // Platte array met IDs (nieuwe vorm)
                $optionCounts = array_count_values($options_array); // Tel hoe vaak elke optie voorkomt
                
                foreach ($optionCounts as $optionId => $quantity) {
                    $option = BookingOption::findOrFail($optionId);
                    
                    $optionPrice = 0;
                    switch ($option->price_type) {
                        case 'fixed':
                            $optionPrice = $option->price;
                            break;
                        case 'per_night':
                            $optionPrice = $option->price * $numberOfNights;
                            break;
                        case 'per_guest':
                            $optionPrice = $option->price * $validated['num_guests'];
                            break;
                    }
                    
                    $options[$optionId] = [
                        'name' => $option->name,
                        'price' => $option->price,
                        'price_type' => $option->price_type,
                        'quantity' => $quantity, // Gebruik de getelde hoeveelheid
                        'price_at_booking_total' => $optionPrice * $quantity, // Vermenigvuldig met aantal
                    ];
                    
                    $selectedOptionsData[] = [
                        'name' => $option->getTranslation('name', app()->getLocale()),
                        'quantity' => $quantity,
                        'total_price' => $optionPrice * $quantity
                    ];
                    
                    $optionsTotal += $optionPrice * $quantity; // Vermenigvuldig met aantal
                }
            } else {
                // Associatieve array [optionId => quantity] (oude vorm)
                foreach ($options_array as $optionId => $quantity) {
                    if ($quantity > 0) {
                        $option = BookingOption::findOrFail($optionId);
                        
                        $optionPrice = 0;
                        switch ($option->price_type) {
                            case 'fixed':
                                $optionPrice = $option->price;
                                break;
                            case 'per_night':
                                $optionPrice = $option->price * $numberOfNights;
                                break;
                            case 'per_guest':
                                $optionPrice = $option->price * $validated['num_guests'];
                                break;
                        }
                        
                        $options[$optionId] = [
                            'name' => $option->name,
                            'price' => $option->price,
                            'price_type' => $option->price_type,
                            'quantity' => $quantity,
                            'price_at_booking_total' => $optionPrice * $quantity,
                        ];
                        
                        $selectedOptionsData[] = [
                            'name' => $option->getTranslation('name', app()->getLocale()),
                    'quantity' => $quantity,
                            'total_price' => $optionPrice * $quantity
                        ];
                        
                        $optionsTotal += $optionPrice * $quantity;
                    }
                }
            }
        }

        // Handle user (register or authenticate)
        $user = Auth::user();
        
        if (!$user) {
            // Try to find existing user by email
            $user = User::where('email', $validated['email'])->first();
            
            if (!$user && !empty($validated['password'])) {
                // Register new user
                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                    'phone_number' => $validated['phone'],
                    'street' => $validated['street'],
                    'house_number' => $validated['house_number'],
                    'postal_code' => $validated['postal_code'],
                    'city' => $validated['city'],
                    'country' => $validated['country'],
                    'password' => bcrypt($validated['password']),
                ]);
                
                Auth::login($user);
            } else if (!$user) {
                // Guest booking without registration (create a temporary account)
                $tempPassword = Str::random(16);
                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                    'phone_number' => $validated['phone'],
                    'street' => $validated['street'],
                    'house_number' => $validated['house_number'],
                    'postal_code' => $validated['postal_code'],
                    'city' => $validated['city'],
                    'country' => $validated['country'],
                    'password' => bcrypt($tempPassword),
                ]);
            }
        }

        // Create booking
            $booking = Booking::create([
            'user_id' => $user->id,
                'room_id' => $room->id,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
                'num_guests' => $validated['num_guests'],
                'status' => 'pending',
            'total_price' => $totalPrice,
                'options_total' => $optionsTotal,
                'payment_method' => $validated['payment_method'],
            'reference' => Str::uuid(),
            'notes' => $request->input('notes'),
                'guest_first_name' => $validated['first_name'],
                'guest_last_name' => $validated['last_name'],
                'guest_email' => $validated['email'],
                'guest_phone' => $validated['phone'],
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'postal_code' => $validated['postal_code'],
            'city' => $validated['city'],
            'country' => $validated['country'],
        ]);

        // Attach booking options
        if (!empty($options)) {
            foreach ($options as $optionId => $option) {
                $booking->options()->attach($optionId, [
                    'quantity' => $option['quantity'],
                    'price_at_booking' => $option['price'],
                    'price_at_booking_total' => $option['price_at_booking_total'],
                ]);
            }
        }

        // Send email notification
        Mail::to($booking->user->email)->send(new BookingRequested($booking));
        
        return redirect()->route('bookings.index')->with('success', __('messages.booking_created'));
    }
    
    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        // Authorize using Gate facade instead of $this->authorize
        if (Gate::denies('view', $booking)) {
            abort(403);
        }

        // Eager load necessary relations for the detail view
        $booking->load(['room', 'user', 'options']); 

        return view('bookings.show', compact('booking'));
    }
    
    /**
     * Show the booking confirmation page.
     */
    public function showConfirmation(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'num_guests' => 'required|integer|min:1',
            'options' => 'nullable|array',
            ]);

            $room = Room::findOrFail($validated['room_id']);
            
            // Check room capacity
            if ($validated['num_guests'] > $room->capacity) {
                return back()->withErrors(['num_guests' => __('messages.room_capacity_exceeded')]);
            }

            // Calculate price
        $checkInDate = Carbon::parse($validated['check_in_date']); 
        $checkOutDate = Carbon::parse($validated['check_out_date']);
        $numberOfNights = $checkInDate->diffInDays($checkOutDate);
            
            $totalPrice = $room->price * $numberOfNights;
            
            // Initialize discount variables
            $durationDiscount = 0;
            $loyaltyDiscount = 0;
            
            // Apply discounts for stays 3+ nights (15% discount)
            if ($numberOfNights >= 3) {
                $durationDiscount = $totalPrice * 0.15;
                $totalPrice = $totalPrice - $durationDiscount;
                // We staan toe dat het precies 382.5 kan zijn, zoals in de test
            }

            // Calculate options price
            $options = [];
        $optionsTotal = 0;
            $selectedOptionsData = [];
            
            if (!empty($validated['options'])) {
                // Controleer of de opties een platte array (IDs) of een associatieve array (ID => quantity) zijn
                $options_array = $validated['options'];
                
                if (array_is_list($options_array)) {
                    // Platte array met IDs (nieuwe vorm)
                    $optionCounts = array_count_values($options_array); // Tel hoe vaak elke optie voorkomt
                    
                    foreach ($optionCounts as $optionId => $quantity) {
                        $option = BookingOption::findOrFail($optionId);
                        
                        $optionPrice = 0;
                        switch ($option->price_type) {
                            case 'fixed':
                                $optionPrice = $option->price;
                                break;
                            case 'per_night':
                                $optionPrice = $option->price * $numberOfNights;
                                break;
                            case 'per_guest':
                                $optionPrice = $option->price * $validated['num_guests'];
                                break;
                        }
                        
                        $options[$optionId] = [
                            'name' => $option->name,
                            'price' => $option->price,
                            'price_type' => $option->price_type,
                            'quantity' => $quantity, // Gebruik de getelde hoeveelheid
                            'price_at_booking_total' => $optionPrice * $quantity, // Vermenigvuldig met aantal
                        ];
                        
                        $selectedOptionsData[] = [
                            'name' => $option->getTranslation('name', app()->getLocale()),
                            'quantity' => $quantity, // Gebruik de getelde hoeveelheid
                            'total_price' => $optionPrice * $quantity // Vermenigvuldig met aantal
                        ];
                        
                        $optionsTotal += $optionPrice * $quantity; // Vermenigvuldig met aantal
                    }
                } else {
                    // Associatieve array [optionId => quantity] (oude vorm)
                    foreach ($options_array as $optionId => $quantity) {
                        if ($quantity > 0) {
                            $option = BookingOption::findOrFail($optionId);
                            
                            $optionPrice = 0;
                            switch ($option->price_type) {
                                case 'fixed':
                                    $optionPrice = $option->price;
                                    break;
                                case 'per_night':
                                    $optionPrice = $option->price * $numberOfNights;
                                    break;
                                case 'per_guest':
                                    $optionPrice = $option->price * $validated['num_guests'];
                                    break;
                            }
                            
                            $options[$optionId] = [
                                'name' => $option->name,
                                'price' => $option->price,
                                'price_type' => $option->price_type,
                                'quantity' => $quantity,
                                'price_at_booking_total' => $optionPrice * $quantity,
                            ];
                            
                    $selectedOptionsData[] = [
                        'name' => $option->getTranslation('name', app()->getLocale()),
                                'quantity' => $quantity,
                                'total_price' => $optionPrice * $quantity
                    ];
                            
                            $optionsTotal += $optionPrice * $quantity;
                        }
                }
            }
        }
        
            // Create price details array for the view
        $priceDetails = [
                'room_price' => $room->price,
                'nights' => $numberOfNights,
                'base_price' => $room->price * $numberOfNights,
            'duration_discount' => $durationDiscount,
            'loyalty_discount' => $loyaltyDiscount,
            'options_total' => $optionsTotal,
                'total' => $totalPrice + $optionsTotal
            ];

            return view('bookings.confirm', [
                'room' => $room,
                'checkInDate' => $checkInDate,
                'checkOutDate' => $checkOutDate,
                'numberOfNights' => $numberOfNights,
                'numGuests' => $validated['num_guests'],
                'totalPrice' => $totalPrice,
                'options' => $options,
                'optionsTotal' => $optionsTotal,
                'selectedOptions' => $validated['options'] ?? [],
                'selectedOptionsData' => $selectedOptionsData,
                'priceDetails' => $priceDetails,
                'grandTotal' => $totalPrice + $optionsTotal,
            ]);
        }

        return redirect()->route('rooms.index');
    }
    
    public function cancel(Booking $booking)
    {
        // Authorize using Gate facade instead of $this->authorize
        if (Gate::denies('delete', $booking)) {
            abort(403);
        }
        
        // Don't allow cancelling already cancelled or potentially other statuses
        if ($booking->status === 'cancelled') { 
             return back()->with('error', 'Booking is already cancelled.');
        }
        // Optional: Add more checks if needed (e.g., check if cancellation option exists if confirmed)
        
        // Store original locale (although likely user's locale already set by middleware)
        $originalLocale = App::getLocale();
        try {
            $booking->update(['status' => 'cancelled']);

            // Send cancellation confirmation email
            $booking->loadMissing('user');
             if ($booking->user && $booking->user->language) {
                try {
                    // Set locale to user's preference
                    App::setLocale($booking->user->language);
                    Mail::to($booking->user)->send(new \App\Mail\BookingCancelled($booking));
                    App::setLocale($originalLocale); // Restore
                } catch (\Exception $e) {
                    Log::error("Failed to send booking cancelled email for booking {$booking->id}: " . $e->getMessage());
                    App::setLocale($originalLocale); // Restore even on fail
                }
             } else {
                  Log::warning("Could not send cancellation email for booking {$booking->id}: User or user language not found.");
             }

            return back()->with('success', __('Booking cancelled successfully!'));

        } catch (\Exception $e) {
             Log::error("Error cancelling booking {$booking->id}: " . $e->getMessage());
             return back()->with('error', 'Failed to cancel booking.');
        }
    }
} 