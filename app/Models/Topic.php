<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

}
