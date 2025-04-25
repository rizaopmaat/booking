<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \\App\\Models\\User  $user
     * @return \\Illuminate\\Auth\\Access\\Response|bool
     */
    public function viewAny(User $user)
    {
        // Iedere ingelogde user mag zijn eigen lijst zien (controller regelt welke)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \\App\\Models\\User  $user
     * @param  \\App\\Models\\Booking  $booking
     * @return \\Illuminate\\Auth\\Access\\Response|bool
     */
    public function view(User $user, Booking $booking)
    {
        // User mag boeking zien als het zijn/haar boeking is OF als hij/zij admin is
        // Zorg dat $user->is_admin correct is geimplementeerd op je User model!
        return $user->id === $booking->user_id || ($user->is_admin ?? false);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \\App\\Models\\User  $user
     * @return \\Illuminate\\Auth\\Access\\Response|bool
     */
    public function create(User $user)
    {
        // Iedere ingelogde user mag proberen te boeken
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \\App\\Models\\User  $user
     * @param  \\App\\Models\\Booking  $booking
     * @return \\Illuminate\\Auth\\Access\\Response|bool
     */
    public function update(User $user, Booking $booking)
    {
        // Alleen admin mag updaten (via admin panel)
        return $user->is_admin ?? false;
    }

    /**
     * Determine whether the user can delete the model.
     * (We gebruiken dit voor annuleren door gebruiker)
     *
     * @param  \\App\\Models\\User  $user
     * @param  \\App\\Models\\Booking  $booking
     * @return \\Illuminate\\Auth\\Access\\Response|bool
     */
    public function delete(User $user, Booking $booking)
    {
         // User mag boeking annuleren als het zijn/haar boeking is OF als hij/zij admin is
        // Extra check: alleen als status niet al cancelled is (controller doet dit ook)
        return (($user->id === $booking->user_id) || ($user->is_admin ?? false)) && $booking->status !== 'cancelled';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \\App\\Models\\User  $user
     * @param  \\App\\Models\\Booking  $booking
     * @return \\Illuminate\\Auth\\Access\\Response|bool
     */
    public function restore(User $user, Booking $booking)
    {
        // Niet van toepassing
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \\App\\Models\\User  $user
     * @param  \\App\\Models\\Booking  $booking
     * @return \\Illuminate\\Auth\\Access\\Response|bool
     */
    public function forceDelete(User $user, Booking $booking)
    {
        // Alleen admin mag permanent verwijderen (indien geimplementeerd)
        return $user->is_admin ?? false;
    }
} 