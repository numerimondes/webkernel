<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financement extends Model
{
    use HasFactory;

    protected $table = 'ream_mar_financings';

    protected $fillable = [
        'amount',
        'aids',
        'loan',
        'notes',
        'ecoptz_amount',
        'ecoptz_rate',
        'ecoptz_duration_months',
        'bank_loan_amount',
        'bank_loan_rate',
        'bank_loan_duration_months',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'aids' => 'decimal:2',
        'loan' => 'decimal:2',
        'ecoptz_amount' => 'decimal:2',
        'ecoptz_rate' => 'decimal:4',
        'ecoptz_duration_months' => 'integer',
        'bank_loan_amount' => 'decimal:2',
        'bank_loan_rate' => 'decimal:4',
        'bank_loan_duration_months' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accesseurs
    public function getTotalFinancingAttribute()
    {
        return $this->aids + $this->loan;
    }

    public function getEcoptzMonthlyPaymentAttribute()
    {
        if ($this->ecoptz_amount && $this->ecoptz_rate && $this->ecoptz_duration_months) {
            $rate = $this->ecoptz_rate / 100 / 12;
            $months = $this->ecoptz_duration_months;
            return $this->ecoptz_amount * ($rate * pow(1 + $rate, $months)) / (pow(1 + $rate, $months) - 1);
        }
        return 0;
    }

    public function getBankLoanMonthlyPaymentAttribute()
    {
        if ($this->bank_loan_amount && $this->bank_loan_rate && $this->bank_loan_duration_months) {
            $rate = $this->bank_loan_rate / 100 / 12;
            $months = $this->bank_loan_duration_months;
            return $this->bank_loan_amount * ($rate * pow(1 + $rate, $months)) / (pow(1 + $rate, $months) - 1);
        }
        return 0;
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
    public function scopeByAmountRange($query, $min, $max)
    {
        return $query->whereBetween('amount', [$min, $max]);
    }
} 