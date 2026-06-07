<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sender_id', 'receiver_id', 'message_text', 'is_read', 'read_at', 'created_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Message $message) {
            if ($message->created_at === null) {
                $message->created_at = now();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
