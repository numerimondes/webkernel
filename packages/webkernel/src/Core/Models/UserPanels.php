<?php

namespace Webkernel\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPanels extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'panels'
    ];

    protected $casts = [
        'panels' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 