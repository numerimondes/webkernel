<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'project_name',
        'contract_date',
        'mission_amount_excl_tax',
        'mission_amount_incl_tax',
        'vat',
        'first_installment',
        'mar_administrative_agent',
        'mar_financial_agent',
        'company_name',
        'mar_approval_number',
        'siret_number',
        'head_office_address',
        'head_office_postal_code',
        'head_office_city',
        'company_phone',
        'company_email',
        'insurer',
        'insurance_policy_number',
        'signature_provider',
        'signature_provider_id',
        'mar_signature_link',
        'client_signature_link',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'mission_amount_excl_tax' => 'decimal:2',
        'mission_amount_incl_tax' => 'decimal:2',
        'vat' => 'decimal:2',
        'first_installment' => 'decimal:2',
        'mar_administrative_agent' => 'boolean',
        'mar_financial_agent' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
