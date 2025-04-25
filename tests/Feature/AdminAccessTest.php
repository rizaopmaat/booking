<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, get};

uses(RefreshDatabase::class);

test('a guest cannot access admin dashboard', function () {
    get(route('admin.dashboard'))
        ->assertRedirect(route('login')); // Should redirect to login
});

test('a regular user cannot access admin dashboard', function () {
    // Create a regular user (is_admin is false by default)
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden(); // Should return 403 Forbidden
});

test('an admin user can access admin dashboard', function () {
    // Create an admin user
    $adminUser = User::factory()->create(['is_admin' => true]);

    actingAs($adminUser)
        ->get(route('admin.dashboard'))
        ->assertOk(); // Should return 200 OK
});

test('an admin user can access admin room management', function () {
    $adminUser = User::factory()->create(['is_admin' => true]);

    actingAs($adminUser)
        ->get(route('admin.rooms.index'))
        ->assertOk();
});

test('an admin user can access admin booking management', function () {
    $adminUser = User::factory()->create(['is_admin' => true]);

    actingAs($adminUser)
        ->get(route('admin.bookings.index'))
        ->assertOk();
});

// Optional: Tests for guest/regular users on other admin routes
test('a guest cannot access admin room management', function () {
    get(route('admin.rooms.index'))
        ->assertRedirect(route('login'));
});

test('a regular user cannot access admin room management', function () {
    $user = User::factory()->create();
    actingAs($user)
        ->get(route('admin.rooms.index'))
        ->assertForbidden();
});
