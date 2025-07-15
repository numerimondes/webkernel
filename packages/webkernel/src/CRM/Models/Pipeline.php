<?php

namespace Webkernel\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends Model
{
    protected $table = 'crm_pipelines';

    protected $fillable = [
        'name',
        'description',
        'module',
        'is_active',
        'is_default',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'settings' => 'array',
    ];

    public function stages(): HasMany
    {
        return $this->hasMany(PipelineStage::class)->orderBy('order');
    }

    public function activeStages(): HasMany
    {
        return $this->stages()->where('is_active', true);
    }

    public function getClientStage()
    {
        return $this->stages()->where('is_client_stage', true)->first();
    }
} 