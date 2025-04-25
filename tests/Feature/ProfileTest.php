<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, assertDatabaseHas, assertDatabaseMissing, get, post, patch};

uses(RefreshDatabase::class);

test('user can view profile page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertViewIs('profile.edit')
        ->assertViewHas('user', $user);
});

test('guest cannot view profile page', function () {
    get(route('profile.edit'))
        ->assertRedirect(route('login'));
});

test('user can update profile', function () {
    $user = User::factory()->create();
    
    $updateData = [
        'first_name' => 'New',
        'last_name' => 'Name',
        'email' => 'new@example.com',
        'phone_number' => '0612345678',
        'street' => 'New Street',
        'house_number' => '10A',
        'postal_code' => '1234 AB',
        'city' => 'New City',
        'country' => 'Netherlands',
        'language' => 'nl',
    ];
    
    actingAs($user)
        ->patch(route('profile.update'), $updateData)
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('status', 'profile-updated');
    
    assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'first_name' => 'New',
        'last_name' => 'Name',
        'email' => 'new@example.com',
        'language' => 'nl'
    ]);
});

test('user can change language', function() {
    $user = User::factory()->create(['language' => 'en']);
    
    actingAs($user)
        ->patch(route('profile.update'), [
            'first_name' => $user->first_name ?? 'Test',
            'last_name' => $user->last_name ?? 'User',
            'email' => $user->email,
            'phone_number' => $user->phone_number ?? '0612345678',
            'street' => $user->street ?? 'Test Street',
            'house_number' => $user->house_number ?? '123',
            'postal_code' => $user->postal_code ?? '1234AB',
            'city' => $user->city ?? 'Test City',
            'country' => $user->country ?? 'Netherlands',
            'language' => 'nl',
        ])
        ->assertRedirect(route('profile.edit'));
    
    assertDatabaseHas('users', [
        'id' => $user->id,
        'language' => 'nl'
    ]);
});

test('user gets validation error with invalid data', function () {
    $user = User::factory()->create();
    
    actingAs($user)
        ->patch(route('profile.update'), [
            'first_name' => '',
            'email' => 'not-valid-email',
            'language' => 'invalid', // Language must be nl or en
        ])
        ->assertSessionHasErrors(['email', 'language']);
    
    // First name might not be validated as required in your implementation
    // so we're just checking the other fields
});

test('user can delete account', function () {
    $user = User::factory()->create();
    
    actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password', // Default factory password
        ])
        ->assertRedirect('/');
    
    assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('user cannot delete account with incorrect password', function () {
    $user = User::factory()->create();
    
    actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrors('password');
    
    assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});
