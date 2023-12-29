<?php

namespace App\Models;

use App\Enums\OvertimeReason;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Tables;

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

    public static function filamentTable(): array
    {
        return [
            Tables\Columns\TextColumn::make('overtime_confirmation_id')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_user_id')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('applicant_user_id')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('approval_user_id')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('uuid')
                ->label('UUID')
                ->searchable(),
            Tables\Columns\TextColumn::make('date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('time_from'),
            Tables\Columns\TextColumn::make('time_until'),
            Tables\Columns\TextColumn::make('applied_at')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('approved_at')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('deleted_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public function timeDifference(): Attribute
    {
        return Attribute::make(
            get: function () {
                $timeFrom = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time_from);
                $timeUntil = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time_until);

                $diff = $timeUntil->diff($timeFrom);
                $format = '';
                if ($diff->h !== 0) {
                    $format .= '%h時間';
                }
                if ($diff->i !== 0) {
                    $format .= '%i分';
                }

                return $timeUntil->diff($timeFrom)->format($format);
            }
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_user_id');
    }
}
