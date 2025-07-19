<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkernel\CRM\Models\Pipeline;
use Webkernel\CRM\Models\PipelineStage;

class Mission extends Model
{
    use HasFactory;

    protected $table = 'ream_mar_missions';

    protected $fillable = [
        // CRM Integration
        'pipeline_id', 'pipeline_stage_id', 'deal_status', 'deal_value', 
        'expected_close_date', 'actual_close_date',
        
        // Prospect/Contact Information
        'prospect_type', 'prospect_source', 'contact_date', 'contact_method',
        'prospect_notes', 'contact_notes',
        
        // Opportunity Information
        'opportunity_value', 'opportunity_probability', 'opportunity_stage', 'opportunity_notes',
        
        // Relations
        'client_id', 'bien_id', 'audit_id', 'financement_id', 'travaux_id', 'completion_id',
        
        // Workflow
        'current_step', 'step_status', 'validation_date', 'workflow_notes',
        
        // Métadonnées
        'created_by', 'updated_by',
    ];

    protected $casts = [
        // CRM
        'deal_value' => 'decimal:2',
        'opportunity_value' => 'decimal:2',
        'opportunity_probability' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'contact_date' => 'date',
        
        // Workflow
        'validation_date' => 'datetime',
        
        // Métadonnées
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];



    // Relations CRM
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function pipelineStage()
    {
        return $this->belongsTo(PipelineStage::class);
    }

    // Relations existantes
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function financement()
    {
        return $this->belongsTo(Financement::class);
    }

    public function travaux()
    {
        return $this->belongsTo(Travaux::class);
    }

    public function completion()
    {
        return $this->belongsTo(Completion::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Accesseurs CRM
    public function getDealStatusLabelAttribute()
    {
        return match($this->deal_status) {
            'active' => 'Actif',
            'won' => 'Gagné',
            'lost' => 'Perdu',
            'cancelled' => 'Annulé',
            default => 'Inconnu'
        };
    }

    public function getDealStatusColorAttribute()
    {
        return match($this->deal_status) {
            'active' => 'blue',
            'won' => 'success',
            'lost' => 'danger',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getIsWonAttribute(): bool
    {
        return $this->deal_status === 'won';
    }

    public function getIsLostAttribute(): bool
    {
        return $this->deal_status === 'lost';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->deal_status === 'active';
    }

    // Accesseurs existants
    public function getClientFullNameAttribute()
    {
        return $this->client ? $this->client->full_name : null;
    }

    public function getBienFullAddressAttribute()
    {
        return $this->bien ? $this->bien->full_address : null;
    }

    public function getCurrentStepLabelAttribute()
    {
        return match($this->current_step) {
            'prospect' => 'Prospect',
            'contact' => 'Contact',
            'opportunity' => 'Opportunité',
            'client' => 'Client',
            'bien' => 'Propriété',
            'audit' => 'Audit',
            'financing' => 'Financement',
            'work' => 'Travaux',
            'completion' => 'Finalisation',
            default => 'Inconnu'
        };
    }

    public function getStepStatusLabelAttribute()
    {
        return match($this->step_status) {
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'blocked' => 'Bloqué',
            default => 'Inconnu'
        };
    }

    // Scopes CRM
    public function scopeByDealStatus($query, $status)
    {
        return $query->where('deal_status', $status);
    }

    public function scopeWon($query)
    {
        return $query->where('deal_status', 'won');
    }

    public function scopeLost($query)
    {
        return $query->where('deal_status', 'lost');
    }

    public function scopeActive($query)
    {
        return $query->where('deal_status', 'active');
    }

    public function scopeByPipeline($query, $pipelineId)
    {
        return $query->where('pipeline_id', $pipelineId);
    }

    public function scopeByPipelineStage($query, $stageId)
    {
        return $query->where('pipeline_stage_id', $stageId);
    }

    // Scopes existants
    public function scopeByStep($query, $step)
    {
        return $query->where('current_step', $step);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('step_status', $status);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByBien($query, $bienId)
    {
        return $query->where('bien_id', $bienId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('step_status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('step_status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('step_status', 'in_progress');
    }

    // Méthodes CRM
    public function markAsWon()
    {
        $this->update([
            'deal_status' => 'won',
            'actual_close_date' => now(),
        ]);
    }

    public function markAsLost()
    {
        $this->update([
            'deal_status' => 'lost',
            'actual_close_date' => now(),
        ]);
    }

    public function markAsCancelled()
    {
        $this->update([
            'deal_status' => 'cancelled',
            'actual_close_date' => now(),
        ]);
    }

    public function moveToStage(PipelineStage $stage)
    {
        $this->update([
            'pipeline_stage_id' => $stage->id,
            'current_step' => $stage->name,
        ]);

        // Si c'est l'étape client, marquer comme client
        if ($stage->is_client_stage) {
            // Logique pour marquer comme client
        }
    }

    // Méthodes de workflow existantes
    public function canAccessStep($step): bool
    {
        $stepOrder = [
            'prospect' => 1,
            'contact' => 2,
            'opportunity' => 3,
            'client' => 4,
            'bien' => 5,
            'audit' => 6,
            'financing' => 7,
            'work' => 8,
            'completion' => 9,
        ];

        $currentStepOrder = $stepOrder[$this->current_step] ?? 0;
        $targetStepOrder = $stepOrder[$step] ?? 0;

        // Audit est optionnel
        if ($step === 'audit' && !$this->audit_id) {
            return true;
        }

        return $targetStepOrder <= $currentStepOrder + 1;
    }

    public function isStepCompleted($step): bool
    {
        return $this->current_step === $step && $this->step_status === 'completed';
    }

    public function getNextStep(): ?string
    {
        $stepOrder = [
            'prospect' => 'contact',
            'contact' => 'opportunity',
            'opportunity' => 'client',
            'client' => 'bien',
            'bien' => 'audit',
            'audit' => 'financing',
            'financing' => 'work',
            'work' => 'completion',
            'completion' => null,
        ];

        return $stepOrder[$this->current_step] ?? null;
    }

    public function getPreviousStep(): ?string
    {
        $stepOrder = [
            'prospect' => null,
            'contact' => 'prospect',
            'opportunity' => 'contact',
            'client' => 'opportunity',
            'bien' => 'client',
            'audit' => 'bien',
            'financing' => 'audit',
            'work' => 'financing',
            'completion' => 'work',
        ];

        return $stepOrder[$this->current_step] ?? null;
    }


}
