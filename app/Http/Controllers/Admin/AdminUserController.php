<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::query();
            
            // Search by name or email
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Filter on admin status
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

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

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
            
            $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
            
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

    public function toggleAdmin(User $user)
    {
        try {
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

    public function destroy(User $user)
    {
        try {
            if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
                return back()->with('error', __('messages.cannot_delete_last_admin'));
            }
            
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

    public function sendPasswordResetLink(Request $request, User $user)
    {
        try {
            // Gebruik Laravel's ingebouwde password broker
            $status = Password::broker()->sendResetLink(
                ['email' => $user->email] // Stuur naar het e-mailadres van de gebruiker
            );

            if ($status == Password::RESET_LINK_SENT) {
                return back()->with('success', __('admin.users.reset_link_sent_success', ['email' => $user->email]));
            } else {
                // Log de status voor debugging als het onverwacht is
                Log::warning('Password reset link sending failed for user ' . $user->id . ' with status: ' . $status);
                // Vertaal de status voor de admin (indien mogelijk/bekend)
                return back()->with('error', __($status)); 
            }
        } catch (\Exception $e) {
            Log::error('Error sending password reset link for user ' . $user->id . ': ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred while sending the password reset link.'); // Generieke foutmelding
        }
    }
} 