<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - techBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
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
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-5px);
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            text-align: center;
            position: relative;
        }

        .profile-avatar {
            position: relative;
            display: inline-block;
        }

        .profile-avatar img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            object-fit: cover;
            background: white;
        }

        .avatar-overlay {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #667eea;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid white;
        }

        .avatar-overlay:hover {
            background: #764ba2;
            transform: scale(1.1);
        }

        .avatar-overlay i {
            color: white;
            font-size: 14px;
        }

        .profile-name {
            color: white;
            margin-top: 15px;
            font-size: 24px;
            font-weight: 600;
        }

        .profile-role {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-top: 5px;
        }

        .profile-body {
            padding: 40px;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            display: inline-block;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .info-item:hover {
            background: #f0f0f0;
            transform: translateX(5px);
        }

        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-value i {
            color: #667eea;
            width: 20px;
        }

        .edit-form {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }

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
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 500;
            margin-left: 10px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

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

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        /* Cropper Modal Styles */
        .modal-crop {
            z-index: 9999;
        }
        
        .modal-crop .modal-dialog {
            max-width: 600px;
        }
        
        .img-container {
            max-height: 400px;
            overflow: hidden;
        }
        
        .img-container img {
            max-width: 100%;
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
                padding: 20px;
            }
            
            .profile-body {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .profile-header {
                padding: 30px;
            }
            
            .profile-avatar img {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <h3><i class="fas fa-cogs me-2"></i>techBook</h3>
            <p>User Dashboard</p>
        </div>
        
        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="active">
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
        <div class="profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=667eea&color=fff&size=150' }}" 
                         alt="Profile Picture" 
                         id="profileImage">
                    <div class="avatar-overlay" onclick="document.getElementById('profile_pic_input').click()">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-role">
                    <i class="fas fa-{{ $user->role === 'admin' ? 'crown' : 'user' }} me-1"></i>
                    {{ $user->role === 'admin' ? 'Administrator' : 'Member' }}
                </div>
            </div>

            <!-- Profile Body -->
            <div class="profile-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <span><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Statistics Section -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">{{ $user->services_count ?? 0 }}</div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $user->services->where('status', 'Pending')->count() ?? 0 }}</div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $user->services->where('status', 'In Progress')->count() ?? 0 }}</div>
                        <div class="stat-label">In Progress</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $user->services->where('status', 'Completed')->count() ?? 0 }}</div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="info-section">
                    <div class="info-title">
                        <i class="fas fa-user-circle me-2"></i>Personal Information
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">
                                <i class="fas fa-user"></i>
                                <span>{{ $user->name }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $user->email }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Account Type</div>
                            <div class="info-value">
                                <i class="fas fa-{{ $user->role === 'admin' ? 'crown' : 'shield-alt' }}"></i>
                                <span>{{ $user->role === 'admin' ? 'Administrator' : 'Regular User' }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Account Status</div>
                            <div class="info-value">
                                <i class="fas fa-circle {{ $user->is_active ? 'text-success' : 'text-danger' }}"></i>
                                <span>{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">
                                <i class="fas fa-phone"></i>
                                <span>{{ $user->phone ?? 'Not provided' }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Address</div>
                            <div class="info-value">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $user->address ?? 'Not provided' }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Member Since</div>
                            <div class="info-value">
                                <i class="fas fa-calendar-alt"></i>
                                <span>{{ $user->created_at->format('F d, Y') }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">
                                <i class="fas fa-clock"></i>
                                <span>{{ $user->updated_at->format('F d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div class="edit-form">
                    <div class="info-title">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </div>
                    
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Full Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-envelope"></i> Email Address</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-phone"></i> Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-lock"></i> New Password (leave blank to keep current)</label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter new password">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label><i class="fas fa-map-marker-alt"></i> Address</label>
                                    <textarea name="address" class="form-control" rows="3">{{ old('address', $user->address) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label><i class="fas fa-image"></i> Profile Picture</label>
                                    <input type="file" name="profile_pic" id="profile_pic_input" class="form-control" accept="image/*" style="display: none;">
                                    <input type="hidden" name="cropped_image" id="cropped_image">
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('profile_pic_input').click()">
                                        <i class="fas fa-upload me-2"></i>Choose New Picture
                                    </button>
                                    <small class="text-muted d-block mt-2">Accepted formats: JPG, PNG, GIF (Max 2MB)</small>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('dashboard') }}" class="btn-cancel">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn-update">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper Modal -->
    <div class="modal fade modal-crop" id="cropModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Crop Profile Picture</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <img id="cropImage" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="cropBtn">Crop & Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        let cropper;
        let cropModal;
        let selectedFile = null;

        // Initialize modal
        document.addEventListener('DOMContentLoaded', function() {
            cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
        });

        // Handle file selection
        document.getElementById('profile_pic_input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select an image file (JPG, PNG, GIF)');
                return;
            }
            
            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                return;
            }
            
            selectedFile = file;
            const reader = new FileReader();
            
            reader.onload = function(event) {
                const image = document.getElementById('cropImage');
                image.src = event.target.result;
                
                // Show modal
                cropModal.show();
                
                // Initialize cropper after image loads
                setTimeout(() => {
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(image, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        minCropBoxWidth: 100,
                        minCropBoxHeight: 100
                    });
                }, 100);
            };
            
            reader.readAsDataURL(file);
        });

        // Handle crop button click
        document.getElementById('cropBtn').addEventListener('click', function() {
            if (!cropper) return;
            
            // Get cropped canvas
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300
            });
            
            // Convert canvas to blob
            canvas.toBlob(function(blob) {
                // Create form data for upload
                const formData = new FormData();
                formData.append('profile_pic', blob, 'profile_avatar.jpg');
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Show loading state
                const cropBtn = document.getElementById('cropBtn');
                const originalText = cropBtn.innerHTML;
                cropBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
                cropBtn.disabled = true;
                
                // Upload cropped image
                fetch('{{ route("profile.upload.avatar") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update profile image preview
                        document.getElementById('profileImage').src = data.image_url + '?t=' + new Date().getTime();
                        showToast('Profile picture updated successfully!', 'success');
                        cropModal.hide();
                        
                        // Clear the file input
                        document.getElementById('profile_pic_input').value = '';
                    } else {
                        showToast(data.message || 'Failed to upload image', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while uploading', 'danger');
                })
                .finally(() => {
                    cropBtn.innerHTML = originalText;
                    cropBtn.disabled = false;
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                });
            }, 'image/jpeg', 0.9);
        });

        // Show toast notification
        function showToast(message, type = 'success') {
            const toastContainer = document.createElement('div');
            toastContainer.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            toastContainer.style.zIndex = '9999';
            toastContainer.style.minWidth = '300px';
            toastContainer.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toastContainer);
            
            setTimeout(() => {
                toastContainer.remove();
            }, 3000);
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => bsAlert.close(), 500);
            });
        }, 5000);

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
    </script>
</body>
</html>