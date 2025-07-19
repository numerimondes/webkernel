<?php

namespace Numerimondes\Modules\ReamMar\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
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
    ];
} 