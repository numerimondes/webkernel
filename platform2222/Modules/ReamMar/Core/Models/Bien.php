<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    use HasFactory;

    protected $table = 'ream_mar_properties';

    protected $fillable = [
        'address',
        'postal_code',
        'city',
        'country',
        'type',
        'usage',
        'household_status',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accesseurs
    public function getFullAddressAttribute()
    {
        return trim($this->address . ', ' . $this->postal_code . ' ' . $this->city);
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'house' => 'Maison',
            'apartment' => 'Appartement',
            'villa' => 'Villa',
            'duplex' => 'Duplex',
            'loft' => 'Loft',
            'other' => 'Autre',
            default => $this->type
        };
    }

    public function getUsageLabelAttribute()
    {
        return match($this->usage) {
            'primary_residence' => 'Résidence principale',
            'secondary_residence' => 'Résidence secondaire',
            'rental' => 'Location',
            'investment' => 'Investissement',
            'other' => 'Autre',
            default => $this->usage
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }
} 