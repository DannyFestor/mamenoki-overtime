<?php

namespace App\Models;

use App\Enums\OvertimeReason;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Overtime extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'overtime_confirmation_id',
        'date',
        'from_hours',
        'from_minutes',
        'to_hours',
        'to_minutes',
        'reason',
        'remarks',
        'created_user_id',
        'applicant_user_id',
        'applied_at',
        'approval_user_id',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'applied_at' => 'datetime',
        'approved_at' => 'datetime',
        'reason' => OvertimeReason::class,
    ];
}
