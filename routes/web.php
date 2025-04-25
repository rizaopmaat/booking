<?php

// Import new Admin controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\BookingOptionController;
use App\Http\Controllers\Admin\AdminUserController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

// Home route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Language Switcher
Route::get('language/{locale}', [LanguageController::class, 'switchLang'])->name('language');

// Authentication routes
require __DIR__.'/auth.php';

// Guest routes
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

// Booking confirmation route - Accessible to guests and logged-in users
Route::match(['get', 'post'], '/booking/confirm', [BookingController::class, 'showConfirmation'])->name('booking.confirmation.show');

// --- Route for storing a booking (guest or authenticated) --- Moved outside auth middleware
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    
    // Allow both POST (from room page) and GET (redirect back on store error) 
    // Route::post('/booking/confirm', [BookingController::class, 'showConfirmation'])->name('booking.confirmation.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Add a new route for the profile completion after registration
    Route::get('/profile/complete', [App\Http\Controllers\ProfileController::class, 'showComplete'])->name('profile.complete');
    Route::post('/profile/complete', [App\Http\Controllers\ProfileController::class, 'saveComplete'])->name('profile.complete.save');
});

// Admin routes - Now using dedicated controllers
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () { 
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); // Use index method
    
    // CRUD for rooms using Route Resource pointing to AdminRoomController
    Route::resource('rooms', AdminRoomController::class)
         ->parameters(['rooms' => 'room'])
         ->except(['show']); // Exclude show as it's typically for front-end view

    // Booking management using Route Resource pointing to AdminBookingController
    Route::resource('bookings', AdminBookingController::class)
         ->parameters(['bookings' => 'booking'])
         ->except(['create', 'store']); // Exclude user actions

    // Custom route for updating booking status remains separate
    Route::put('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');

    // CRUD for Booking Options
    Route::resource('booking-options', BookingOptionController::class)->parameters(['booking-options' => 'option']);
    
    // User management
    Route::resource('users', AdminUserController::class)->except(['show', 'create', 'store']);
    Route::put('/users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
});
