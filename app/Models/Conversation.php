<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
        'last_message_preview',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public static function getOrCreate(int $userId1, int $userId2): self
    {
        if ($userId1 === $userId2) {
            abort(422, 'Cannot create a conversation with yourself.');
        }

        [$first, $second] = $userId1 < $userId2 ? [$userId1, $userId2] : [$userId2, $userId1];

        return static::firstOrCreate([
            'user_one_id' => $first,
            'user_two_id' => $second,
        ]);
    }

    public function getOtherUser(int $userId): User
    {
        if ($this->user_one_id === $userId) {
            return $this->userTwo;
        }

        return $this->userOne;
    }

    public function getUnreadCount(int $userId): int
    {
        if ($userId === $this->user_one_id) {
            return $this->unread_count_user_one;
        }

        if ($userId === $this->user_two_id) {
            return $this->unread_count_user_two;
        }

        return 0;
    }
}
