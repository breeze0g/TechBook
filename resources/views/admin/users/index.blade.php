<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - techBook</title>
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
            transform: translateX(5px);
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
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border: none;
            border-radius: 15px;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .btn-delete {
            transition: all 0.2s;
        }
        .btn-delete:hover:not(:disabled) {
            transform: scale(1.05);
        }
        
        .fade-out {
            animation: fadeOutRow 0.4s ease forwards;
        }
        
        @keyframes fadeOutRow {
            0% {
                opacity: 1;
                transform: translateX(0);
            }
            100% {
                opacity: 0;
                transform: translateX(-30px);
                display: none;
            }
        }
        
        .custom-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 320px;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            border-radius: 12px;
            border-left: 4px solid;
        }
        
        .custom-toast.success {
            border-left-color: #28a745;
        }
        
        .custom-toast.danger {
            border-left-color: #dc3545;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
        }
        
        .avatar-circle {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding-left: 40px;
            border-radius: 25px;
            border: 1px solid #e0e0e0;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
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
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-dashboard me-2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}" class="active">
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
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1">
                            <i class="fas fa-users me-2 text-primary"></i>Users Management
                        </h2>
                        <p class="text-muted mb-0">Manage and monitor all registered users</p>
                    </div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Add New User
                    </a>
                </div>

                <!-- Session Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Search Bar -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.export.users') }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Export Users
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4 g-4">
                    <div class="col-md-2 col-sm-6">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Total Users</h6>
                                        <h3 class="mb-0" id="totalUsersCount">{{ $users->count() }}</h3>
                                    </div>
                                    <i class="fas fa-users fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="card stat-card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Admins</h6>
                                        <h3 class="mb-0" id="adminsCount">{{ $users->where('role', 'admin')->count() }}</h3>
                                    </div>
                                    <i class="fas fa-user-shield fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Regular Users</h6>
                                        <h3 class="mb-0" id="regularCount">{{ $users->where('role', 'user')->count() }}</h3>
                                    </div>
                                    <i class="fas fa-user fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Active</h6>
                                        <h3 class="mb-0" id="activeCount">{{ $users->where('is_active', true)->count() }}</h3>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="card stat-card bg-secondary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Inactive</h6>
                                        <h3 class="mb-0" id="inactiveCount">{{ $users->where('is_active', false)->count() }}</h3>
                                    </div>
                                    <i class="fas fa-ban fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2 text-primary"></i>All Users
                            </h5>
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>Click delete to remove user
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%">ID</th>
                                        <th style="width: 20%">Name</th>
                                        <th style="width: 25%">Email</th>
                                        <th style="width: 10%">Role</th>
                                        <th style="width: 8%">Services</th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 12%">Joined</th>
                                        <th style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    @forelse($users as $user)
                                    <tr id="user-row-{{ $user->id }}" data-user-id="{{ $user->id }}">
                                        <td class="align-middle">{{ $user->id }}</td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                @if($user->profile_pic)
                                                    <img src="{{ asset('storage/' . $user->profile_pic) }}" 
                                                         class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                                @else
                                                    <div class="avatar-circle me-2">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    @if($user->id == auth()->id())
                                                        <span class="badge bg-info ms-1">You</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">{{ $user->email }}</td>
                                        <td class="align-middle">
                                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'info' }} px-3 py-2">
                                                <i class="fas {{ $user->role == 'admin' ? 'fa-crown' : 'fa-user' }} me-1"></i>
                                                {{ ucfirst($user->role ?? 'user') }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-secondary">{{ $user->services_count ?? 0 }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }} px-3 py-2">
                                                <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-clock' }} me-1"></i>
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="align-middle">{{ $user->created_at->format('M d, Y') }}</td>
                                        <td class="align-middle">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @if($user->id != auth()->id())
                                                    <button type="button" 
                                                            onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" 
                                                            class="btn btn-danger btn-sm btn-delete"
                                                            id="delete-btn-{{ $user->id }}">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">No users found</h5>
                                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-user-plus me-1"></i>Add your first user
                                            </a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm User Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-user-minus fa-4x text-danger mb-3"></i>
                    <p class="mb-2" id="deleteModalMessage">Are you sure you want to delete this user?</p>
                    <p class="text-danger mb-0 small">
                        <i class="fas fa-exclamation-circle me-1"></i>This action cannot be undone!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash-alt me-1"></i>Yes, Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let deleteModal;
        let userIdToDelete = null;
        let userNameToDelete = null;
        
        // Initialize modal when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            
            // Add confirm delete button event listener
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    if (userIdToDelete) {
                        performDelete(userIdToDelete, userNameToDelete);
                    }
                });
            }
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    filterTable(this.value);
                });
            }
        });
        
        // Filter table based on search input
        function filterTable(searchTerm) {
            const rows = document.querySelectorAll('#usersTableBody tr');
            const term = searchTerm.toLowerCase();
            
            rows.forEach(row => {
                if (row.cells.length > 1) {
                    const name = row.cells[1]?.innerText.toLowerCase() || '';
                    const email = row.cells[2]?.innerText.toLowerCase() || '';
                    
                    if (name.includes(term) || email.includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
        
        // Function to show toast notifications
        function showToast(message, type = 'success') {
            const existingToast = document.querySelector('.custom-toast');
            if (existingToast) {
                existingToast.remove();
            }
            
            const toastContainer = document.createElement('div');
            toastContainer.className = `custom-toast alert alert-${type} alert-dismissible fade show ${type}`;
            toastContainer.setAttribute('role', 'alert');
            
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            toastContainer.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas ${icon} fa-lg me-2"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            document.body.appendChild(toastContainer);
            
            setTimeout(() => {
                if (toastContainer && toastContainer.parentElement) {
                    toastContainer.remove();
                }
            }, 4000);
        }
        
        // Update statistics after deletion
        function updateStatistics(userRole, userStatus) {
            const totalUsersEl = document.getElementById('totalUsersCount');
            if (totalUsersEl) {
                let currentTotal = parseInt(totalUsersEl.innerText);
                totalUsersEl.innerText = currentTotal - 1;
            }
            
            if (userRole === 'admin') {
                const adminsCountEl = document.getElementById('adminsCount');
                if (adminsCountEl) {
                    let currentAdmins = parseInt(adminsCountEl.innerText);
                    adminsCountEl.innerText = currentAdmins - 1;
                }
            } else if (userRole === 'user') {
                const regularCountEl = document.getElementById('regularCount');
                if (regularCountEl) {
                    let currentRegular = parseInt(regularCountEl.innerText);
                    regularCountEl.innerText = currentRegular - 1;
                }
            }
            
            if (userStatus === 'Active') {
                const activeCountEl = document.getElementById('activeCount');
                if (activeCountEl) {
                    let currentActive = parseInt(activeCountEl.innerText);
                    activeCountEl.innerText = currentActive - 1;
                }
            } else if (userStatus === 'Inactive') {
                const inactiveCountEl = document.getElementById('inactiveCount');
                if (inactiveCountEl) {
                    let currentInactive = parseInt(inactiveCountEl.innerText);
                    inactiveCountEl.innerText = currentInactive - 1;
                }
            }
        }
        
        // Perform the actual delete operation
        function performDelete(userId, userName) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            const originalBtnHtml = confirmBtn.innerHTML;
            
            // Show loading state
            confirmBtn.innerHTML = '<span class="loading-spinner"></span> Deleting...';
            confirmBtn.disabled = true;
            
            const row = document.getElementById(`user-row-${userId}`);
            let userRole = 'user';
            let userStatus = 'Active';
            
            if (row) {
                const roleBadge = row.querySelector('td:nth-child(4) .badge');
                if (roleBadge) {
                    const roleText = roleBadge.innerText.toLowerCase();
                    userRole = roleText.includes('admin') ? 'admin' : 'user';
                }
                const statusBadge = row.querySelector('td:nth-child(6) .badge');
                if (statusBadge) {
                    userStatus = statusBadge.innerText.trim();
                }
            }
            
            // Create a form to submit DELETE request (more reliable than fetch for Laravel)
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/users/${userId}`;
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = token;
            form.appendChild(csrfInput);
            
            // Add method spoofing for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            
            // Submit the form
            form.submit();
            
            // Close modal and show loading
            deleteModal.hide();
            showToast(`Deleting user "${userName}"...`, 'warning');
            
            // Reset button after form submission (will reload page anyway)
            setTimeout(() => {
                confirmBtn.innerHTML = originalBtnHtml;
                confirmBtn.disabled = false;
            }, 1000);
        }
        
        // Show delete confirmation modal
        function deleteUser(userId, userName) {
            userIdToDelete = userId;
            userNameToDelete = userName;
            
            const modalMessage = document.getElementById('deleteModalMessage');
            modalMessage.innerHTML = `Are you sure you want to delete user <strong class="text-danger">${escapeHtml(userName)}</strong>?`;
            
            deleteModal.show();
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>