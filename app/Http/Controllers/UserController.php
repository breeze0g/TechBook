<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Handle user registration
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => true,
        ]);

        // Redirect to login page with success message
        return redirect()->route('login')
            ->with('success', 'Registration successful! Please login with your credentials.');
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        // Validate login input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Get credentials
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Check if user exists and is active
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && !$user->is_active) {
            return redirect()->back()
                ->withErrors(['email' => 'Your account is deactivated. Please contact admin.'])
                ->withInput($request->except('password'));
        }

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            // Redirect based on user role
            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome back, Admin!');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back!');
        }

        // Login failed
        return redirect()->back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->withInput($request->except('password'));
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Display users table (for admin)
     */
    public function showTable()
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized access.');
        }

        $users = User::all();
        return view('table', compact('users'));
    }

    /**
     * Test login (for debugging)
     */
    public function testLogin(Request $request)
    {
        $email = $request->email ?? 'breezemarlon272@gmail.com';
        $password = $request->password ?? '16012218';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'email' => $email
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'User found',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->is_active
            ],
            'password_hash' => $user->password,
            'password_input' => $password,
            'password_match' => Hash::check($password, $user->password)
        ]);
    }

    // ==================== NOTIFICATION METHODS FOR USERS ====================

    /**
     * Get notifications for normal user
     */
    public function getUserNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        $formattedNotifications = $notifications->map(function($notification) {
            // Format message based on type
            $message = $notification->message;
            
            if ($notification->type === 'status_update' && $notification->service) {
                $message = 'Your service for ' . $notification->service->device . ' has been updated to: ' . $notification->service->status;
            }
            
            if ($notification->type === 'new_message') {
                $message = $notification->message;
            }
            
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'message' => $message,
                'is_read' => $notification->is_read,
                'time_ago' => $notification->created_at->diffForHumans(),
                'created_at' => $notification->created_at->format('Y-m-d H:i:s')
            ];
        });
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'notifications' => $formattedNotifications,
                'unread_count' => $unreadCount
            ]);
        }
        
        return view('user.notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark user notification as read
     */
    public function markUserNotificationRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all user notifications as read
     */
    public function markAllUserNotificationsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Get user unread count
     */
    public function getUserUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }
}