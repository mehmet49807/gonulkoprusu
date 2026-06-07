<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const MALE_TRIAL_DAYS = 3;

    protected $fillable = [
        'username', 'first_name', 'last_name', 'email', 'password',
        'phone', 'gender', 'country', 'city', 'district', 'profile_photo_url',
        'role', 'is_banned', 'banned_at', 'banned_reason', 'trial_ends_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'last_active_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'is_banned' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function premiumSubscriptions()
    {
        return $this->hasMany(PremiumSubscription::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOnTrial(): bool
    {
        return $this->gender === 'male'
            && $this->trial_ends_at !== null
            && $this->trial_ends_at->isFuture();
    }

    public function isPremium(): bool
    {
        if ($this->gender === 'female') {
            return false;
        }

        return $this->premiumSubscriptions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function canPostStories(): bool
    {
        if ($this->gender === 'female') {
            return true;
        }

        return $this->isOnTrial() || $this->isPremium();
    }

    public function canSendMessages(): bool
    {
        if ($this->gender === 'female') {
            return true;
        }

        return $this->isOnTrial() || $this->isPremium();
    }

    public function trialDaysRemaining(): int
    {
        if (!$this->isOnTrial()) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInSeconds($this->trial_ends_at) / 86400));
    }

    public static function trialEndsAtForNewMale(): \Illuminate\Support\Carbon
    {
        return now()->addDays(self::MALE_TRIAL_DAYS);
    }

    public function oppositeGender(): string
    {
        return $this->gender === 'male' ? 'female' : 'male';
    }

    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'profile_photo_url' => $this->profile_photo_url,
            'country' => $this->country ?? 'Türkiye',
            'city' => $this->city,
            'district' => $this->district,
        ];
    }

    public function toOwnerArray(): array
    {
        return array_merge($this->toPublicArray(), [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'is_premium' => $this->isPremium(),
            'is_on_trial' => $this->isOnTrial(),
            'trial_ends_at' => $this->trial_ends_at?->toIso8601String(),
            'trial_days_remaining' => $this->trialDaysRemaining(),
            'can_post_stories' => $this->canPostStories(),
            'can_send_messages' => $this->canSendMessages(),
        ]);
    }
}
