<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'is_edited',
        'deleted_for_sender',
        'deleted_for_recipient',
        'status',
        'read_at',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'deleted_for_sender' => 'boolean',
        'deleted_for_recipient' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    public function getPreviewText(int $userId): string
    {
        if ($this->status === 'unsent') {
            return 'Message deleted';
        }

        if ($this->sender_id === $userId && $this->deleted_for_sender) {
            return 'Message deleted';
        }

        if ($this->sender_id !== $userId && $this->deleted_for_recipient) {
            return 'Message deleted';
        }

        return Str::limit($this->body, 50);
    }
}
