<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'fieldname',
        'description',
        'picture',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
