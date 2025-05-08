<?php

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

// 1. Globale Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('language/{locale}', [LanguageController::class, 'switchLang'])->name('language');
require __DIR__.'/auth.php';

// 2. Publieke/Guest Routes
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::match(['get', 'post'], '/booking/confirm', [BookingController::class, 'showConfirmation'])->name('booking.confirmation.show');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

// 3. Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/complete', [ProfileController::class, 'showComplete'])->name('profile.complete');
    Route::post('/profile/complete', [ProfileController::class, 'saveComplete'])->name('profile.complete.save');
});

// 4. Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Room Management
    Route::resource('rooms', AdminRoomController::class);
    Route::delete('rooms/{room}/gallery/{media}', [AdminRoomController::class, 'destroyMedia'])->name('rooms.gallery.destroy');

    // Booking Management
    Route::resource('bookings', AdminBookingController::class)->except(['create', 'store', 'show']);
    Route::put('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');

    // User Management
    Route::resource('users', AdminUserController::class)->except(['create', 'store', 'show']);
    Route::post('users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggleAdmin');
    Route::post('users/{user}/send-reset-link', [AdminUserController::class, 'sendPasswordResetLink'])->name('users.sendResetLink');

    // Booking Options Management
    Route::resource('options', BookingOptionController::class)->except(['show']);

    // Add other admin routes here
});
