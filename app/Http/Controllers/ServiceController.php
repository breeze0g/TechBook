<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\User;
use App\Models\Notification;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    // Show the dashboard with recent bookings
    public function index()
    {
        // Get all services booked by the logged-in user
        $services = Service::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Get notifications for user
        $notifications = Notification::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Return the dashboard view
        return view('index', compact('services', 'notifications'));
    }

    // Show all bookings (full page with progress tracking)
    public function myServices()
    {
        // Get all services booked by the logged-in user
        $services = Service::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Get notifications for user
        $notifications = Notification::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Return the detailed my-services view
        return view('my-services', compact('services', 'notifications'));
    }

    // Book a new repair service with image upload
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'device' => 'required|string|max:255',
            'issue'  => 'required|string',
            'issue_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        // Create a new service record
        $service = Service::create([
            'user_id' => Auth::id(),
            'device'  => $request->device,
            'issue'   => $request->issue,
            'status'  => 'Pending',
        ]);

        // Handle image uploads
        if ($request->hasFile('issue_images')) {
            $imagePaths = [];
            foreach ($request->file('issue_images') as $image) {
                // Generate unique filename
                $filename = 'service_' . $service->id . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                // Store the file
                $path = $image->storeAs('service_images', $filename, 'public');
                $imagePaths[] = $path;
            }
            // Save images as JSON
            $service->images = json_encode($imagePaths);
            $service->save();
        }

        // Create notification for all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'service_id' => $service->id,
                'type' => 'new_booking',
                'message' => Auth::user()->name . ' has booked a new service for ' . $request->device,
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Service booked successfully!');
    }

    // Get single service details (for users - AJAX)
    public function getServiceDetails($id)
    {
        try {
            $service = Service::with('user')->where('user_id', Auth::id())->findOrFail($id);
            
            // Get image URLs
            $images = [];
            if ($service->images) {
                $imagePaths = json_decode($service->images, true);
                if ($imagePaths && is_array($imagePaths)) {
                    foreach ($imagePaths as $path) {
                        $images[] = asset('storage/' . $path);
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'service' => [
                    'id' => $service->id,
                    'device' => $service->device,
                    'issue' => $service->issue,
                    'status' => $service->status,
                    'created_at' => $service->created_at->format('F d, Y h:i A'),
                    'updated_at' => $service->updated_at->format('F d, Y h:i A'),
                    'user_name' => $service->user->name,
                    'user_email' => $service->user->email,
                    'images' => $images,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found: ' . $e->getMessage()
            ], 404);
        }
    }

    // Get service details for admin (AJAX)
    public function getAdminServiceDetails($id)
    {
        try {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            $service = Service::with('user')->findOrFail($id);
            
            // Get image URLs
            $images = [];
            if ($service->images) {
                $imagePaths = json_decode($service->images, true);
                if ($imagePaths && is_array($imagePaths)) {
                    foreach ($imagePaths as $path) {
                        $images[] = asset('storage/' . $path);
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'service' => [
                    'id' => $service->id,
                    'device' => $service->device,
                    'issue' => $service->issue,
                    'status' => $service->status,
                    'created_at' => $service->created_at->format('F d, Y h:i A'),
                    'updated_at' => $service->updated_at->format('F d, Y h:i A'),
                    'user_name' => $service->user->name,
                    'user_email' => $service->user->email,
                    'images' => $images,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found: ' . $e->getMessage()
            ], 404);
        }
    }

    // Cancel a service booking
    public function cancelService($id)
    {
        try {
            $service = Service::where('user_id', Auth::id())->findOrFail($id);
            
            if ($service->status === 'Pending') {
                $service->update(['status' => 'Cancelled']);
                
                // Notify admins about cancellation
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'service_id' => $service->id,
                        'type' => 'cancelled_booking',
                        'message' => Auth::user()->name . ' has cancelled their service for ' . $service->device,
                        'is_read' => false,
                    ]);
                }
                
                return response()->json(['success' => true, 'message' => 'Service cancelled successfully!']);
            }
            
            return response()->json(['success' => false, 'message' => 'Only pending services can be cancelled.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Service not found']);
        }
    }

    // Mark notification as read
    public function markNotificationRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    // Mark all notifications as read
    public function markAllNotificationsRead()
    {
        Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    // Get unread notifications count
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
        
        return response()->json(['count' => $count]);
    }

    // Delete a service (Admin only)
    public function deleteService($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
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
            
            return response()->json(['success' => true, 'message' => 'Service deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Service not found']);
        }
    }

    // Update service status (Admin only)
    public function updateServiceStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized');
        }
        
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed,Cancelled'
        ]);
        
        try {
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
            
            return redirect()->back()->with('success', 'Service status updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Service not found');
        }
    }
}