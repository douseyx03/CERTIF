<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use ApiPlatform\Metadata\ApiResource; 

#[ApiResource]
class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'fieldname',
        'description',
        'picture',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forum(): HasOne
    {
        return $this->hasOne(Forum::class);
    }
}
