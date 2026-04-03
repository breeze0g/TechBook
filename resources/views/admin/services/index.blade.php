<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - techBook Admin</title>
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

        .sidebar-menu {
            padding: 20px 0;
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

        .sidebar-menu a i {
            width: 20px;
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

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
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

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .image-gallery {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .image-thumb {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }

        .image-thumb:hover {
            border-color: #667eea;
            transform: scale(1.05);
        }

        .image-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-image {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 10px;
        }

        .filter-btn {
            padding: 8px 20px;
            border-radius: 20px;
            border: 1px solid #ddd;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
        }

        /* Chat Styles */
        .chat-container {
            border-left: 2px solid #f0f0f0;
        }

        .chat-messages {
            height: 350px;
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
            align-items: flex-start;
        }

        .message.admin {
            align-items: flex-end;
        }

        .message-bubble {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
            position: relative;
        }

        .message.user .message-bubble {
            background: #e9ecef;
            color: #333;
            border-bottom-left-radius: 5px;
        }

        .message.admin .message-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message-time {
            font-size: 10px;
            margin-top: 5px;
            color: #999;
        }

        .message.user .message-time {
            text-align: left;
        }

        .message.admin .message-time {
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
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <h3><i class="fas fa-cogs me-2"></i>techBook Admin</h3>
    </div>
    <div class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="{{ route('admin.users') }}">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="{{ route('admin.services') }}" class="active">
            <i class="fas fa-tools"></i> Services
        </a>
        <a href="{{ route('admin.settings') }}">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="{{ route('admin.reports') }}">
            <i class="fas fa-chart-bar"></i> Reports
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
        <h2 class="mb-4">Service Bookings Management</h2>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-number">{{ $services->count() }}</div>
                    <div class="text-muted">Total Services</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-number">{{ $services->where('status', 'Pending')->count() }}</div>
                    <div class="text-muted">Pending</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-number">{{ $services->where('status', 'In Progress')->count() }}</div>
                    <div class="text-muted">In Progress</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-number">{{ $services->where('status', 'Completed')->count() }}</div>
                    <div class="text-muted">Completed</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-white rounded p-3 shadow-sm">
                    <div class="d-flex justify-content-center flex-wrap gap-2">
                        <button class="filter-btn active" onclick="filterServices('all')">All</button>
                        <button class="filter-btn" onclick="filterServices('Pending')">Pending</button>
                        <button class="filter-btn" onclick="filterServices('In Progress')">In Progress</button>
                        <button class="filter-btn" onclick="filterServices('Completed')">Completed</button>
                        <button class="filter-btn" onclick="filterServices('Cancelled')">Cancelled</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services List -->
        @forelse($services as $service)
        <div class="service-card" data-status="{{ $service->status }}" data-service-id="{{ $service->id }}">
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $service->device }}</h5>
                                <small class="text-muted">
                                    Booking #{{ $service->id }} | 
                                    By: {{ $service->user->name ?? 'N/A' }} | 
                                    {{ $service->user->email ?? 'N/A' }}
                                </small>
                            </div>
                            <span class="status-badge 
                                @if($service->status == 'Pending') bg-warning
                                @elseif($service->status == 'In Progress') bg-info
                                @elseif($service->status == 'Completed') bg-success
                                @else bg-danger
                                @endif text-white">
                                {{ $service->status }}
                            </span>
                        </div>

                        <div class="bg-light rounded p-3 mb-3">
                            <strong><i class="fa fa-info-circle me-2"></i>Issue Description:</strong>
                            <p class="mb-0 mt-2">{{ $service->issue }}</p>
                        </div>

                        <!-- Image Gallery -->
                        @php
                            $images = $service->images ? json_decode($service->images, true) : [];
                        @endphp
                        @if(!empty($images) && is_array($images))
                        <div class="mb-3">
                            <strong><i class="fa fa-image me-2"></i>Issue Photos ({{ count($images) }}):</strong>
                            <div class="image-gallery mt-2">
                                @foreach($images as $image)
                                <div class="image-thumb" onclick="viewImage('{{ asset('storage/' . $image) }}')">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Service Image">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="mb-3">
                            <strong><i class="fa fa-image me-2"></i>Issue Photos:</strong>
                            <p class="text-muted mt-2">No images uploaded</p>
                        </div>
                        @endif

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fa fa-calendar me-1"></i> Booked: {{ $service->created_at->format('M d, Y h:i A') }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fa fa-clock me-1"></i> Updated: {{ $service->updated_at->format('M d, Y h:i A') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="chat-container ps-3">
                            <div class="chat-messages" id="chat-messages-{{ $service->id }}" style="height: 300px; display: none;">
                                <div class="text-center py-4">Loading messages...</div>
                            </div>
                            <button class="chat-toggle-btn" onclick="toggleChat({{ $service->id }})">
                                <i class="fas fa-comment-dots me-2"></i> Chat with Customer
                                <span id="unread-badge-{{ $service->id }}" class="unread-badge" style="display: none;">0</span>
                            </button>
                            <div class="chat-input-area" id="chat-input-{{ $service->id }}" style="display: none;">
                                <input type="text" id="message-input-{{ $service->id }}" placeholder="Type your message to customer..." onkeypress="if(event.key === 'Enter') sendAdminMessage({{ $service->id }})">
                                <button onclick="sendAdminMessage({{ $service->id }})"><i class="fas fa-paper-plane"></i> Send</button>
                            </div>
                        </div>
                        
                        <div class="border-start ps-3 mt-3">
                            <label class="form-label fw-bold">Update Status</label>
                            <form action="{{ route('admin.services.status', $service->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select mb-2" onchange="this.form.submit()">
                                    <option value="Pending" {{ $service->status == 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="In Progress" {{ $service->status == 'In Progress' ? 'selected' : '' }}>🔄 In Progress</option>
                                    <option value="Completed" {{ $service->status == 'Completed' ? 'selected' : '' }}>✅ Completed</option>
                                    <option value="Cancelled" {{ $service->status == 'Cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                </select>
                            </form>
                            
                            <button class="btn btn-danger btn-sm w-100 mt-2" onclick="deleteService({{ $service->id }})">
                                <i class="fas fa-trash-alt me-1"></i> Delete Service
                            </button>
                            
                            <button class="btn btn-info btn-sm w-100 mt-2" onclick="viewServiceDetails({{ $service->id }})">
                                <i class="fas fa-eye me-1"></i> Full Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded p-5 text-center shadow-sm">
            <i class="fa fa-inbox fa-4x mb-3 text-muted"></i>
            <h5>No Service Bookings</h5>
            <p class="text-muted">There are no service bookings yet.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="fa fa-image me-2"></i>Service Issue Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" class="modal-image" src="" alt="Service Image">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Service Details Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="fa fa-info-circle me-2"></i>Full Service Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="serviceModalContent">
                <div class="text-center py-4">Loading...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let activeChats = {};

function viewImage(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function filterServices(status) {
    const items = document.querySelectorAll('.service-card');
    const btns = document.querySelectorAll('.filter-btn');
    
    btns.forEach(btn => btn.classList.remove('active'));
    if (event && event.target) {
        event.target.classList.add('active');
    }
    
    items.forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function deleteService(id) {
    if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
        fetch(`/admin/services/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Service deleted successfully');
                location.reload();
            } else {
                alert(data.message || 'Error deleting service');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the service');
        });
    }
}

function viewServiceDetails(id) {
    const modalContent = document.getElementById('serviceModalContent');
    modalContent.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>';
    
    fetch(`/admin/service/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let imagesHtml = '';
                if (data.service.images && data.service.images.length > 0) {
                    imagesHtml = '<div class="mt-3"><strong>Issue Photos:</strong><div class="image-gallery mt-2">';
                    data.service.images.forEach(img => {
                        imagesHtml += `<div class="image-thumb" onclick="viewImage('${img}')"><img src="${img}" alt="Service Image"></div>`;
                    });
                    imagesHtml += '</div></div>';
                } else {
                    imagesHtml = '<div class="mt-3"><strong>Issue Photos:</strong><p class="text-muted">No images uploaded</p></div>';
                }
                
                modalContent.innerHTML = `
                    <table class="table table-bordered">
                        <tr><th width="30%">Booking ID</th><td>#${data.service.id}</td></tr>
                        <tr><th>Customer Name</th><td>${escapeHtml(data.service.user_name)}</td></tr>
                        <tr><th>Customer Email</th><td>${escapeHtml(data.service.user_email)}</td></tr>
                        <tr><th>Device</th><td>${escapeHtml(data.service.device)}</td></tr>
                        <tr><th>Issue Description</th><td>${escapeHtml(data.service.issue)}</td></tr>
                        <tr><th>Status</th><td><span class="badge bg-${data.service.status === 'Completed' ? 'success' : (data.service.status === 'Pending' ? 'warning' : 'info')}">${data.service.status}</span></td></tr>
                        <tr><th>Booked On</th><td>${data.service.created_at}</td></tr>
                        <tr><th>Last Updated</th><td>${data.service.updated_at}</td></tr>
                    </table>
                    ${imagesHtml}
                `;
            } else {
                modalContent.innerHTML = `<div class="alert alert-danger">${data.message || 'Error loading service details'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = '<div class="alert alert-danger">Error loading service details</div>';
        });
    
    new bootstrap.Modal(document.getElementById('serviceModal')).show();
}

// Admin Chat Functions
function toggleChat(serviceId) {
    if (activeChats[serviceId]) {
        document.getElementById(`chat-messages-${serviceId}`).style.display = 'none';
        document.getElementById(`chat-input-${serviceId}`).style.display = 'none';
        activeChats[serviceId] = false;
    } else {
        document.getElementById(`chat-messages-${serviceId}`).style.display = 'block';
        document.getElementById(`chat-input-${serviceId}`).style.display = 'flex';
        activeChats[serviceId] = true;
        loadAdminMessages(serviceId);
    }
}

function loadAdminMessages(serviceId) {
    const messagesContainer = document.getElementById(`chat-messages-${serviceId}`);
    messagesContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2">Loading messages...</p></div>';
    
    fetch(`/admin/service/${serviceId}/chat`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAdminMessages(serviceId, data.messages);
                // Mark messages as read
                fetch(`/service/${serviceId}/mark-read`, { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    } 
                });
                document.getElementById(`unread-badge-${serviceId}`).style.display = 'none';
            }
        })
        .catch(error => {
            messagesContainer.innerHTML = '<div class="alert alert-danger">Error loading messages</div>';
        });
}

function displayAdminMessages(serviceId, messages) {
    const messagesContainer = document.getElementById(`chat-messages-${serviceId}`);
    if (messages.length === 0) {
        messagesContainer.innerHTML = '<div class="text-center text-muted py-4">No messages yet. Start a conversation with the customer!</div>';
        return;
    }
    
    messagesContainer.innerHTML = '';
    messages.forEach(msg => {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${msg.sender_type}`;
        messageDiv.innerHTML = `
            <div class="message-bubble">
                <strong>${escapeHtml(msg.sender_name)}</strong><br>
                ${escapeHtml(msg.message)}
            </div>
            <div class="message-time">${msg.created_at}</div>
        `;
        messagesContainer.appendChild(messageDiv);
    });
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function sendAdminMessage(serviceId) {
    const input = document.getElementById(`message-input-${serviceId}`);
    const message = input.value.trim();
    
    if (!message) return;
    
    input.disabled = true;
    
    fetch(`/service/${serviceId}/send-message`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadAdminMessages(serviceId);
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
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Auto-refresh messages every 10 seconds for open chats
setInterval(() => {
    for (let serviceId in activeChats) {
        if (activeChats[serviceId]) {
            loadAdminMessages(serviceId);
        }
    }
}, 10000);
</script>
</body>
</html>