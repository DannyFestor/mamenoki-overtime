<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeConfirmation extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'remarks',
        'transfer_remarks',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];
}
