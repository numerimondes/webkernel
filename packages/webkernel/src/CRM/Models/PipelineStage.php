<?php

namespace Webkernel\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipelineStage extends Model
{
    protected $table = 'crm_pipeline_stages';
    protected $fillable = [
        'pipeline_id',
        'name',
        'description',
        'order',
        'win_probability',
        'color',
        'is_client_stage',
        'settings',
    ];

    protected $casts = [
        'win_probability' => 'decimal:2',
        'is_client_stage' => 'boolean',
        'settings' => 'array',
    ];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }
} 