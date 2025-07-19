<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    protected $table = 'ream_mar_audits';

    protected $fillable = [
        'date',
        'type',
        'report_path',
        'notes',
        'fees',
        'required',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'fees' => 'decimal:2',
        'required' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accesseurs
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'energy' => 'Énergétique',
            'thermal' => 'Thermique',
            'complete' => 'Complet',
            'diagnostic' => 'Diagnostic',
            default => $this->type
        };
    }

    // Relations
    public function missions()
    {
        return $this->hasMany(Mission::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Scopes
    public function scopeRequired($query)
    {
        return $query->where('required', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
} 