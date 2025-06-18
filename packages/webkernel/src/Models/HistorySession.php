<?php

namespace Webkernel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorySession extends Model
{
    protected $table = 'history_sessions';

    protected $fillable = [
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'last_activity',
        'archived_at',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
