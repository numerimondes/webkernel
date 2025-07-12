<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'action',
        'description',
        'data',
        'created_at',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
