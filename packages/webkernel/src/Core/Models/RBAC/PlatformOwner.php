<?php

namespace Webkernel\Core\Models\RBAC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformOwner extends Model
{
    use HasFactory;

    protected $table = 'rbac_platform_owners';
    protected $fillable = [
        'user_id',
        'panel_id',
        'is_eternal_owner',
        'when',
        'until',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_eternal_owner' => 'boolean',
        'when' => 'datetime',
        'until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
} 