<?php

namespace Webkernel\Core\Models\RBAC;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPanels extends Model
{
    use HasFactory;

    protected $table = 'rbac_user_panels';
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