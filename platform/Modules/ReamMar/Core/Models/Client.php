<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'ream_mar_clients';

    protected $fillable = [
        'civility',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'address',
        'postal_code',
        'city',
        'country',
        'notes',
        'is_active',
        'source',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];



    // Accesseurs
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getFullAddressAttribute()
    {
        return trim($this->address . ', ' . $this->postal_code . ' ' . $this->city);
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

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
}

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }


} 