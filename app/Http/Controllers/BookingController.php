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
        
        $upcomingBookings = $user->bookings()
            ->where('status', 'confirmed')
            ->where('check_in_date', '>=', today())
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
            
        Log::debug('Bookings index for user: ' . $user->id);
        Log::debug('Upcoming Bookings Query Result:', $upcomingBookings->toArray()); 

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
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'num_guests' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', Rule::in(['at_accommodation', 'bank_transfer'])],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'options' => ['nullable', 'array'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        
        $room = Room::findOrFail($validated['room_id']);
        
        if ($validated['num_guests'] > $room->capacity) {
            return back()->withErrors(['num_guests' => __('messages.room_capacity_exceeded')]);
        }

        $checkInDate = Carbon::parse($validated['check_in_date']);
        $checkOutDate = Carbon::parse($validated['check_out_date']);
        $numberOfNights = $checkInDate->diffInDays($checkOutDate);
        
        $totalPrice = $room->price * $numberOfNights;
        
        if ($numberOfNights >= 3) {
            $discount = $totalPrice * 0.15;
            $totalPrice = $totalPrice - $discount; 
            
            // Log for debugging price calculation
            Log::debug("Room price: " . $room->price);
            Log::debug("Number of nights: " . $numberOfNights);
            Log::debug("Base price: " . ($room->price * $numberOfNights));
            Log::debug("Discount: " . $discount);
            Log::debug("Total price after discount: " . $totalPrice);
        }

        $options = [];
        $optionsTotal = 0;
        $selectedOptionsData = [];
        
        if (!empty($validated['options'])) {
            $options_array = $validated['options'];
            
            // Handles both flat array of IDs and associative array [id => quantity]
            if (array_is_list($options_array)) {
                $optionCounts = array_count_values($options_array);
                foreach ($optionCounts as $optionId => $quantity) {
                    $option = BookingOption::findOrFail($optionId);
                    $optionPrice = $this->calculateOptionPrice($option, $numberOfNights, $validated['num_guests']);
                    $options[$optionId] = $this->formatOptionData($option, $quantity, $optionPrice);
                    $selectedOptionsData[] = $this->formatSelectedOptionData($option, $quantity, $optionPrice);
                    $optionsTotal += $optionPrice * $quantity;
                }
            } else {
                foreach ($options_array as $optionId => $quantity) {
                    if ($quantity > 0) {
                        $option = BookingOption::findOrFail($optionId);
                        $optionPrice = $this->calculateOptionPrice($option, $numberOfNights, $validated['num_guests']);
                        $options[$optionId] = $this->formatOptionData($option, $quantity, $optionPrice);
                        $selectedOptionsData[] = $this->formatSelectedOptionData($option, $quantity, $optionPrice);
                        $optionsTotal += $optionPrice * $quantity;
                    }
                }
            }
        }

        $user = Auth::user();
        $newUserRegistered = false;
        if (!$user) {
            $user = User::where('email', $validated['email'])->first();
            if (!$user && !empty($validated['password'])) {
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
                    'language' => App::getLocale(), // Assign current locale
                ]);
                event(new Registered($user));
                Auth::login($user);
                $newUserRegistered = true;
            } else if (!$user) {
                // Guest booking without an account (create a temporary user)
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
                    'language' => App::getLocale(), // Assign current locale
                ]);
                 // Note: Consider if guest users should be logged in or handled differently
            }
        }
        
        if ($user && empty($user->language)){
             $user->language = App::getLocale();
             $user->save();
        }

        // Update profile for logged-in user if details were missing
        if (Auth::check() && !$newUserRegistered) { 
            $profileUpdated = false;
            $userToUpdate = User::find(Auth::id()); // Fetch user model instance from DB

            // Ensure user exists before proceeding
            if ($userToUpdate) {
                // Define fields to potentially update
                $fieldsToUpdate = [
                    'first_name', 'last_name', 'phone_number' => 'phone', // Map model field to request field
                    'street', 'house_number', 'postal_code', 'city', 'country'
                ];

                foreach ($fieldsToUpdate as $modelField => $requestField) {
                    // Handle cases where model field and request field names are the same
                    if (is_int($modelField)) {
                        $modelField = $requestField;
                    }

                    // Check if the profile field is empty and the request field has a value
                    if (empty($userToUpdate->$modelField) && !empty($validated[$requestField])) {
                        $userToUpdate->$modelField = $validated[$requestField];
                        $profileUpdated = true;
                    }
                }

                // Update the 'name' field if first_name or last_name was updated
                if (empty($userToUpdate->name) && !empty($userToUpdate->first_name) && !empty($userToUpdate->last_name)) {
                    $userToUpdate->name = $userToUpdate->first_name . ' ' . $userToUpdate->last_name;
                    $profileUpdated = true;
                }

                if ($profileUpdated) {
                    $userToUpdate->save();
                    Log::info("User profile updated for user ID: " . $userToUpdate->id);
                }
            } else {
                Log::warning("Attempted to update profile for non-existent logged-in user ID: " . Auth::id());
            }
        }

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
            'notes' => $request->input('notes'), // Make sure a 'notes' field exists in form or handle null
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

        if (!empty($options)) {
            foreach ($options as $optionId => $optionData) {
                $booking->options()->attach($optionId, [
                    'quantity' => $optionData['quantity'],
                    'price_at_booking' => $optionData['price'],
                    'price_at_booking_total' => $optionData['price_at_booking_total'],
                ]);
            }
        }

        try {
             Mail::to($booking->user->email)->send(new BookingRequested($booking));
        } catch (\Exception $e) {
             Log::error("Failed to send booking request email for booking {$booking->id}: " . $e->getMessage());
             // Continue without sending email, but log the error
        }
        
        // Prepare success message, considering if a new user was registered
        $successMessage = $newUserRegistered 
                          ? __('messages.welcome_new_user', ['name' => $user->first_name]) 
                          : __('messages.booking_created');

        return redirect()->route('bookings.index')->with('success', $successMessage);
    }
    
    public function show(Booking $booking)
    {
        if (Gate::denies('view', $booking)) {
            abort(403);
        }

        $booking->load(['room', 'user', 'options']); 

        return view('bookings.show', compact('booking'));
    }
    
    public function showConfirmation(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'room_id' => ['required', 'exists:rooms,id'],
                'check_in_date' => ['required', 'date', 'after_or_equal:today'],
                'check_out_date' => ['required', 'date', 'after:check_in_date'],
                'num_guests' => ['required', 'integer', 'min:1'],
                'options' => ['nullable', 'array'],
            ]);

            $room = Room::findOrFail($validated['room_id']);
            
            if ($validated['num_guests'] > $room->capacity) {
                return back()->withErrors(['num_guests' => __('messages.room_capacity_exceeded')]);
            }

            $checkInDate = Carbon::parse($validated['check_in_date']); 
            $checkOutDate = Carbon::parse($validated['check_out_date']);
            $numberOfNights = $checkInDate->diffInDays($checkOutDate);
            
            $totalPrice = $room->price * $numberOfNights;
            
            $durationDiscount = 0;
            $loyaltyDiscount = 0;
            
            if ($numberOfNights >= 3) {
                $durationDiscount = $totalPrice * 0.15;
                $totalPrice = $totalPrice - $durationDiscount;
            }
            
            // Apply loyalty discount if user is logged in, verified, and returning customer
            $isReturningCustomer = false;
            $user = Auth::user();
            if ($user && $user->email_verified_at !== null) {
                $isReturningCustomer = Booking::where('user_id', $user->id)
                                              ->where('status', 'confirmed')
                                              ->exists();
            }
            if ($isReturningCustomer) {
                $loyaltyDiscount = 5; // Fixed discount amount
                $totalPrice -= $loyaltyDiscount;
            }
            $totalPrice = max(0, $totalPrice); // Ensure price doesn't go below 0

            $options = [];
            $optionsTotal = 0;
            $selectedOptionsData = [];
            
            if (!empty($validated['options'])) {
                 $options_array = $validated['options'];
                
                // Handles both flat array of IDs and associative array [id => quantity]
                 if (array_is_list($options_array)) {
                    $optionCounts = array_count_values($options_array);
                    foreach ($optionCounts as $optionId => $quantity) {
                        $option = BookingOption::findOrFail($optionId);
                        $optionPrice = $this->calculateOptionPrice($option, $numberOfNights, $validated['num_guests']);
                        $options[$optionId] = $this->formatOptionData($option, $quantity, $optionPrice);
                        $selectedOptionsData[] = $this->formatSelectedOptionData($option, $quantity, $optionPrice);
                        $optionsTotal += $optionPrice * $quantity;
                    }
                } else {
                    foreach ($options_array as $optionId => $quantity) {
                        if ($quantity > 0) {
                            $option = BookingOption::findOrFail($optionId);
                            $optionPrice = $this->calculateOptionPrice($option, $numberOfNights, $validated['num_guests']);
                            $options[$optionId] = $this->formatOptionData($option, $quantity, $optionPrice);
                            $selectedOptionsData[] = $this->formatSelectedOptionData($option, $quantity, $optionPrice);
                            $optionsTotal += $optionPrice * $quantity;
                        }
                    }
                }
            }
        
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
                'user' => $user
            ]);
        }

        return redirect()->route('rooms.index');
    }
    
    public function cancel(Booking $booking)
    {
        if (Gate::denies('delete', $booking)) {
            abort(403);
        }
        
        if ($booking->status === 'cancelled') { 
             return back()->with('error', 'Booking is already cancelled.');
        }
        
        $originalLocale = App::getLocale();
        try {
            $booking->update(['status' => 'cancelled']);

            $booking->loadMissing('user');
             if ($booking->user && $booking->user->language) {
                try {
                    App::setLocale($booking->user->language);
                    Mail::to($booking->user)->send(new \App\Mail\BookingCancelled($booking));
                    App::setLocale($originalLocale); 
                } catch (\Exception $e) {
                    Log::error("Failed to send booking cancelled email for booking {$booking->id}: " . $e->getMessage());
                    App::setLocale($originalLocale);
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
    
    // Helper method to calculate option price
    private function calculateOptionPrice(BookingOption $option, int $numberOfNights, int $numGuests): float
    {
        switch ($option->price_type) {
            case 'fixed':
                return $option->price;
            case 'per_night':
                return $option->price * $numberOfNights;
            case 'per_guest':
                return $option->price * $numGuests;
            default:
                return 0;
        }
    }

    // Helper method to format option data for internal use/booking creation
    private function formatOptionData(BookingOption $option, int $quantity, float $optionPrice): array
    {
        return [
            'name' => $option->name, // Keep original name potentially
            'price' => $option->price,
            'price_type' => $option->price_type,
            'quantity' => $quantity,
            'price_at_booking_total' => $optionPrice * $quantity,
        ];
    }

    // Helper method to format option data for display (with translation)
    private function formatSelectedOptionData(BookingOption $option, int $quantity, float $optionPrice): array
    {
        return [
            'name' => $option->getTranslation('name', app()->getLocale()),
            'quantity' => $quantity,
            'total_price' => $optionPrice * $quantity
        ];
    }
} 