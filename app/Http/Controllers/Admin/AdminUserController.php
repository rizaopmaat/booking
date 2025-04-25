<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();
            
            // Zoeken op naam of email
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Filter op admin status
            if ($request->filled('is_admin')) {
                $query->where('is_admin', $request->boolean('is_admin'));
            }
            
            $users = $query->latest()->paginate(10);
            
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error loading admin users: ' . $e->getMessage());
            return back()->with('error', __('messages.error_loading_users'));
        }
    }

    /**
     * Toon het formulier om een gebruiker te bewerken
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update de gebruiker
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone_number' => 'nullable|string|max:255',
                'is_admin' => 'boolean',
                'password' => 'nullable|string|min:8|confirmed',
            ]);
            
            // Update de naam (combinatie van voornaam en achternaam)
            $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
            
            // Alleen wachtwoord updaten als het is ingevuld
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }
            
            $user->update($validated);
            
            return redirect()->route('admin.users.index')->with('success', __('messages.user_updated'));
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->withInput()->with('error', __('messages.error_updating_user'));
        }
    }

    /**
     * Toggle admin status
     */
    public function toggleAdmin(User $user)
    {
        try {
            // Voorkom dat de laatste admin zijn rechten verliest
            if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
                return back()->with('error', __('messages.must_have_one_admin'));
            }
            
            $user->update(['is_admin' => !$user->is_admin]);
            
            $message = $user->is_admin ? 
                __('messages.user_made_admin', ['name' => $user->name]) : 
                __('messages.user_admin_status_revoked', ['name' => $user->name]);
                
            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error toggling admin status: ' . $e->getMessage());
            return back()->with('error', __('messages.error_toggling_admin'));
        }
    }

    /**
     * Verwijder een gebruiker
     */
    public function destroy(User $user)
    {
        try {
            // Voorkom dat de laatste admin wordt verwijderd
            if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
                return back()->with('error', __('messages.cannot_delete_last_admin'));
            }
            
            // Check of de gebruiker boekingen heeft
            if ($user->bookings()->exists()) {
                return back()->with('error', __('messages.user_delete_has_bookings'));
            }
            
            $user->delete();
            
            return redirect()->route('admin.users.index')->with('success', __('messages.user_deleted'));
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return back()->with('error', __('messages.error_deleting_user'));
        }
    }
} 