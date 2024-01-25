<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use IntlDateFormatter;
use Str;

class OvertimeConfirmation extends Model
{
    use HasFactory, SoftDeletes;

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

        static::creating(function(OvertimeConfirmation $overtimeConfirmation) {
            $overtimeConfirmation->uuid = Str::uuid();
        });
    }

    public static function filamentTable(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label('名前')
                ->sortable(),
            Tables\Columns\TextColumn::make('year')
                ->label('年')
                ->suffix('年')
                ->sortable(query: function(Builder $query, string $direction): Builder {
                    return $query
                        ->orderBy('year', $direction)
                        ->orderBy('month', $direction);
                }),
            Tables\Columns\TextColumn::make('month')
                ->label('月')
                ->suffix('月')
                ->numeric(),
            Tables\Columns\IconColumn::make('confirmed_at')
                ->label('最終確認済み')
                ->icon(function(string $state): string {
                    if ($state === '') {
                        return 'heroicon-o-x-mark';
                    }

                    return 'heroicon-o-check-badge';
                })
                ->color(function(string $state): string {
                    if ($state === '') {
                        return 'danger';
                    }

                    return 'success';
                })
                ->default('')
                ->alignCenter()
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function overtimes(): HasMany
    {
        return $this->hasMany(Overtime::class, 'overtime_confirmation_id');
    }

    public function scopeConfirmed(Builder $builder): Builder
    {
        return $builder->whereNotNull('confirmed_at');
    }

    public function scopeUnconfirmed(Builder $builder): Builder
    {
        return $builder->whereNull('confirmed_at');
    }

    public function japaneseYear(): Attribute
    {
        return Attribute::make(
            get: function(mixed $value, array $attributes) {
                $formatter = new IntlDateFormatter(
                    'ja_JP@calendar=japanese', IntlDateFormatter::FULL,
                    IntlDateFormatter::NONE, 'Asia/Tokyo', IntlDateFormatter::TRADITIONAL
                );

                $dateString = $formatter->format(Carbon::parse($attributes['year'] . '-' . $attributes['month']));

                $dateString = substr($dateString, 0, strpos($dateString, '年'));

                return $dateString;
            }
        );
    }
}
