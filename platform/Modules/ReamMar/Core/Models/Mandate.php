<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mandate extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'household_composition',
        'reference_tax_income',
        'household_category',
        'cerfa_type',
    ];

    protected $casts = [
        'reference_tax_income' => 'decimal:2',
        'household_composition' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
