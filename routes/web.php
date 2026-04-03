<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

// ==================== PUBLIC ROUTES ====================

// Home/Login page
Route::get('/', function () {
    return view('login');
})->name('login');

// Registration page
Route::get('/register', function () {
    return view('register');
})->name('register');

// Authentication routes
Route::post('/register', [UserController::class, 'store'])->name('register.post');
Route::post('/login', [UserController::class, 'login'])->name('login.post');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// ==================== PROTECTED ROUTES (Require Login) ====================
Route::middleware(['auth'])->group(function () {
    
    // ======== User Dashboard & Services ========
    Route::get('/index', [ServiceController::class, 'index'])->name('dashboard');
    Route::post('/book-service', [ServiceController::class, 'store'])->name('book-service');
    Route::get('/my-services', [ServiceController::class, 'myServices'])->name('my-services');
    
    // ======== Service Details & Actions ========
    Route::get('/service/{id}/details', [ServiceController::class, 'getServiceDetails'])->name('service.details');
    Route::post('/service/{id}/cancel', [ServiceController::class, 'cancelService'])->name('service.cancel');
    
    // ======== Profile Routes ========
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.upload.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::get('/profile/stats', [ProfileController::class, 'getStats'])->name('profile.stats');
    
    // ======== Notification Routes ========
    Route::get('/notifications/unread-count', [ServiceController::class, 'getUnreadCount'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [ServiceController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [ServiceController::class, 'markAllNotificationsRead'])->name('notifications.mark-all');
    
    // ======== Chat Routes ========
    Route::get('/service/{id}/chat', [MessageController::class, 'getMessages'])->name('service.chat');
    Route::post('/service/{id}/send-message', [MessageController::class, 'sendMessage'])->name('service.send-message');
    Route::post('/service/{id}/mark-read', [MessageController::class, 'markMessagesRead'])->name('service.mark-read');
    
    // ======== Admin Routes ========
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        
        // Admin Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::delete('/users/bulk-delete', [AdminController::class, 'bulkDeleteUsers'])->name('users.bulk-delete');
        
        // Service Management
        Route::get('/services', [AdminController::class, 'services'])->name('services');
        Route::patch('/services/{id}/status', [AdminController::class, 'updateServiceStatus'])->name('services.status');
        Route::delete('/services/{id}', [AdminController::class, 'deleteService'])->name('services.delete');
        Route::delete('/services/bulk-delete', [AdminController::class, 'bulkDeleteServices'])->name('services.bulk-delete');
        
        // Admin Service Details
        Route::get('/service/{id}/details', [ServiceController::class, 'getAdminServiceDetails'])->name('service.details');
        
        // Admin Chat Routes
        Route::get('/service/{id}/chat', [MessageController::class, 'getAdminMessages'])->name('service.chat');
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread.count');
        
        // Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        
        // Reports
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        
        // Export
        Route::get('/export/users', [AdminController::class, 'exportUsers'])->name('export.users');
        Route::get('/export/services', [AdminController::class, 'exportServices'])->name('export.services');
    });
});

// ==================== ERROR PAGES ====================
Route::get('/404', function () {
    return view('404');
})->name('404');

Route::get('/blank', function () {
    return view('blank');
})->name('blank');

Route::get('/table', [UserController::class, 'showTable'])->name('table');

// ==================== MAINTENANCE ROUTE ====================
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// ==================== DEBUG & HELPER ROUTES (Remove in production) ====================

