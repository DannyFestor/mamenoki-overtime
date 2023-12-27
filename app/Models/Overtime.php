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
        'uuid',
        'overtime_confirmation_id',
        'date',
        'time_from',
        'time_until',
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

    public static function booting(): void
    {
        parent::booting();

        static::creating(function (Overtime $overtime) {
            $overtime->uuid = \Str::uuid();
        });
    }
}
