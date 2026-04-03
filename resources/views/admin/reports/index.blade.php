<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - techBook Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
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
            margin-left: 280px;
            min-height: 100vh;
            background: #f8f9fa;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
        }
        
        .report-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .report-header {
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-export {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .btn-export:hover {
            transform: translateY(-2px);
        }
        
        .chart-container {
            max-height: 300px;
            margin: 20px 0;
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
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .date-filter {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .date-filter input {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 8px 15px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="p-3">
        <h4 class="text-center mb-4">
            <i class="fas fa-cogs me-2"></i>techBook Admin
        </h4>
        <hr class="bg-white">
        <a href="{{ route('admin.dashboard') }}">
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
        <a href="{{ route('admin.reports') }}" class="active">
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
<div class="main-content">
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-chart-bar me-2"></i>Analytics & Reports</h2>
            <button class="btn btn-export" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print Report
            </button>
        </div>

        <!-- Date Filter -->
        <div class="filter-section">
            <div class="date-filter">
                <label><i class="fas fa-calendar me-2"></i>Filter by Date:</label>
                <input type="date" id="startDate" class="form-control" style="width: auto;">
                <span>to</span>
                <input type="date" id="endDate" class="form-control" style="width: auto;">
                <button class="btn btn-primary" onclick="filterByDate()">Apply Filter</button>
                <button class="btn btn-secondary" onclick="resetFilter()">Reset</button>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <i class="fas fa-users fa-2x mb-2" style="color: #667eea;"></i>
                    <div class="stat-number" id="totalUsers">{{ $totalUsers ?? 0 }}</div>
                    <div class="text-muted">Total Users</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <i class="fas fa-tools fa-2x mb-2" style="color: #10b981;"></i>
                    <div class="stat-number" id="totalServices">{{ $totalServices ?? 0 }}</div>
                    <div class="text-muted">Total Services</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <i class="fas fa-clock fa-2x mb-2" style="color: #f59e0b;"></i>
                    <div class="stat-number" id="pendingServices">{{ $pendingServices ?? 0 }}</div>
                    <div class="text-muted">Pending</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <i class="fas fa-check-circle fa-2x mb-2" style="color: #10b981;"></i>
                    <div class="stat-number" id="completedServices">{{ $completedServices ?? 0 }}</div>
                    <div class="text-muted">Completed</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-header">
                        <h5><i class="fas fa-chart-line me-2" style="color: #667eea;"></i>Service Trends</h5>
                    </div>
                    <canvas id="serviceTrendsChart" class="chart-container"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-header">
                        <h5><i class="fas fa-chart-pie me-2" style="color: #667eea;"></i>Service Status Distribution</h5>
                    </div>
                    <canvas id="statusChart" class="chart-container"></canvas>
                </div>
            </div>
        </div>

        <!-- Service Status Breakdown -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-header">
                        <h5><i class="fas fa-chart-bar me-2" style="color: #667eea;"></i>Service Status Breakdown</h5>
                    </div>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-warning">⏳ Pending</span></td>
                                <td>{{ $pendingServices ?? 0 }}</td>
                                <td>{{ $totalServices > 0 ? round(($pendingServices ?? 0) / ($totalServices ?? 1) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">🔄 In Progress</span></td>
                                <td>{{ $inProgressServices ?? 0 }}</td>
                                <td>{{ $totalServices > 0 ? round(($inProgressServices ?? 0) / ($totalServices ?? 1) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">✅ Completed</span></td>
                                <td>{{ $completedServices ?? 0 }}</td>
                                <td>{{ $totalServices > 0 ? round(($completedServices ?? 0) / ($totalServices ?? 1) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">❌ Cancelled</span></td>
                                <td>{{ $cancelledServices ?? 0 }}</td>
                                <td>{{ $totalServices > 0 ? round(($cancelledServices ?? 0) / ($totalServices ?? 1) * 100, 1) : 0 }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-header">
                        <h5><i class="fas fa-history me-2" style="color: #667eea;"></i>Recent Activity</h5>
                        <a href="{{ route('admin.services') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Device</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentServices ?? [] as $service)
                                <tr>
                                    <td>{{ $service->user->name ?? 'N/A' }}</td>
                                    <td>{{ $service->device }}</td>
                                    <td>
                                        <span class="badge bg-{{ $service->status == 'Completed' ? 'success' : ($service->status == 'Pending' ? 'warning' : 'info') }}">
                                            {{ $service->status }}
                                        </span>
                                    </td>
                                    <td>{{ $service->created_at->format('M d, H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No recent activity</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Users -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-header">
                        <h5><i class="fas fa-trophy me-2" style="color: #667eea;"></i>Top Users by Bookings</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topUsers ?? [] as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge bg-primary">{{ $user->services_count }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Most Requested Devices -->
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-header">
                        <h5><i class="fas fa-mobile-alt me-2" style="color: #667eea;"></i>Most Requested Devices</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Device</th>
                                    <th>Requests</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topDevices ?? [] as $index => $device)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $device->device }}</td>
                                    <td><span class="badge bg-info">{{ $device->count }}</span></td>
                                    <td>{{ $totalServices > 0 ? round($device->count / ($totalServices ?? 1) * 100, 1) : 0 }}%</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No data available</td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Service Trends Chart
    const ctx1 = document.getElementById('serviceTrendsChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthLabels ?? []) !!},
            datasets: [{
                label: 'Services Booked',
                data: {!! json_encode($monthlyData ?? []) !!},
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Status Distribution Chart
    const ctx2 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $pendingServices ?? 0 }}, 
                    {{ $inProgressServices ?? 0 }}, 
                    {{ $completedServices ?? 0 }}, 
                    {{ $cancelledServices ?? 0 }}
                ],
                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    function filterByDate() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (startDate && endDate) {
            window.location.href = `/admin/reports?start=${startDate}&end=${endDate}`;
        } else {
            alert('Please select both start and end dates');
        }
    }

    function resetFilter() {
        window.location.href = '/admin/reports';
    }
</script>
</body>
</html>