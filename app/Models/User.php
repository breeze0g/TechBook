<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_pic',
        'role',
        'phone',
        'address',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the services associated with the user.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Get the user's profile picture URL.
     *
     * @return string
     */
    public function getProfilePicUrlAttribute()
    {
        if ($this->profile_pic && file_exists(storage_path('app/public/' . $this->profile_pic))) {
            return asset('storage/' . $this->profile_pic);
        }
        
        // Return avatar with user's initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=667eea&color=fff&size=150&rounded=true';
    }

    /**
     * Get the user's profile picture (alias for profile_pic_url).
     *
     * @return string
     */
    public function getAvatarAttribute()
    {
        return $this->profile_pic_url;
    }

    /**
     * Get the user's full name with role badge.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ($this->isAdmin() ? ' (Admin)' : '');
    }

    /**
     * Get the user's service statistics.
     *
     * @return array
     */
    public function getServiceStatsAttribute()
    {
        return [
            'total' => $this->services()->count(),
            'pending' => $this->services()->where('status', 'Pending')->count(),
            'in_progress' => $this->services()->where('status', 'In Progress')->count(),
            'completed' => $this->services()->where('status', 'Completed')->count(),
            'cancelled' => $this->services()->where('status', 'Cancelled')->count(),
        ];
    }

    /**
     * Get the user's formatted join date.
     *
     * @return string
     */
    public function getJoinedDateAttribute()
    {
        return $this->created_at->format('F d, Y');
    }

    /**
     * Check if the user profile is complete.
     *
     * @return bool
     */
    public function isProfileComplete()
    {
        return !empty($this->phone) && !empty($this->address);
    }

    /**
     * Get the user's initials.
     *
     * @return string
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        return $initials;
    }

    /**
     * Update user's last active timestamp.
     */
    public function updateLastActive()
    {
        $this->last_active_at = now();
        $this->save();
    }

    /**
     * Get the user's notification count.
     *
     * @return int
     */
    public function getUnreadNotificationCount()
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Determine if the user can perform an action.
     *
     * @param string $action
     * @return bool
     */
    public function canPerform($action)
    {
        // Admin can do everything
        if ($this->isAdmin()) {
            return true;
        }
        
        // Define user permissions
        $userPermissions = [
            'view_services' => true,
            'create_service' => true,
            'edit_profile' => true,
            'delete_service' => false,
            'manage_users' => false,
        ];
        
        return $userPermissions[$action] ?? false;
    }
}