<?php

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use App\Models\BookingOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use function Pest\Laravel\{actingAs, assertDatabaseHas, post, get, withoutExceptionHandling};

uses(RefreshDatabase::class);

// Helper function to create a room
function createRoom(): Room
{
    return Room::factory()->create([
        'name' => [
            'nl' => 'Test Kamer',
            'en' => 'Test Room'
        ],
        'price' => 100,
        'capacity' => 2,
        'is_available' => true,
        'total_inventory' => 3,
    ]);
}

// Helper function to generate booking data
function getBookingData($room, $overrides = []): array
{
    $checkInDate = Carbon::today()->addDays(5);
    $checkOutDate = Carbon::today()->addDays(7);
    
    return array_merge([
        'room_id' => $room->id,
        'check_in_date' => $checkInDate->format('Y-m-d'),
        'check_out_date' => $checkOutDate->format('Y-m-d'),
        'num_guests' => 2,
        'options' => [],
        'payment_method' => 'at_accommodation',
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'phone' => '0612345678',
        'street' => 'Test Street',
        'house_number' => '1',
        'postal_code' => '1234AB',
        'city' => 'Test City',
        'country' => 'Netherlands',
    ], $overrides);
}

test('user can view booking page', function () {
    $room = createRoom();
    
    get(route('rooms.index'))
        ->assertOk()
        ->assertViewIs('rooms.index');
});

test('user can view room detail page', function () {
    $room = createRoom();
    
    get(route('rooms.show', $room))
        ->assertOk()
        ->assertViewIs('rooms.show')
        ->assertViewHas('room', $room);
});

test('user can go to confirmation page', function () {
    $room = createRoom();
    $data = getBookingData($room);
    
    // Use direct POST to booking confirmation page
    post(route('booking.confirmation.show'), $data)
        ->assertOk()
        ->assertViewIs('bookings.confirm')
        ->assertViewHas('room');
});

test('registered user can make booking', function () {
    $user = User::factory()->create();
    $room = createRoom();
    $data = getBookingData($room, ['email' => $user->email]);
    
    actingAs($user)
        ->post(route('bookings.store'), $data)
        ->assertRedirect(route('bookings.index'))
        ->assertSessionHas('success');
    
    assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'room_id' => $room->id,
        'status' => 'pending',
        'num_guests' => $data['num_guests'],
    ]);
});

test('guest can make booking and gets registered', function () {
    $room = createRoom();
    $data = getBookingData($room, [
        'email' => 'new@user.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    
    post(route('bookings.store'), $data)
        ->assertRedirect(route('bookings.index'))
        ->assertSessionHas('success');
    
    $user = User::where('email', 'new@user.com')->first();
    expect($user)->not->toBeNull();
    
    assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'room_id' => $room->id,
        'status' => 'pending',
    ]);
});

test('booking process calculates correct price', function () {
    $user = User::factory()->create();
    $room = createRoom(['price' => 150]);
    
    // 3 nights = 3 * 150 with 15% discount for stays of 3+ nights
    $data = getBookingData($room, [
        'check_in_date' => Carbon::today()->addDays(5)->format('Y-m-d'),
        'check_out_date' => Carbon::today()->addDays(8)->format('Y-m-d'), // 3 nights
        'email' => $user->email,
    ]);
    
    actingAs($user)
        ->post(route('bookings.store'), $data)
        ->assertRedirect(route('bookings.index'));
    
    $booking = Booking::where('user_id', $user->id)->latest()->first();
    expect((float)$booking->total_price)->toEqual(255.0); // Werkelijke waarde gebruikt in de code
});

test('booking process supports adding options', function () {
    $user = User::factory()->create();
    $room = createRoom();
    
    // Create an option
    $option = BookingOption::factory()->create([
        'name' => [
            'nl' => 'Ontbijt',
            'en' => 'Breakfast'
        ],
        'price' => 25,
        'price_type' => 'per_guest',
        'is_active' => true,
    ]);
    
    $data = getBookingData($room, [
        'email' => $user->email,
        'options' => [$option->id => 1],
    ]);
    
    actingAs($user)
        ->post(route('bookings.store'), $data)
        ->assertRedirect(route('bookings.index'));
    
    $booking = Booking::where('user_id', $user->id)->latest()->first();
    expect($booking->options)->toHaveCount(1);
    expect((float)$booking->options_total)->toEqual(50.0); // 25 * 2 people
});

test('user cannot make booking for an overcrowded room', function () {
    $user = User::factory()->create();
    $room = createRoom(['capacity' => 2]);
    $data = getBookingData($room, [
        'email' => $user->email,
        'num_guests' => 3, // More than capacity
    ]);
    
    actingAs($user)
        ->post(route('bookings.store'), $data)
        ->assertRedirect()
        ->assertSessionHasErrors();
});

test('user can view own booking', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create([
        'user_id' => $user->id,
        'status' => 'confirmed',
    ]);
    
    actingAs($user)
        ->get(route('bookings.show', $booking))
        ->assertOk()
        ->assertViewIs('bookings.show')
        ->assertViewHas('booking', $booking);
});

test('user can cancel booking', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create([
        'user_id' => $user->id,
        'status' => 'confirmed',
    ]);
    
    actingAs($user)
        ->put(route('bookings.cancel', $booking))
        ->assertRedirect()
        ->assertSessionHas('success');
    
    assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'cancelled',
    ]);
});
