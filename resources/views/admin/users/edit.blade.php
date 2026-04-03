<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - techBook Admin</title>
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
            min-height: 100vh;
            background: #f8f9fa;
            padding: 40px;
        }

        /* Card Styles */
        .edit-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
        }

        .card-header-custom h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .card-header-custom h3 i {
            margin-right: 10px;
        }

        .card-body-custom {
            padding: 30px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        .form-group label i {
            margin-right: 8px;
            color: #667eea;
            width: 20px;
        }

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

        select.form-control {
            cursor: pointer;
            background-color: white;
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
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary-custom {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
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

        /* Responsive */
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
                padding: 20px;
            }
            .edit-card {
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <h3><i class="fas fa-cogs me-2"></i>techBook Admin</h3>
        <p>Admin Dashboard</p>
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
        <a href="{{ route('admin.dashboard') }}">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.users') }}" class="active">
            <i class="fas fa-users"></i>
            <span>Users</span>
        </a>
        <a href="{{ route('admin.services') }}">
            <i class="fas fa-tools"></i>
            <span>Services</span>
        </a>
        <a href="{{ route('admin.settings') }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        <a href="{{ route('admin.reports') }}">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
    </div>
    
    <div class="sidebar-footer">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light w-100 mb-2">
            <i class="fas fa-arrow-left me-2"></i> Back to Site
        </a>
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
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">
                <i class="fas fa-user-edit me-2" style="color: #667eea;"></i>Edit User
            </h2>
            <p class="text-muted mb-0">Update user information and permissions</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary-custom">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form Card -->
    <div class="edit-card">
        <div class="card-header-custom">
            <h3><i class="fas fa-user"></i> Edit User Information</h3>
        </div>
        <div class="card-body-custom">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    <small class="text-muted">Minimum 6 characters if you want to change password</small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user-tag"></i> User Role</label>
                    <select name="role" class="form-control" required>
                        <option value="user" {{ (old('role', $user->role) == 'user') ? 'selected' : '' }}>👤 Regular User</option>
                        <option value="admin" {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}>👑 Administrator</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-toggle-on"></i> Account Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>✅ Active</option>
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>❌ Inactive</option>
                    </select>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary-custom">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn-primary-custom ms-2">
                        <i class="fas fa-save me-2"></i>Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle for mobile
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }
</script>
</body>
</html>