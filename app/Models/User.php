<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uthm_id',
        'name',
        'email',
        'phone',
        'role',
        'faculty',
        'profile_picture',
        'is_verified',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isClubAdmin()
    {
        return $this->role === 'club_admin';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function announcements()
{
    return $this->hasMany(Announcement::class, 'author_id');
}

    public function getEmailForPasswordReset()
{
    return $this->email; // This should return the correct email
}

    public function isVerifiedMember()
    {
        return $this->is_verified;
    }
}