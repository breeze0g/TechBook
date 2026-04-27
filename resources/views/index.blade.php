<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>techBook Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon - Using Font Awesome as fallback -->
<link rel="icon" type="image/png" sizes="32x32" href="https://img.icons8.com/color/48/repair-tools.png">
<link rel="apple-touch-icon" sizes="180x180" href="https://img.icons8.com/color/48/repair-tools.png">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Sidebar Styles */
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

        .sidebar-logo p {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 5px;
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
            position: relative;
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
            font-size: 18px;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 40px;
            min-height: 100vh;
            background: #f8f9fa;
        }

        /* Top Navbar with Notification */
        .top-navbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }

        /* Notification Icon */
        .notification-icon {
            position: relative;
            cursor: pointer;
            background: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .notification-icon:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .notification-icon i {
            font-size: 20px;
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

        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            top: 55px;
            right: 0;
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

        /* Card Styles */
        .dashboard-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .card-header-custom {
            background: white;
            padding: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .card-header-custom h5 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }

        .card-header-custom h5 i {
            color: #667eea;
            margin-right: 10px;
        }

        .card-body-custom {
            padding: 20px;
        }

        /* Form Controls */
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Image Upload Area */
        .image-upload-area {
            border: 2px dashed #e0e0e0;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fafafa;
        }

        .image-upload-area:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .image-upload-area.dragover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .image-preview {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }

        .remove-image:hover {
            transform: scale(1.1);
        }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
            cursor: pointer;
            width: 100%;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-custom {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-custom:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            opacity: 0.9;
        }

        /* Table Styles */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
        }

        .table-custom thead tr {
            background: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
        }

        .table-custom th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }

        .table-custom td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #666;
        }

        .table-custom tbody tr:hover {
            background: #f8f9fa;
        }

        /* Badge Styles */
        .badge-custom {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-pending {
            background: #fff3e0;
            color: #f59e0b;
        }

        .badge-progress {
            background: #e3f2fd;
            color: #3b82f6;
        }

        .badge-completed {
            background: #e8f5e9;
            color: #10b981;
        }

        .badge-cancelled {
            background: #ffebee;
            color: #ef4444;
        }

        /* Alert Styles */
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .welcome-banner h4 {
            color: #333;
            margin-bottom: 8px;
        }

        .welcome-banner p {
            color: #666;
            margin: 0;
        }

        /* User Info */
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

        /* Mobile Menu Button */
        .mobile-menu-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #667eea;
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 10px;
            font-size: 24px;
            cursor: pointer;
            display: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .mobile-menu-btn:hover {
            background: #5a67d8;
            transform: scale(1.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block !important;
            }
            
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .notification-dropdown {
                width: 320px;
                right: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Mobile Hamburger Menu Button -->
<button class="mobile-menu-btn" onclick="toggleSidebar()">☰</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <h3><i class="fas fa-cogs me-2"></i>techBook</h3>
        <p>User Dashboard</p>
    </div>
    
    <div class="user-info">
        <img class="user-avatar" 
             src="{{ auth()->user()->profile_pic ? asset('storage/' . auth()->user()->profile_pic) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=667eea&color=fff&size=100' }}" 
             alt="User">
        <div class="user-details">
            <h6>{{ auth()->user()->name }}</h6>
            <small>{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Member' }}</small>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <a href="{{ route('dashboard') }}" class="active">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('profile.edit') }}">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>
        <a href="{{ route('my-services') }}">
            <i class="fas fa-tools"></i>
            <span>My Bookings</span>
        </a>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}">
                <i class="fas fa-chart-line"></i>
                <span>Admin Panel</span>
            </a>
        @endif
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
    <!-- Top Navbar with Notification -->
    <div class="top-navbar">
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
    <div class="welcome-banner">
        <h4>Welcome back, {{ auth()->user()->name }}! 👋</h4>
        <p>Book your device repair service and upload pictures of the issue</p>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-number">{{ $services->count() }}</div>
                <div class="stat-label">Total Bookings</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                <div class="stat-number">{{ $services->where('status', 'Pending')->count() }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <div class="stat-number">{{ $services->where('status', 'In Progress')->count() }}</div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-number">{{ $services->where('status', 'Completed')->count() }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </div>

    <!-- Book a Service Card -->
    <div class="dashboard-card mb-4">
        <div class="card-header-custom">
            <h5><i class="fas fa-wrench"></i> Book a Repair Service</h5>
        </div>
        <div class="card-body-custom">
            @if(session('success'))
                <div class="alert-success mb-3">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('book-service') }}" method="POST" enctype="multipart/form-data" id="bookingForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Device Type</label>
                    <select name="device" class="form-control" required>
                        <option value="">Select Device</option>
                        <option value="Smartphone">📱 Smartphone</option>
                        <option value="Laptop">💻 Laptop</option>
                        <option value="Tablet">📟 Tablet</option>
                        <option value="Desktop">🖥️ Desktop</option>
                        <option value="Printer">🖨️ Printer</option>
                        <option value="Other">🔧 Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Issue Description</label>
                    <textarea name="issue" class="form-control" rows="3" placeholder="Describe the problem in detail..." required></textarea>
                </div>
                
                <!-- Image Upload Section -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Upload Issue Photos (Optional)</label>
                    <div class="image-upload-area" id="imageUploadArea">
                        <i class="fas fa-cloud-upload-alt fa-3x mb-2" style="color: #667eea;"></i>
                        <p class="mb-1">Click or drag & drop images here</p>
                        <small class="text-muted">Supported formats: JPG, PNG, GIF (Max 5MB each)</small>
                        <input type="file" name="issue_images[]" id="issueImages" multiple accept="image/*" style="display: none;">
                    </div>
                    <div class="image-preview-container" id="imagePreviewContainer"></div>
                </div>
                
                <button type="submit" class="btn-primary-custom">
                    <i class="fas fa-calendar-check me-2"></i>Book Service Now
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="dashboard-card">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-list"></i> My Recent Bookings</h5>
            <a href="{{ route('my-services') }}" class="btn-outline-custom" style="padding: 5px 15px; font-size: 13px;">
                View All <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body-custom">
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Device</th>
                            <th>Issue</th>
                            <th>Images</th>
                            <th>Status</th>
                            <th>Booked At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services->take(5) as $service)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $service->device }}</td>
                            <td>{{ Str::limit($service->issue, 40) }}</td>
                            <td>
                                @if($service->images && count(json_decode($service->images, true) ?? []) > 0)
                                    <i class="fas fa-image text-primary"></i> {{ count(json_decode($service->images, true)) }}
                                @else
                                    <span class="text-muted">No images</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = match($service->status) {
                                        'Pending' => 'badge-pending',
                                        'In Progress' => 'badge-progress',
                                        'Completed' => 'badge-completed',
                                        'Cancelled' => 'badge-cancelled',
                                        default => 'badge-pending'
                                    };
                                @endphp
                                <span class="badge-custom {{ $statusClass }}">{{ $service->status }}</span>
                            </td>
                            <td class="text-muted">{{ $service->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                <p class="mb-0">No bookings yet. Book your first service above!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let selectedFiles = [];

// ==================== NOTIFICATION FUNCTIONS ====================

function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('show');
    if (dropdown.classList.contains('show')) {
        loadUserNotifications();
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

// Load notifications for normal user
function loadUserNotifications() {
    fetch('/user/notifications')
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
        <div class="notification-item ${!notification.is_read ? 'unread' : ''}" data-id="${notification.id}" onclick="markUserNotificationRead(${notification.id})">
            <div class="notification-icon-small">
                <i class="fas ${notification.type === 'status_update' ? 'fa-sync-alt' : (notification.type === 'new_message' ? 'fa-comment-dots' : 'fa-info-circle')}"></i>
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

// Mark single notification as read for user
function markUserNotificationRead(id) {
    fetch(`/user/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadUserNotifications();
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

// Mark all notifications as read for user
function markAllAsRead() {
    fetch('/user/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadUserNotifications();
        }
    })
    .catch(error => console.error('Error marking all as read:', error));
}

// Get unread count for user
function updateUserUnreadCount() {
    fetch('/user/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            updateNotificationCount(data.count);
        })
        .catch(error => console.error('Error fetching unread count:', error));
}

// ==================== IMAGE UPLOAD FUNCTIONS ====================

const uploadArea = document.getElementById('imageUploadArea');
const imageInput = document.getElementById('issueImages');
const previewContainer = document.getElementById('imagePreviewContainer');

if (uploadArea) {
    uploadArea.addEventListener('click', () => {
        imageInput.click();
    });

    imageInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });
}

function handleFiles(files) {
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    const maxSize = 5 * 1024 * 1024;
    
    for (let file of files) {
        if (!validTypes.includes(file.type)) {
            alert(`Invalid file type: ${file.name}. Please upload JPG, PNG, or GIF images only.`);
            continue;
        }
        
        if (file.size > maxSize) {
            alert(`File too large: ${file.name}. Maximum size is 5MB.`);
            continue;
        }
        
        if (selectedFiles.length >= 5) {
            alert('You can upload maximum 5 images.');
            break;
        }
        
        selectedFiles.push(file);
    }
    
    updatePreview();
    updateFormData();
}

function updatePreview() {
    if (!previewContainer) return;
    previewContainer.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'image-preview';
            previewDiv.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <div class="remove-image" onclick="removeImage(${index})">
                    <i class="fas fa-times"></i>
                </div>
            `;
            previewContainer.appendChild(previewDiv);
        };
        reader.readAsDataURL(file);
    });
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    updatePreview();
    updateFormData();
}

function updateFormData() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });
    imageInput.files = dataTransfer.files;
}

// ==================== HELPER FUNCTIONS ====================

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
}

// ==================== INITIALIZATION ====================

document.addEventListener('DOMContentLoaded', function() {
    updateUserUnreadCount();
    // Refresh unread count every 30 seconds
    setInterval(updateUserUnreadCount, 30000);
});

// Handle form submission
const bookingForm = document.getElementById('bookingForm');
if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Booking...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
}
</script>
</body>
</html>
