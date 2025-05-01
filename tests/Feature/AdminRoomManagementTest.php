<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{actingAs, assertDatabaseHas, assertDatabaseMissing, delete, get, patch, post};

uses(RefreshDatabase::class);

// Helper to create an admin user
function createAdmin(): User
{
    return User::factory()->create(['is_admin' => true]);
}

// Helper for basic room data
function getRoomData(array $overrides = []): array
{
    $faker = \Faker\Factory::create();
    return array_merge([
        'name' => [
            'nl' => 'Test Kamer NL', 
            'en' => 'Test Room EN'
        ],
        'description' => [
            'nl' => $faker->paragraph(),
            'en' => $faker->paragraph(),
        ],
        'price' => 100.50,
        'capacity' => 2,
        'is_available' => true,
        // Images are handled separately in tests
    ], $overrides);
}

test('admin can view room index page', function () {
    $admin = createAdmin();
    Room::factory()->count(3)->create(); // Create some rooms

    actingAs($admin)
        ->get(route('admin.rooms.index'))
        ->assertOk()
        ->assertViewIs('admin.rooms.index')
        ->assertViewHas('rooms');
});

test('admin can view create room page', function () {
    $admin = createAdmin();

    actingAs($admin)
        ->get(route('admin.rooms.create'))
        ->assertOk()
        ->assertViewIs('admin.rooms.create');
});

test('admin can create a new room without images', function () {
    Storage::fake('public'); // Fake storage
    $admin = createAdmin();
    $roomData = getRoomData();

    actingAs($admin)
        ->post(route('admin.rooms.store'), $roomData)
        ->assertRedirect(route('admin.rooms.index'))
        ->assertSessionHas('success', __('messages.room_created'));

    // Check database (without image path check)
    assertDatabaseHas('rooms', [
        'name->nl' => $roomData['name']['nl'],
        'name->en' => $roomData['name']['en'],
        'price' => $roomData['price'],
        'capacity' => $roomData['capacity'],
        'is_available' => $roomData['is_available'],
    ]);
});

test('admin cannot create a room with invalid data', function () {
    $admin = createAdmin();
    $invalidData = getRoomData(['name' => ['nl' => ''], 'price' => -10]); // Invalid name and price

    actingAs($admin)
        ->post(route('admin.rooms.store'), $invalidData)
        ->assertSessionHasErrors(['name.nl', 'price']);
});

test('admin can view edit room page', function () {
    $admin = createAdmin();
    $room = Room::factory()->create();

    actingAs($admin)
        ->get(route('admin.rooms.edit', $room))
        ->assertOk()
        ->assertViewIs('admin.rooms.edit')
        ->assertViewHas('room', $room);
});

test('admin can update a room', function () {
    Storage::fake('public');
    $admin = createAdmin();
    $room = Room::factory()->create();
    $updateData = getRoomData([
        'name' => [
            'nl' => 'Bijgewerkte Kamer NL', 
            'en' => 'Updated Room EN'
        ],
        'price' => 150,
    ]);

    // ADD total_inventory TO UPDATE DATA
    $updateData['total_inventory'] = $room->total_inventory ?? 1;

    actingAs($admin)
        ->patch(route('admin.rooms.update', $room), $updateData)
        ->assertRedirect(route('admin.rooms.index'))
        ->assertSessionHas('success', __('messages.room_updated'));

    assertDatabaseHas('rooms', [
        'id' => $room->id,
        'name->nl' => $updateData['name']['nl'],
        'name->en' => $updateData['name']['en'],
        'price' => $updateData['price'],
    ]);
});

test('admin can update a room and delete gallery images', function () {
    Storage::fake('public');
    $admin = createAdmin();
    
    // Create a room with images using a different approach (without using real images)
    $room = Room::factory()->create();
    $image = $room->images()->create([
        'path' => 'test_image.jpg',
        'order' => 1,
    ]);
    $room->images()->create([
        'path' => 'test_image2.jpg',
        'order' => 2,
    ]);
    
    $updateData = getRoomData([
        'delete_images' => [$image->id],
    ]);

    // ADD total_inventory TO UPDATE DATA
    $updateData['total_inventory'] = $room->total_inventory ?? 1;

    actingAs($admin)
        ->patch(route('admin.rooms.update', $room), $updateData)
        ->assertRedirect(route('admin.rooms.index'));

    Storage::disk('public')->assertMissing($image->path);
    assertDatabaseMissing('room_images', ['id' => $image->id]);
    expect($room->refresh()->images)->toHaveCount(1);
});

test('admin can delete a room without bookings', function () {
    Storage::fake('public');
    $admin = createAdmin();
    
    // Create a room with an image without using UploadedFile
    $room = Room::factory()->create(['image' => 'test_main.jpg']);
    $image = $room->images()->create([
        'path' => 'test_gallery.jpg',
        'order' => 1,
    ]);

    actingAs($admin)
        ->delete(route('admin.rooms.destroy', $room))
        ->assertRedirect(route('admin.rooms.index'))
        ->assertSessionHas('success', __('messages.room_deleted'));

    assertDatabaseMissing('rooms', ['id' => $room->id]);
    assertDatabaseMissing('room_images', ['room_id' => $room->id]);
    
    Storage::disk('public')->assertMissing('test_main.jpg');
    Storage::disk('public')->assertMissing('test_gallery.jpg');
});

test('admin cannot delete a room with existing bookings', function () {
    $admin = createAdmin();
    $room = Room::factory()->create();
    \App\Models\Booking::factory()->create(['room_id' => $room->id]); // Create a booking for the room

    actingAs($admin)
        ->delete(route('admin.rooms.destroy', $room))
        ->assertRedirect()
        ->assertSessionHas('error', __('messages.room_delete_has_bookings'));

    assertDatabaseHas('rooms', ['id' => $room->id]); // Room should still exist
});
