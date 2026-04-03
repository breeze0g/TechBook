<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Show the profile edit page
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Load user's services count for statistics
        $user->loadCount('services');
        $user->load('services');
        
        return view('profile.edit', compact('user'));
    }

    /**
     * Handle profile update
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate the request
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|min:6|confirmed',
            'password_confirmation' => 'nullable|min:6',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update basic info
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->filled('address')) {
            $user->address = $request->address;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle regular profile picture upload (without cropping)
        if ($request->hasFile('profile_pic') && !$request->has('cropped_image')) {
            $this->handleRegularUpload($request, $user);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Upload and crop profile picture
     */
    public function uploadAvatar(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $user = Auth::user();
            
            // Handle the uploaded image
            $file = $request->file('profile_pic');
            
            // Generate unique filename
            $filename = 'avatar_' . time() . '_' . Str::random(10) . '.jpg';
            
            // Store the file in storage/app/public/profile_pictures
            $path = $file->storeAs('profile_pictures', $filename, 'public');
            
            // Delete old profile picture if exists
            if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
                Storage::disk('public')->delete($user->profile_pic);
            }
            
            // Update user record
            $user->profile_pic = $path;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'image_url' => asset('storage/' . $path)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle regular file upload (without cropping)
     */
    private function handleRegularUpload(Request $request, $user)
    {
        $file = $request->file('profile_pic');
        
        // Generate unique filename
        $filename = 'profile_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Store the file in storage/app/public/profile_pictures
        $path = $file->storeAs('profile_pictures', $filename, 'public');
        
        // Delete old profile picture if exists
        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }
        
        $user->profile_pic = $path;
    }

    /**
     * Get profile statistics
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_bookings' => $user->services()->count(),
            'pending' => $user->services()->where('status', 'Pending')->count(),
            'in_progress' => $user->services()->where('status', 'In Progress')->count(),
            'completed' => $user->services()->where('status', 'Completed')->count(),
            'cancelled' => $user->services()->where('status', 'Cancelled')->count(),
        ];
        
        return response()->json($stats);
    }

    /**
     * Show user's bookings
     */
    public function myServices()
    {
        $user = Auth::user();
        $services = $user->services()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('profile.services', compact('services', 'user'));
    }

    /**
     * Show profile page (alias for edit)
     */
    public function show()
    {
        return $this->edit();
    }

    /**
     * Delete profile picture
     */
    public function deleteAvatar()
    {
        try {
            $user = Auth::user();
            
            // Delete the profile picture file
            if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
                Storage::disk('public')->delete($user->profile_pic);
            }
            
            // Remove the reference from database
            $user->profile_pic = null;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Profile picture removed successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing profile picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password only
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully!');
    }

    /**
     * Update profile settings (JSON API endpoint)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Get user notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('profile.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }
}