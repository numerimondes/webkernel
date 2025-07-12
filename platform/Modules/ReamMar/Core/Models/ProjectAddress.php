<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'street',
        'postal_code',
        'city',
        'country',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
