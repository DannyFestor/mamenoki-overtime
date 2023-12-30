<?php

namespace App\Models;

use App\Enums\WorkingSystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWorkInformation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employed_at',
        'working_hours',
        'used_working_hours',
        'working_system',
    ];

    protected $casts = [
        'working_system' => WorkingSystem::class,
        'employed_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
