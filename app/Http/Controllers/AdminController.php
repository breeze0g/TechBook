<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // ==================== DASHBOARD ====================

    public function dashboard()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $totalUsers = User::count();
        $totalServices = Service::count();
        $pendingServices = Service::where('status', 'Pending')->count();
        $completedServices = Service::where('status', 'Completed')->count();
        $recentUsers = User::latest()->take(5)->get();
        $recentServices = Service::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalServices', 'pendingServices', 
            'completedServices', 'recentUsers', 'recentServices'
        ));
    }

    // ==================== USER MANAGEMENT ====================

    public function users()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $users = User::withCount('services')->get();
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:user,admin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    public function editUser($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:user,admin',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'You cannot delete your own account'], 400);
            }
            return back()->with('error', 'You cannot delete your own account');
        }

        $serviceCount = $user->services()->count();
        $user->services()->delete();
        $user->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
                'deleted_services' => $serviceCount
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    public function bulkDeleteUsers(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $deletedCount = 0;
        $skippedCount = 0;
        $currentUserId = auth()->id();

        foreach ($request->user_ids as $userId) {
            if ($userId == $currentUserId) {
                $skippedCount++;
                continue;
            }
            
            $user = User::find($userId);
            if ($user) {
                $user->services()->delete();
                $user->delete();
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} users deleted successfully. {$skippedCount} users skipped (current user)."
        ]);
    }

    // ==================== SERVICE MANAGEMENT ====================

    public function services()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $services = Service::with('user')->get();
        return view('admin.services.index', compact('services'));
    }

    public function updateServiceStatus(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed,Cancelled'
        ]);

        $service = Service::findOrFail($id);
        $oldStatus = $service->status;
        $service->status = $request->status;
        $service->save();

        // Notify user about status change
        if ($oldStatus !== $request->status) {
            Notification::create([
                'user_id' => $service->user_id,
                'service_id' => $service->id,
                'type' => 'status_update',
                'message' => 'Your service for ' . $service->device . ' has been updated from ' . $oldStatus . ' to ' . $request->status,
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Service status updated successfully');
    }

    public function deleteService($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            if (request()->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $service = Service::findOrFail($id);
        
        // Delete associated images
        if ($service->images) {
            $images = json_decode($service->images, true);
            if ($images && is_array($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
        }
        
        $service->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);
        }

        return redirect()->route('admin.services')->with('success', 'Service deleted successfully');
    }

    public function bulkDeleteServices(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id'
        ]);

        // Delete associated images for each service
        foreach ($request->service_ids as $serviceId) {
            $service = Service::find($serviceId);
            if ($service && $service->images) {
                $images = json_decode($service->images, true);
                if ($images && is_array($images)) {
                    foreach ($images as $image) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }
        }

        Service::whereIn('id', $request->service_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->service_ids) . ' services deleted successfully'
        ]);
    }

    // ==================== SETTINGS MANAGEMENT ====================

    public function settings()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        return view('admin.settings.index');
    }

    public function updateSettings(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        foreach ($request->except('_token') as $key => $value) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('settings', 'public');
                Setting::set($key, $path);
            } else {
                Setting::set($key, $value);
            }
        }

        return back()->with('success', 'Settings updated successfully');
    }

    // ==================== REPORTS ====================

    public function reports()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        // Get date filters from request
        $startDate = request()->get('start');
        $endDate = request()->get('end');

        // Base queries
        $usersQuery = User::query();
        $servicesQuery = Service::query();

        if ($startDate && $endDate) {
            $usersQuery->whereBetween('created_at', [$startDate, $endDate]);
            $servicesQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Summary Statistics
        $totalUsers = $usersQuery->count();
        $totalServices = $servicesQuery->count();
        $pendingServices = (clone $servicesQuery)->where('status', 'Pending')->count();
        $inProgressServices = (clone $servicesQuery)->where('status', 'In Progress')->count();
        $completedServices = (clone $servicesQuery)->where('status', 'Completed')->count();
        $cancelledServices = (clone $servicesQuery)->where('status', 'Cancelled')->count();

        // Monthly trends for the last 6 months
        $monthlyData = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthLabels[] = $month->format('M Y');
            $count = Service::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $monthlyData[] = $count;
        }

        // Top users by bookings
        $topUsers = User::withCount('services')
            ->orderBy('services_count', 'desc')
            ->take(5)
            ->get();

        // Most requested devices
        $topDevices = Service::select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        // Recent services
        $recentServices = Service::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'totalUsers', 'totalServices', 'pendingServices', 'inProgressServices',
            'completedServices', 'cancelledServices', 'monthlyData', 'monthLabels',
            'topUsers', 'topDevices', 'recentServices'
        ));
    }

    // ==================== EXPORT ====================

    public function exportUsers()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $users = User::all();
        
        $csvFileName = 'users_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $callback = function() use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Joined Date']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role ?? 'user',
                    $user->created_at->format('Y-m-d')
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportServices()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $services = Service::with('user')->get();
        
        $csvFileName = 'services_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $callback = function() use ($services) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User', 'Device', 'Issue', 'Status', 'Booked Date', 'Last Updated']);

            foreach ($services as $service) {
                fputcsv($handle, [
                    $service->id,
                    $service->user->name ?? 'N/A',
                    $service->device,
                    $service->issue,
                    $service->status,
                    $service->created_at->format('Y-m-d H:i'),
                    $service->updated_at->format('Y-m-d H:i')
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== NOTIFICATION METHODS FOR ADMIN ====================

    /**
     * Get all notifications for admin
     */
    public function getNotifications()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notifications = Notification::where('user_id', Auth::id())
            ->with('service.user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        $formattedNotifications = $notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'message' => $notification->message,
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
        
        return view('admin.notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a single notification as read for admin
     */
    public function markNotificationRead($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read for admin
     */
    public function markAllNotificationsRead()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Get unread count for admin
     */
    public function getUnreadCount()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['count' => 0]);
        }

        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }
}