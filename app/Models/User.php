<?php

namespace App\Models;

use App\Helpers\MailHelper;	
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\URL;
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
        'is_banned',
        'password',
        'google_id',
        'google_token',
        'google_refresh_token',
        'google_token_expires_at',
        'google_calendar_id',
        'google_calendar_synced',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_banned' => 'boolean',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
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

/**
 * Get the memberships for the user.
 */
public function groupMemberships()
{
    return $this->hasMany(\App\Models\GroupMember::class, 'user_id');
}

    public function isVerifiedMember()
    {
        return $this->is_verified;
    }

    public function isBanned(): bool
    {
        return (bool) $this->is_banned;
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $verificationUrl = $this->verificationUrl();
        
        MailHelper::send(
            $this->email,
            'Verify Your Email Address - UTHM Bulletin Board',
            "<h1>Verify Your Email</h1>
             <p>Click the link below to verify your email address:</p>
             <a href='{$verificationUrl}'>Verify Email</a>
             <p>If you did not create an account, ignore this email.</p>"
        );
    }

 /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($this->email));
        
        MailHelper::send(
            $this->email,
            'Reset Your Password - UTHM Bulletin Board',
            "<h1>Reset Your Password</h1>
             <p>Click the link below to reset your password:</p>
             <a href='{$resetUrl}'>Reset Password</a>
             <p>This link expires in 60 minutes.</p>
             <p>If you did not request a password reset, ignore this email.</p>"
        );
    }

    /**
     * Get the email verification URL.
     */
    protected function verificationUrl()
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ]
        );
    }
}

