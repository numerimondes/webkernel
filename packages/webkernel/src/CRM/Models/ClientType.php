<?php

namespace Webkernel\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    protected $table = 'crm_client_types';
    protected $fillable = [
        'name',
        'description',
        'model_type',
        'module',
        'is_active',
        'capabilities',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capabilities' => 'array',
    ];

    public function getModelClass()
    {
        return $this->model_type;
    }

    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->capabilities ?? []);
    }
} 