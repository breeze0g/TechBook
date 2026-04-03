<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - techBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-logo {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .user-details h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }

        .user-details small {
            font-size: 11px;
            opacity: 0.8;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.1);
            padding-left: 30px;
        }

        .sidebar-menu a.active {
            background: rgba(255,255,255,0.2);
            border-left: 4px solid white;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: #f8f9fa;
            padding: 20px;
        }

        .service-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Chat Styles */
        .chat-container {
            border-left: 2px solid #f0f0f0;
        }

        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .message.user {
            align-items: flex-end;
        }

        .message.admin {
            align-items: flex-start;
        }

        .message-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
            position: relative;
        }

        .message.user .message-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message.admin .message-bubble {
            background: #e9ecef;
            color: #333;
            border-bottom-left-radius: 5px;
        }

        .message-time {
            font-size: 10px;
            margin-top: 5px;
            color: #999;
        }

        .message.user .message-time {
            text-align: right;
        }

        .chat-input-area {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .chat-input-area input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 14px;
        }

        .chat-input-area input:focus {
            outline: none;
            border-color: #667eea;
        }

        .chat-input-area button {
            padding: 10px 20px;
            border-radius: 25px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .chat-input-area button:hover {
            transform: translateY(-2px);
        }

        .chat-toggle-btn {
            margin-top: 10px;
            width: 100%;
            padding: 8px;
            border-radius: 20px;
            border: none;
            background: #667eea;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .chat-toggle-btn:hover {
            background: #764ba2;
        }

        .unread-badge {
            background: #ef4444;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            margin-left: 5px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>

    <!-- Favicon - Using Font Awesome as fallback -->
<link rel="icon" type="image/png" sizes="32x32" href="https://img.icons8.com/color/48/repair-tools.png">
<link rel="apple-touch-icon" sizes="180x180" href="https://img.icons8.com/color/48/repair-tools.png">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <h3><i class="fas fa-cogs me-2"></i>techBook</h3>
        <p>User Dashboard</p>
    </div>
    
    <div class="user-info">
        <img class="user-avatar" src="{{ auth()->user()->profile_pic ? asset('storage/' . auth()->user()->profile_pic) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=667eea&color=fff&size=100' }}" alt="User">
        <div class="user-details">
            <h6>{{ auth()->user()->name }}</h6>
            <small>{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Member' }}</small>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <a href="{{ route('dashboard') }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="{{ route('profile.edit') }}">
            <i class="fas fa-user"></i> My Profile
        </a>
        <a href="{{ route('my-services') }}" class="active">
            <i class="fas fa-tools"></i> My Bookings
        </a>
    </div>
    
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">My Service Bookings</h2>

        @forelse($services as $service)
        <div class="service-card" data-service-id="{{ $service->id }}">
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $service->device }}</h5>
                                <small class="text-muted">Booking #{{ $service->id }} | Booked: {{ $service->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            <span class="badge 
                                @if($service->status == 'Pending') bg-warning
                                @elseif($service->status == 'In Progress') bg-info
                                @elseif($service->status == 'Completed') bg-success
                                @else bg-danger
                                @endif">
                                {{ $service->status }}
                            </span>
                        </div>

                        <div class="bg-light rounded p-3 mb-3">
                            <strong>Issue Description:</strong>
                            <p class="mb-0 mt-2">{{ $service->issue }}</p>
                        </div>

                        @php
                            $images = $service->images ? json_decode($service->images, true) : [];
                        @endphp
                        @if(!empty($images))
                        <div class="mb-3">
                            <strong>Issue Photos:</strong>
                            <div class="d-flex gap-2 mt-2">
                                @foreach(array_slice($images, 0, 3) as $image)
                                <img src="{{ asset('storage/' . $image) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer;" onclick="window.open('{{ asset('storage/' . $image) }}')">
                                @endforeach
                                @if(count($images) > 3)
                                <span class="text-muted">+{{ count($images) - 3 }} more</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <div class="chat-container ps-3">
                            <div class="chat-messages" id="chat-messages-{{ $service->id }}" style="height: 300px; display: none;">
                                <div class="text-center py-4">Loading messages...</div>
                            </div>
                            <button class="chat-toggle-btn" onclick="toggleChat({{ $service->id }})">
                                <i class="fas fa-comment-dots me-2"></i> Chat with Admin
                                <span id="unread-badge-{{ $service->id }}" class="unread-badge" style="display: none;">0</span>
                            </button>
                            <div class="chat-input-area" id="chat-input-{{ $service->id }}" style="display: none;">
                                <input type="text" id="message-input-{{ $service->id }}" placeholder="Type your message..." onkeypress="if(event.key === 'Enter') sendMessage({{ $service->id }})">
                                <button onclick="sendMessage({{ $service->id }})"><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded p-5 text-center shadow-sm">
            <i class="fa fa-inbox fa-4x mb-3 text-muted"></i>
            <h5>No Bookings Yet</h5>
            <p class="text-muted">You haven't made any service bookings yet.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Book Your First Service</a>
        </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let activeChats = {};

function toggleChat(serviceId) {
    if (activeChats[serviceId]) {
        document.getElementById(`chat-messages-${serviceId}`).style.display = 'none';
        document.getElementById(`chat-input-${serviceId}`).style.display = 'none';
        activeChats[serviceId] = false;
    } else {
        document.getElementById(`chat-messages-${serviceId}`).style.display = 'block';
        document.getElementById(`chat-input-${serviceId}`).style.display = 'flex';
        activeChats[serviceId] = true;
        loadMessages(serviceId);
    }
}

function loadMessages(serviceId) {
    const messagesContainer = document.getElementById(`chat-messages-${serviceId}`);
    messagesContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2">Loading messages...</p></div>';
    
    fetch(`/service/${serviceId}/chat`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessages(serviceId, data.messages);
                // Mark messages as read
                fetch(`/service/${serviceId}/mark-read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                document.getElementById(`unread-badge-${serviceId}`).style.display = 'none';
            }
        })
        .catch(error => {
            messagesContainer.innerHTML = '<div class="alert alert-danger">Error loading messages</div>';
        });
}

function displayMessages(serviceId, messages) {
    const messagesContainer = document.getElementById(`chat-messages-${serviceId}`);
    if (messages.length === 0) {
        messagesContainer.innerHTML = '<div class="text-center text-muted py-4">No messages yet. Start a conversation with admin!</div>';
        return;
    }
    
    messagesContainer.innerHTML = '';
    messages.forEach(msg => {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${msg.sender_type}`;
        messageDiv.innerHTML = `
            <div class="message-bubble">
                <strong>${msg.sender_name}</strong><br>
                ${escapeHtml(msg.message)}
            </div>
            <div class="message-time">${msg.created_at}</div>
        `;
        messagesContainer.appendChild(messageDiv);
    });
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function sendMessage(serviceId) {
    const input = document.getElementById(`message-input-${serviceId}`);
    const message = input.value.trim();
    
    if (!message) return;
    
    input.disabled = true;
    
    fetch(`/service/${serviceId}/send-message`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadMessages(serviceId);
        } else {
            alert('Error sending message');
        }
    })
    .catch(error => {
        alert('Error sending message');
    })
    .finally(() => {
        input.disabled = false;
        input.focus();
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Auto-refresh messages every 10 seconds for open chats
setInterval(() => {
    for (let serviceId in activeChats) {
        if (activeChats[serviceId]) {
            loadMessages(serviceId);
        }
    }
}, 10000);

// Sidebar toggle
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
}
</script>
</body>
</html>