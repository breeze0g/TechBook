<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device',
        'issue',
        'status',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Relationship with messages
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    // Get unread messages from admin
    public function unreadMessages()
    {
        return $this->hasMany(Message::class)->where('is_read', false)->where('sender_type', 'admin');
    }

    // Accessor to get images as array
    public function getImagesArrayAttribute()
    {
        if ($this->images) {
            return json_decode($this->images, true);
        }
        return [];
    }

    // Accessor to get first image URL
    public function getFirstImageUrlAttribute()
    {
        $images = $this->images_array;
        if (!empty($images)) {
            return asset('storage/' . $images[0]);
        }
        return null;
    }
}