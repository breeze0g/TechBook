<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - techBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
        }
        .sidebar a.active {
            background: rgba(255,255,255,0.2);
            border-left: 4px solid white;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .stat-card {
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }

        /* Notification Styles */
        .notification-icon {
            position: relative;
            cursor: pointer;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-left: auto;
        }

        .notification-icon:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .notification-icon i {
            font-size: 18px;
            color: #667eea;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .notification-dropdown {
            position: absolute;
            top: 60px;
            right: 20px;
            width: 380px;
            max-height: 500px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            z-index: 1001;
            display: none;
            overflow: hidden;
            animation: slideDown 0.3s ease;
        }

        .notification-dropdown.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h6 {
            margin: 0;
            font-weight: 600;
        }

        .mark-all-read {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .mark-all-read:hover {
            background: rgba(255,255,255,0.3);
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            gap: 12px;
        }

        .notification-item:hover {
            background: #f8f9ff;
        }

        .notification-item.unread {
            background: #f0f4ff;
            border-left: 3px solid #667eea;
        }

        .notification-icon-small {
            width: 35px;
            height: 35px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-icon-small i {
            font-size: 16px;
            color: #667eea;
        }

        .notification-content {
            flex: 1;
        }

        .notification-message {
            font-size: 13px;
            color: #333;
            margin-bottom: 5px;
        }

        .notification-time {
            font-size: 11px;
            color: #999;
        }

        .notification-empty {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .notification-empty i {
            font-size: 40px;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        /* Top bar with notification */
        .top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-cogs me-2"></i>techBook Admin
                    </h4>
                    <hr class="bg-white">
                    <a href="{{ route('admin.dashboard') }}" class="active">
                        <i class="fas fa-dashboard me-2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                    <a href="{{ route('admin.services') }}">
                        <i class="fas fa-tools me-2"></i> Services
                    </a>
                    <a href="{{ route('admin.settings') }}">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                    <a href="{{ route('admin.reports') }}">
                        <i class="fas fa-chart-bar me-2"></i> Reports
                    </a>
                    <hr class="bg-white">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-arrow-left me-2"></i> Back to Site
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-4">
                <!-- Top Bar with Notification Icon -->
                <div class="top-bar">
                    <div class="notification-icon" onclick="toggleNotificationDropdown()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationCount" style="display: none;">0</span>
                    </div>
                </div>

                <!-- Notification Dropdown -->
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h6><i class="fas fa-bell me-2"></i>Notifications</h6>
                        <button class="mark-all-read" onclick="markAllAsRead()">Mark all read</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <div class="notification-empty">
                            <i class="fas fa-bell-slash"></i>
                            <p>No notifications</p>
                        </div>
                    </div>
                </div>

                <!-- Welcome Banner -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-white rounded p-4 shadow-sm">
                            <h4>Welcome back, {{ auth()->user()->name }}! 👋</h4>
                            <p class="mb-0">Here's what's happening with your platform today.</p>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Total Users</h6>
                                        <h2>{{ $totalUsers ?? 0 }}</h2>
                                    </div>
                                    <i class="fas fa-users fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Total Services</h6>
                                        <h2>{{ $totalServices ?? 0 }}</h2>
                                    </div>
                                    <i class="fas fa-tools fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Pending</h6>
                                        <h2>{{ $pendingServices ?? 0 }}</h2>
                                    </div>
                                    <i class="fas fa-clock fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Completed</h6>
                                        <h2>{{ $completedServices ?? 0 }}</h2>
                                    </div>
                                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-users me-2 text-primary"></i>
                                    Recent Users
                                </h5>
                                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentUsers ?? [] as $user)
                                            <tr>
                                                <td>
                                                    <img src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=667eea&color=fff&size=30' }}" 
                                                         class="rounded-circle me-2" width="30" height="30">
                                                    {{ $user->name }}
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->created_at->diffForHumans() }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No users found</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Services -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-tools me-2 text-success"></i>
                                    Recent Services
                                </h5>
                                <a href="{{ route('admin.services') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Device</th>
                                                <th>Status</th>
                                                <th>Booked</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentServices ?? [] as $service)
                                            <tr>
                                                <td>{{ $service->user->name ?? 'N/A' }}</td>
                                                <td>{{ $service->device }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = match($service->status) {
                                                            'Pending' => 'warning',
                                                            'In Progress' => 'info',
                                                            'Completed' => 'success',
                                                            'Cancelled' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}">{{ $service->status }}</span>
                                                </td>
                                                <td>{{ $service->created_at->diffForHumans() }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No services found</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-bolt me-2 text-warning"></i>
                                    Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3 col-sm-6">
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary w-100 py-3">
                                            <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                            Add New User
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <a href="{{ route('admin.services') }}" class="btn btn-outline-success w-100 py-3">
                                            <i class="fas fa-tools fa-2x mb-2 d-block"></i>
                                            Manage Services
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <a href="{{ route('admin.reports') }}" class="btn btn-outline-info w-100 py-3">
                                            <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                                            View Reports
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary w-100 py-3">
                                            <i class="fas fa-cog fa-2x mb-2 d-block"></i>
                                            System Settings
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Toggle notification dropdown
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) {
                loadNotifications();
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const icon = document.querySelector('.notification-icon');
            if (icon && !icon.contains(event.target) && dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Load notifications for admin
        function loadNotifications() {
            fetch('/admin/notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationUI(data.notifications);
                        updateNotificationCount(data.unread_count);
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        // Update notification UI
        function updateNotificationUI(notifications) {
            const container = document.getElementById('notificationList');
            
            if (!notifications || notifications.length === 0) {
                container.innerHTML = `
                    <div class="notification-empty">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = notifications.map(notification => `
                <div class="notification-item ${!notification.is_read ? 'unread' : ''}" data-id="${notification.id}" onclick="markAsRead(${notification.id})">
                    <div class="notification-icon-small">
                        <i class="fas ${notification.type === 'new_booking' ? 'fa-calendar-plus' : (notification.type === 'new_message' ? 'fa-comment-dots' : 'fa-info-circle')}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-message">${escapeHtml(notification.message)}</div>
                        <div class="notification-time">${notification.time_ago}</div>
                    </div>
                </div>
            `).join('');
        }

        // Update notification count badge
        function updateNotificationCount(count) {
            const badge = document.getElementById('notificationCount');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        // Mark single notification as read
        function markAsRead(id) {
            fetch(`/admin/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        // Mark all notifications as read
        function markAllAsRead() {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        }

        // Get unread count periodically
        function updateUnreadCount() {
            fetch('/admin/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    updateNotificationCount(data.count);
                })
                .catch(error => console.error('Error fetching unread count:', error));
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateUnreadCount();
            // Refresh unread count every 30 seconds
            setInterval(updateUnreadCount, 30000);
        });
    </script>
</body>
</html>