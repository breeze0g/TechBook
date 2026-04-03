<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - techBook Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        
        .settings-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .settings-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .settings-card-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }
        
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .nav-tabs {
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #666;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .nav-tabs .nav-link:hover {
            color: #667eea;
            border: none;
        }
        
        .nav-tabs .nav-link.active {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            background: transparent;
        }
        
        .color-preview {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            margin-top: 10px;
            border: 2px solid #ddd;
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
        
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #667eea;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
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
        <a href="{{ route('admin.settings') }}" class="active">
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
<div class="main-content">
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-cog me-2"></i>System Settings</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Settings Tabs -->
        <ul class="nav nav-tabs" id="settingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                    <i class="fas fa-globe me-2"></i>General
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab">
                    <i class="fas fa-palette me-2"></i>Appearance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="business-tab" data-bs-toggle="tab" data-bs-target="#business" type="button" role="tab">
                    <i class="fas fa-building me-2"></i>Business Info
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="notification-tab" data-bs-toggle="tab" data-bs-target="#notification" type="button" role="tab">
                    <i class="fas fa-bell me-2"></i>Notifications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                    <i class="fas fa-envelope me-2"></i>Email Settings
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                    <i class="fas fa-server me-2"></i>System
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="settingsTabContent">
            
            <!-- GENERAL SETTINGS -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-globe me-2"></i>General Settings
                    </div>
                    <div class="settings-card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="group" value="general">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Site Name</label>
                                        <input type="text" name="site_name" class="form-control" value="{{ App\Models\Setting::get('site_name', 'techBook') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Site Tagline</label>
                                        <input type="text" name="site_tagline" class="form-control" value="{{ App\Models\Setting::get('site_tagline', 'Your Trusted Repair Service') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Site Description</label>
                                <textarea name="site_description" class="form-control" rows="3">{{ App\Models\Setting::get('site_description', 'Professional device repair services') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Site Logo</label>
                                        <input type="file" name="site_logo" class="form-control" accept="image/*">
                                        @if(App\Models\Setting::get('site_logo'))
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . App\Models\Setting::get('site_logo')) }}" height="50" alt="Logo">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Favicon</label>
                                        <input type="file" name="favicon" class="form-control" accept="image/*">
                                        @if(App\Models\Setting::get('favicon'))
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . App\Models\Setting::get('favicon')) }}" height="32" alt="Favicon">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Time Zone</label>
                                <select name="timezone" class="form-select">
                                    <option value="Africa/Nairobi" {{ App\Models\Setting::get('timezone') == 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi</option>
                                    <option value="America/New_York" {{ App\Models\Setting::get('timezone') == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                    <option value="Asia/Dubai" {{ App\Models\Setting::get('timezone') == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai</option>
                                    <option value="Europe/London" {{ App\Models\Setting::get('timezone') == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Date Format</label>
                                <select name="date_format" class="form-select">
                                    <option value="M d, Y" {{ App\Models\Setting::get('date_format') == 'M d, Y' ? 'selected' : '' }}>Jan 15, 2024</option>
                                    <option value="d/m/Y" {{ App\Models\Setting::get('date_format') == 'd/m/Y' ? 'selected' : '' }}>15/01/2024</option>
                                    <option value="Y-m-d" {{ App\Models\Setting::get('date_format') == 'Y-m-d' ? 'selected' : '' }}>2024-01-15</option>
                                </select>
                            </div>

                            <button type="submit" class="btn-save">Save General Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- APPEARANCE SETTINGS -->
            <div class="tab-pane fade" id="appearance" role="tabpanel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-palette me-2"></i>Appearance Settings
                    </div>
                    <div class="settings-card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group" value="appearance">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Primary Color</label>
                                        <input type="color" name="primary_color" class="form-control" value="{{ App\Models\Setting::get('primary_color', '#667eea') }}">
                                        <div class="color-preview" style="background: {{ App\Models\Setting::get('primary_color', '#667eea') }}"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Secondary Color</label>
                                        <input type="color" name="secondary_color" class="form-control" value="{{ App\Models\Setting::get('secondary_color', '#764ba2') }}">
                                        <div class="color-preview" style="background: {{ App\Models\Setting::get('secondary_color', '#764ba2') }}"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Sidebar Color</label>
                                        <input type="color" name="sidebar_color" class="form-control" value="{{ App\Models\Setting::get('sidebar_color', '#2c3e50') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Header Color</label>
                                        <input type="color" name="header_color" class="form-control" value="{{ App\Models\Setting::get('header_color', '#ffffff') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Font Family</label>
                                <select name="font_family" class="form-select">
                                    <option value="Poppins" {{ App\Models\Setting::get('font_family') == 'Poppins' ? 'selected' : '' }}>Poppins</option>
                                    <option value="Roboto" {{ App\Models\Setting::get('font_family') == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                    <option value="Open Sans" {{ App\Models\Setting::get('font_family') == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                    <option value="Montserrat" {{ App\Models\Setting::get('font_family') == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Layout Style</label>
                                <select name="layout_style" class="form-select">
                                    <option value="fluid" {{ App\Models\Setting::get('layout_style') == 'fluid' ? 'selected' : '' }}>Fluid (Full Width)</option>
                                    <option value="boxed" {{ App\Models\Setting::get('layout_style') == 'boxed' ? 'selected' : '' }}>Boxed (Fixed Width)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn-save">Save Appearance Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- BUSINESS INFO SETTINGS -->
            <div class="tab-pane fade" id="business" role="tabpanel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-building me-2"></i>Business Information
                    </div>
                    <div class="settings-card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group" value="business">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" name="company_name" class="form-control" value="{{ App\Models\Setting::get('company_name', 'techBook Solutions') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" value="{{ App\Models\Setting::get('phone', '+1 234 567 8900') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="contact_email" class="form-control" value="{{ App\Models\Setting::get('contact_email', 'support@techbook.com') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">WhatsApp Number</label>
                                        <input type="text" name="whatsapp" class="form-control" value="{{ App\Models\Setting::get('whatsapp', '+1 234 567 8900') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ App\Models\Setting::get('address', '123 Business Street, City, Country') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Working Hours</label>
                                        <input type="text" name="working_hours" class="form-control" value="{{ App\Models\Setting::get('working_hours', 'Mon-Fri: 9AM-6PM') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Social Media - Facebook</label>
                                        <input type="url" name="facebook" class="form-control" value="{{ App\Models\Setting::get('facebook', 'https://facebook.com/techbook') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Twitter</label>
                                        <input type="url" name="twitter" class="form-control" value="{{ App\Models\Setting::get('twitter', 'https://twitter.com/techbook') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Instagram</label>
                                        <input type="url" name="instagram" class="form-control" value="{{ App\Models\Setting::get('instagram', 'https://instagram.com/techbook') }}">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-save">Save Business Information</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- NOTIFICATION SETTINGS -->
            <div class="tab-pane fade" id="notification" role="tabpanel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-bell me-2"></i>Notification Settings
                    </div>
                    <div class="settings-card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group" value="notifications">
                            
                            <div class="form-group">
                                <label class="form-label">Email Notifications</label>
                                <label class="switch">
                                    <input type="checkbox" name="email_notifications" {{ App\Models\Setting::get('email_notifications', '1') == '1' ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                                <small class="text-muted d-block mt-1">Receive email notifications for new bookings</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Push Notifications</label>
                                <label class="switch">
                                    <input type="checkbox" name="push_notifications" {{ App\Models\Setting::get('push_notifications', '1') == '1' ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                                <small class="text-muted d-block mt-1">Receive browser push notifications</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">SMS Notifications</label>
                                <label class="switch">
                                    <input type="checkbox" name="sms_notifications" {{ App\Models\Setting::get('sms_notifications', '0') == '1' ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                                <small class="text-muted d-block mt-1">Receive SMS notifications for urgent updates</small>
                            </div>

                            <button type="submit" class="btn-save">Save Notification Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- EMAIL SETTINGS -->
            <div class="tab-pane fade" id="email" role="tabpanel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-envelope me-2"></i>Email Configuration
                    </div>
                    <div class="settings-card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group" value="email">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">SMTP Host</label>
                                        <input type="text" name="smtp_host" class="form-control" value="{{ App\Models\Setting::get('smtp_host', 'smtp.gmail.com') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">SMTP Port</label>
                                        <input type="text" name="smtp_port" class="form-control" value="{{ App\Models\Setting::get('smtp_port', '587') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">SMTP Username</label>
                                        <input type="text" name="smtp_username" class="form-control" value="{{ App\Models\Setting::get('smtp_username', '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">SMTP Password</label>
                                        <input type="password" name="smtp_password" class="form-control" value="{{ App\Models\Setting::get('smtp_password', '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Encryption</label>
                                        <select name="smtp_encryption" class="form-select">
                                            <option value="tls" {{ App\Models\Setting::get('smtp_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ App\Models\Setting::get('smtp_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="none" {{ App\Models\Setting::get('smtp_encryption') == 'none' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">From Email</label>
                                        <input type="email" name="from_email" class="form-control" value="{{ App\Models\Setting::get('from_email', 'noreply@techbook.com') }}">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-save">Save Email Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- SYSTEM SETTINGS -->
            <div class="tab-pane fade" id="system" role="tabpanel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <i class="fas fa-server me-2"></i>System Settings
                    </div>
                    <div class="settings-card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group" value="system">
                            
                            <div class="form-group">
                                <label class="form-label">Maintenance Mode</label>
                                <label class="switch">
                                    <input type="checkbox" name="maintenance_mode" {{ App\Models\Setting::get('maintenance_mode', '0') == '1' ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                                <small class="text-muted d-block mt-1">Enable maintenance mode (only admins can access)</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">User Registration</label>
                                <label class="switch">
                                    <input type="checkbox" name="allow_registration" {{ App\Models\Setting::get('allow_registration', '1') == '1' ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                                <small class="text-muted d-block mt-1">Allow new user registrations</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Items Per Page</label>
                                <select name="pagination_limit" class="form-select">
                                    <option value="10" {{ App\Models\Setting::get('pagination_limit') == '10' ? 'selected' : '' }}>10 items</option>
                                    <option value="25" {{ App\Models\Setting::get('pagination_limit') == '25' ? 'selected' : '' }}>25 items</option>
                                    <option value="50" {{ App\Models\Setting::get('pagination_limit') == '50' ? 'selected' : '' }}>50 items</option>
                                    <option value="100" {{ App\Models\Setting::get('pagination_limit') == '100' ? 'selected' : '' }}>100 items</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Backup Schedule</label>
                                <select name="backup_schedule" class="form-select">
                                    <option value="daily" {{ App\Models\Setting::get('backup_schedule') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ App\Models\Setting::get('backup_schedule') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ App\Models\Setting::get('backup_schedule') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="disabled" {{ App\Models\Setting::get('backup_schedule') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>

                            <button type="submit" class="btn-save">Save System Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>