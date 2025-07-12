<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'civility',
        'first_name',
        'last_name',
        'folder_name',
        'fiscal_address',
        'fiscal_postal_code',
        'fiscal_city',
        'fiscal_country',
        'phones',
        'email',
        'email_verified_at',
        'password',
        'can_login',
        'household_status',
        'usage_type',
    ];

    protected $casts = [
        'phones' => 'array',
        'email_verified_at' => 'datetime',
        'can_login' => 'boolean',
    ];

    // Relations

    public function projectAddresses(): HasMany
    {
        return $this->hasMany(ProjectAddress::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function mandates(): HasMany
    {
        return $this->hasMany(Mandate::class);
    }

    public function externalAgents(): HasMany
    {
        return $this->hasMany(ExternalAgent::class);
    }

    public function clientActions(): HasMany
    {
        return $this->hasMany(ClientAction::class);
    }

    public function clientDocuments(): HasMany
    {
        return $this->hasMany(ClientDocument::class);
    }
}