// Make Billy Admin
Route::get('/make-billy-admin', function() {
    try {
        $user = App\Models\User::where('email', 'breezemarlon272@gmail.com')->first();
        
        if ($user) {
            $user->role = 'admin';
            $user->save();
            return "✅ Billy (ID: {$user->id}) is now an admin! <a href='/login'>Login</a>";
        } else {
            $user = App\Models\User::create([
                'name' => 'Billy',
                'email' => 'breezemarlon272@gmail.com',
                'password' => Hash::make('16012218'),
                'role' => 'admin',
                'is_active' => true,
            ]);
            return "✅ Billy created as admin! <a href='/login'>Login with email: {$user->email} and password: 16012218</a>";
        }
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

// Check if user is admin
Route::get('/check-admin', function() {
    if (!Auth::check()) {
        return "❌ Not logged in. <a href='/login'>Login</a>";
    }
    
    $user = Auth::user();
    echo "<h2>User Information:</h2>";
    echo "ID: " . $user->id . "<br>";
    echo "Name: " . $user->name . "<br>";
    echo "Email: " . $user->email . "<br>";
    echo "Role: " . ($user->role ?? 'not set') . "<br>";
    echo "Is Admin: " . (($user->role ?? '') === 'admin' ? '✅ YES' : '❌ NO') . "<br>";
    
    if (($user->role ?? '') !== 'admin') {
        echo "<br><a href='/make-me-admin'>Make Me Admin</a>";
    }
    
    echo "<br><br><a href='/admin/dashboard'>Try Admin Dashboard</a>";
});

// Make current user admin
Route::get('/make-me-admin', function() {
    if (!Auth::check()) {
        return "Please login first.";
    }
    
    $user = Auth::user();
    $user->role = 'admin';
    $user->save();
    
    return "✅ You are now an admin! <a href='/admin/dashboard'>Go to Admin Dashboard</a>";
});

// Debug Services View
Route::get('/debug-services-view', function() {
    $viewPath = resource_path('views/admin/services/index.blade.php');
    
    echo "<h1>🔍 Debug Services View</h1>";
    
    echo "<h3>1. File Check:</h3>";
    echo "View path: " . $viewPath . "<br>";
    echo "File exists: " . (file_exists($viewPath) ? '✅ YES' : '❌ NO') . "<br>";
    
    if (file_exists($viewPath)) {
        echo "File size: " . filesize($viewPath) . " bytes<br>";
        echo "Last modified: " . date('Y-m-d H:i:s', filemtime($viewPath)) . "<br>";
        echo "File is readable: " . (is_readable($viewPath) ? '✅ YES' : '❌ NO') . "<br>";
    }
    
    echo "<h3>2. Laravel View Check:</h3>";
    $exists = view()->exists('admin.services.index');
    echo "admin.services.index exists: " . ($exists ? '✅ YES' : '❌ NO') . "<br>";
    
    echo "<h3>3. Directory Contents:</h3>";
    $adminPath = resource_path('views/admin/services');
    if (is_dir($adminPath)) {
        $files = scandir($adminPath);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "📄 " . $file . "<br>";
            }
        }
    }
    
    echo "<h3>4. Test Render:</h3>";
    if ($exists) {
        try {
            $services = App\Models\Service::with('user')->get();
            $html = view('admin.services.index', ['services' => $services])->render();
            echo "✅ View rendered successfully!<br>";
            echo "<a href='/test-services-render' target='_blank'>Click to see rendered view</a>";
        } catch (\Exception $e) {
            echo "❌ Error rendering: " . $e->getMessage() . "<br>";
        }
    }
    
    return "";
});

// Test Services Render
Route::get('/test-services-render', function() {
    try {
        $services = App\Models\Service::with('user')->get();
        return view('admin.services.index', compact('services'));
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Debug Users View
Route::get('/debug-users-view', function() {
    $viewPath = resource_path('views/admin/users/index.blade.php');
    
    echo "<h1>🔍 Debug Users View</h1>";
    
    echo "<h3>1. File Check:</h3>";
    echo "View path: " . $viewPath . "<br>";
    echo "File exists: " . (file_exists($viewPath) ? '✅ YES' : '❌ NO') . "<br>";
    
    echo "<h3>2. Laravel View Check:</h3>";
    $exists = view()->exists('admin.users.index');
    echo "admin.users.index exists: " . ($exists ? '✅ YES' : '❌ NO') . "<br>";
    
    return "";
});

// Debug Dashboard View
Route::get('/debug-dashboard-view', function() {
    $viewPath = resource_path('views/admin/dashboard.blade.php');
    
    echo "<h1>🔍 Debug Dashboard View</h1>";
    
    echo "<h3>1. File Check:</h3>";
    echo "View path: " . $viewPath . "<br>";
    echo "File exists: " . (file_exists($viewPath) ? '✅ YES' : '❌ NO') . "<br>";
    
    echo "<h3>2. Laravel View Check:</h3>";
    $exists = view()->exists('admin.dashboard');
    echo "admin.dashboard exists: " . ($exists ? '✅ YES' : '❌ NO') . "<br>";
    
    return "";
});

// Fix Views - Create all necessary admin views
Route::get('/fix-views', function() {
    $basePath = resource_path('views/admin');
    $servicesPath = $basePath . '/services/index.blade.php';
    $usersPath = $basePath . '/users/index.blade.php';
    $dashboardPath = $basePath . '/dashboard.blade.php';
    
    $messages = [];
    
    // Create services view if it doesn't exist
    if (!file_exists($servicesPath)) {
        if (!is_dir(dirname($servicesPath))) {
            mkdir(dirname($servicesPath), 0755, true);
        }
        file_put_contents($servicesPath, '<!DOCTYPE html>
<html>
<head>
    <title>Services Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-dark bg-primary">
            <div class="container-fluid">
                <span class="navbar-brand">Services Management</span>
                <form action="{{ route("logout") }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light">Logout</button>
                </form>
            </div>
        </nav>
        
        <div class="row mt-4">
            <div class="col-12">
                @if(session("success"))
                    <div class="alert alert-success">{{ session("success") }}</div>
                @endif
                
                <div class="card">
                    <div class="card-header">
                        <h3>Service Bookings</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                32
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Device</th>
                                    <th>Issue</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                <tr>
                                    <td>{{ $service->id }}</td>
                                    <td>{{ $service->user->name ?? "N/A" }}</td>
                                    <td>{{ $service->device }}</td>
                                    <td>{{ $service->issue }}</td>
                                    <td>
                                        <span class="badge bg-{{ $service->status == "Completed" ? "success" : ($service->status == "Pending" ? "warning" : "info") }}">
                                            {{ $service->status }}
                                        </span>
                                    </td>
                                    <td>{{ $service->created_at->format("Y-m-d H:i") }}</td>
                                    <td>
                                        <form action="{{ route("admin.services.delete", $service->id) }}" method="POST">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this service?\')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No services found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>');
        $messages[] = "✅ Created services view";
    } else {
        $messages[] = "✅ Services view already exists";
    }
    
    // Create users view if it doesn't exist
    if (!file_exists($usersPath)) {
        if (!is_dir(dirname($usersPath))) {
            mkdir(dirname($usersPath), 0755, true);
        }
        file_put_contents($usersPath, '<!DOCTYPE html>
<html>
<head>
    <title>Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-dark bg-primary">
            <div class="container-fluid">
                <span class="navbar-brand">Users Management</span>
                <form action="{{ route("logout") }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light">Logout</button>
                </form>
            </div>
        </nav>
        
        <div class="row mt-4">
            <div class="col-12">
                @if(session("success"))
                    <div class="alert alert-success">{{ session("success") }}</div>
                @endif
                
                <div class="card">
                    <div class="card-header">
                        <h3>Users List</h3>
                        <a href="{{ route("admin.users.create") }}" class="btn btn-primary">Add User</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                32
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role ?? "user" }}</td>
                                    <td>{{ $user->is_active ? "Active" : "Inactive" }}</td>
                                    <td>
                                        <a href="{{ route("admin.users.edit", $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route("admin.users.delete", $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this user?\')">Delete</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>');
        $messages[] = "✅ Created users view";
    } else {
        $messages[] = "✅ Users view already exists";
    }
    
    // Create dashboard view if it doesn't exist
    if (!file_exists($dashboardPath)) {
        file_put_contents($dashboardPath, '<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-dark bg-primary">
            <div class="container-fluid">
                <span class="navbar-brand">Admin Dashboard</span>
                <form action="{{ route("logout") }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light">Logout</button>
                </form>
            </div>
        </nav>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Users</h5>
                        <h2>{{ $totalUsers }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Total Services</h5>
                        <h2>{{ $totalServices }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5>Pending</h5>
                        <h2>{{ $pendingServices }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Completed</h5>
                        <h2>{{ $completedServices }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>');
        $messages[] = "✅ Created dashboard view";
    } else {
        $messages[] = "✅ Dashboard view already exists";
    }
    
    // Clear view cache
    Artisan::call('view:clear');
    $messages[] = "✅ View cache cleared";
    
    echo "<h1>View Fix Results</h1>";
    foreach ($messages as $msg) {
        echo $msg . "<br>";
    }
    
    echo "<br><br>";
    echo "<a href='/admin/dashboard'>Go to Admin Dashboard</a><br>";
    echo "<a href='/admin/users'>Go to Users</a><br>";
    echo "<a href='/admin/services'>Go to Services</a><br>";
    echo "<a href='/debug-services-view'>Debug Services View</a>";
    
    return "";
});

// Test route to check if routes are working
Route::get('/test', function() {
    return "✅ Routes are working! Laravel version: " . app()->version();
});

// Simple services list (bypass view)
Route::get('/simple-services', function() {
    $services = App\Models\Service::with('user')->get();
    
    $html = "<h1>Services List (Simple View)</h1>";
    $html .= "<table border='1' cellpadding='10'>";
    $html .= "<tr><th>ID</th><th>User</th><th>Device</th><th>Issue</th><th>Status</th><th>Date</th></tr>";
    
    foreach ($services as $service) {
        $html .= "<tr>";
        $html .= "<td>{$service->id}</td>";
        $html .= "<td>" . ($service->user->name ?? 'N/A') . "</td>";
        $html .= "<td>{$service->device}</td>";
        $html .= "<td>{$service->issue}</td>";
        $html .= "<td>{$service->status}</td>";
        $html .= "<td>{$service->created_at}</td>";
        $html .= "</tr>";
    }
    
    $html .= "</table>";
    
    return $html;
});

// Test login debug route (remove after fixing)
Route::get('/test-login-debug', [UserController::class, 'testLogin']);

// Export
Route::get('/export/users', [AdminController::class, 'exportUsers'])->name('export.users');
Route::get('/export/services', [AdminController::class, 'exportServices'])->name('export.services');

// Chat Routes - Place these inside the auth middleware group
Route::middleware(['auth'])->group(function () {
    // User chat routes
    Route::get('/service/{id}/chat', [App\Http\Controllers\MessageController::class, 'getMessages'])->name('service.chat');
    Route::post('/service/{id}/send-message', [App\Http\Controllers\MessageController::class, 'sendMessage'])->name('service.send-message');
    Route::post('/service/{id}/mark-read', [App\Http\Controllers\MessageController::class, 'markMessagesRead'])->name('service.mark-read');
    
    // Admin chat routes (inside admin prefix)
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::get('/service/{id}/chat', [App\Http\Controllers\MessageController::class, 'getAdminMessages'])->name('service.chat');
        Route::get('/unread-count', [App\Http\Controllers\MessageController::class, 'getUnreadCount'])->name('unread.count');
    });
});

// Add these inside the admin middleware group (where other admin routes are)
Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
    // ... your existing admin routes ...
    
    // Add these notification routes for admin
    Route::get('/notifications', [AdminController::class, 'getNotifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [AdminController::class, 'markAllNotificationsRead'])->name('notifications.mark-all');
    Route::get('/notifications/unread-count', [AdminController::class, 'getUnreadCount'])->name('notifications.unread');
});

// User Notification Routes (for normal users)
Route::middleware(['auth'])->group(function () {
    Route::get('/user/notifications', [UserController::class, 'getUserNotifications'])->name('user.notifications');
    Route::post('/user/notifications/{id}/read', [UserController::class, 'markUserNotificationRead'])->name('user.notifications.read');
    Route::post('/user/notifications/mark-all-read', [UserController::class, 'markAllUserNotificationsRead'])->name('user.notifications.mark-all');
    Route::get('/user/notifications/unread-count', [UserController::class, 'getUserUnreadCount'])->name('user.notifications.unread');
});