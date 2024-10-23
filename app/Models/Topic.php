<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'message_received',
        'user_id',
        'forum_id',
    ];

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

}
