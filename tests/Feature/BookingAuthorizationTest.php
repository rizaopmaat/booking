<?php

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, get, post, put};

uses(RefreshDatabase::class);

// Helper function to create a user with a booking
function createUserWithBooking(): array
{
    $user = User::factory()->create();
    $room = Room::factory()->create();
    $booking = Booking::factory()->create([
        'user_id' => $user->id,
        'room_id' => $room->id,
        'status' => 'confirmed',
    ]);
    
    return [$user, $booking];
}

test('user can only view own bookings', function () {
    [$user1, $booking1] = createUserWithBooking();
    [$user2, $booking2] = createUserWithBooking();
    
    // User 1 can view their own booking
    actingAs($user1)
        ->get(route('bookings.show', $booking1))
        ->assertOk();
    
    // User 1 CANNOT view User 2's booking
    actingAs($user1)
        ->get(route('bookings.show', $booking2))
        ->assertForbidden();
});

test('guest cannot view booking details', function () {
    [, $booking] = createUserWithBooking();
    
    get(route('bookings.show', $booking))
        ->assertRedirect(route('login'));
});

test('user can only cancel own bookings', function () {
    [$user1, $booking1] = createUserWithBooking();
    [$user2, $booking2] = createUserWithBooking();
    
    // User 1 can cancel their own booking
    actingAs($user1)
        ->put(route('bookings.cancel', $booking1))
        ->assertRedirect();
    
    // User 1 CANNOT cancel User 2's booking
    actingAs($user1)
        ->put(route('bookings.cancel', $booking2))
        ->assertForbidden();
});

test('admin can view bookings of all users', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    [, $booking] = createUserWithBooking(); // Booking of a regular user
    
    actingAs($admin)
        ->get(route('bookings.show', $booking))
        ->assertOk();
});

test('admin can cancel bookings of all users', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    [, $booking] = createUserWithBooking(); // Booking of a regular user
    
    actingAs($admin)
        ->put(route('bookings.cancel', $booking))
        ->assertRedirect()
        ->assertSessionHas('success');
});

test('admin can view all bookings in admin dashboard', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Booking::factory()->count(3)->create(); // Create some bookings
    
    actingAs($admin)
        ->get(route('admin.bookings.index'))
        ->assertOk()
        ->assertViewIs('admin.bookings.index')
        ->assertViewHas('bookings');
});

test('user cannot access admin booking page', function () {
    $user = User::factory()->create(['is_admin' => false]);
    
    actingAs($user)
        ->get(route('admin.bookings.index'))
        ->assertForbidden();
});

test('admin can update booking status', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    [, $booking] = createUserWithBooking();
    
    actingAs($admin)
        ->put(route('admin.bookings.status', $booking), [
            'status' => 'confirmed'
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    $booking->refresh();
    expect($booking->status)->toBe('confirmed');
});
