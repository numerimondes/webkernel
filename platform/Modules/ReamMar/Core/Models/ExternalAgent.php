<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'company_name',
        'siret_number',
        'agent_email',
        'registration_number',
        'mandate_document',
        'administrative_agent',
        'financial_agent',
    ];

    protected $casts = [
        'administrative_agent' => 'boolean',
        'financial_agent' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
