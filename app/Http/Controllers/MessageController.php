<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Message;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    // Get messages for regular user
    public function getMessages($id)
    {
        try {
            $service = Service::where('user_id', Auth::id())->findOrFail($id);
            
            $messages = Message::where('service_id', $id)
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Mark unread admin messages as read
            Message::where('service_id', $id)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            $formattedMessages = [];
            foreach ($messages as $msg) {
                $senderName = $msg->sender_type === 'user' ? 'You' : 'Admin';
                $formattedMessages[] = [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $senderName,
                    'created_at' => $msg->created_at->format('M d, Y h:i A'),
                    'is_read' => $msg->is_read
                ];
            }
            
            return response()->json([
                'success' => true,
                'messages' => $formattedMessages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading messages: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Get messages for admin
    public function getAdminMessages($id)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            $service = Service::findOrFail($id);
            
            $messages = Message::where('service_id', $id)
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Mark unread user messages as read
            Message::where('service_id', $id)
                ->where('sender_type', 'user')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            $formattedMessages = [];
            foreach ($messages as $msg) {
                $senderName = $msg->sender_type === 'user' ? ($service->user->name ?? 'Customer') : 'Admin';
                $formattedMessages[] = [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $senderName,
                    'created_at' => $msg->created_at->format('M d, Y h:i A'),
                    'is_read' => $msg->is_read
                ];
            }
            
            return response()->json([
                'success' => true,
                'messages' => $formattedMessages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading messages: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Send a message
    public function sendMessage(Request $request, $id)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000'
            ]);
            
            $service = Service::findOrFail($id);
            $user = Auth::user();
            
            // Check authorization
            if ($user->role !== 'admin' && $service->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            $message = Message::create([
                'service_id' => $id,
                'user_id' => $service->user_id,
                'admin_id' => $user->role === 'admin' ? $user->id : null,
                'message' => $request->message,
                'sender_type' => $user->role === 'admin' ? 'admin' : 'user',
                'is_read' => false
            ]);
            
            // Create notification
            if ($user->role === 'admin') {
                Notification::create([
                    'user_id' => $service->user_id,
                    'service_id' => $id,
                    'type' => 'new_message',
                    'message' => 'Admin replied to your service request for ' . $service->device,
                    'is_read' => false,
                ]);
            } else {
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'service_id' => $id,
                        'type' => 'new_message',
                        'message' => $user->name . ' sent a message about service for ' . $service->device,
                        'is_read' => false,
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'sender_name' => $user->name,
                    'created_at' => $message->created_at->format('M d, Y h:i A')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Mark messages as read
    public function markMessagesRead($id)
    {
        try {
            $service = Service::findOrFail($id);
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                Message::where('service_id', $id)
                    ->where('sender_type', 'user')
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            } else if ($service->user_id === $user->id) {
                Message::where('service_id', $id)
                    ->where('sender_type', 'admin')
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // Get unread count for admin
    public function getUnreadCount()
    {
        try {
            if (Auth::user()->role !== 'admin') {
                return response()->json(['count' => 0]);
            }
            
            $count = Message::where('sender_type', 'user')
                ->where('is_read', false)
                ->count();
            
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }
}

// Create notification for the other party
if ($user->role === 'admin') {
    // Notify user
    Notification::create([
        'user_id' => $service->user_id,
        'service_id' => $id,
        'type' => 'new_message',
        'message' => 'Admin replied to your service request for ' . $service->device . ': "' . substr($request->message, 0, 50) . '"',
        'is_read' => false,
    ]);
} else {
    // Notify admins
    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        Notification::create([
            'user_id' => $admin->id,
            'service_id' => $id,
            'type' => 'new_message',
            'message' => $user->name . ' sent a message about service for ' . $service->device . ': "' . substr($request->message, 0, 50) . '"',
            'is_read' => false,
        ]);
    }
}