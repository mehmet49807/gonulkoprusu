<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'image_url', 'caption', 'likes_count', 'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function toFeedArray(?int $viewerId = null): array
    {
        $isLiked = $viewerId
            ? $this->likes()->where('user_id', $viewerId)->exists()
            : false;

        return [
            'id' => $this->id,
            'username' => $this->user->username,
            'country' => $this->user->country ?? 'Türkiye',
            'city' => $this->user->city,
            'district' => $this->user->district,
            'is_online' => $this->user->isOnline(),
            'online_status_label' => $this->user->onlineStatusLabel(),
            'last_active_at' => $this->user->last_active_at?->toIso8601String(),
            'image_url' => $this->image_url,
            'likes_count' => $this->likes_count,
            'is_liked' => $isLiked,
            'comments_enabled' => false,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
