<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeConfirmation extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'uuid',
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

    public static function booting(): void
    {
        parent::booting();

        static::creating(function (OvertimeConfirmation $overtimeConfirmation) {
            $overtimeConfirmation->uuid = \Str::uuid();
        });
    }

    public function getRouteKey()
    {
        return 'uuid';
    }

    public function overtimes(): HasMany
    {
        return $this->hasMany(Overtime::class, 'overtime_confirmation_id');
    }
}
