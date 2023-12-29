<?php

namespace App\Models;

use App\Enums\OvertimeReason;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Overtime extends Model
{
    use HasFactory, SoftDeletes;

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

        static::creating(function(Overtime $overtime) {
            $overtime->uuid = Str::uuid();
        });
    }

    public static function filamentTable(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')
                ->label('取得日')
                ->date('Y年m月d日')
                ->sortable(),
            Tables\Columns\TextColumn::make('time_from')
                ->time('H:i時')
                ->label('取得開始時間'),
            Tables\Columns\TextColumn::make('time_until')
                ->time('H:i時')
                ->label('取得終了時間'),
            Tables\Columns\TextColumn::make('creator.name')
                ->label('作成者')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('作成日')
                ->date('Y年m月d日')
                ->sortable()
                ->toggleable(),
            Tables\Columns\TextColumn::make('applicant.name')
                ->label('申請者')
                ->numeric()
                ->sortable()
                ->toggleable(),
            Tables\Columns\TextColumn::make('applied_at')
                ->label('申請日')
                ->date('Y年m月d日')
                ->sortable()
                ->toggleable(),
            Tables\Columns\TextColumn::make('approver.name')
                ->label('承認者')
                ->numeric()
                ->sortable()
                ->toggleable(),
            Tables\Columns\TextColumn::make('approved_at')
                ->label('承認日')
                ->date('Y年m月d日')
                ->sortable()
                ->toggleable(),
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

    public static function filamentForm(): array
    {
        return [
            Forms\Components\Section::make('申請内容')
                ->schema([
                    Forms\Components\DatePicker::make('date')
                        ->label('取得日')
                        ->native(false)
                        ->required(),
                    Forms\Components\TimePicker::make('time_from')
                        ->label('取得開始時間')
                        ->seconds(false)
                        ->format('H:i')
                        ->native(false)
                        ->required(),
                    Forms\Components\TimePicker::make('time_until')
                        ->label('取得終了時間')
                        ->seconds(false)
                        ->format('H:i')
                        ->native(false)
                        ->required(),

                    Forms\Components\Select::make('reason')
                        ->label('理由')
                        ->options(OvertimeReason::toArray())
                        ->required(),
                    Forms\Components\Textarea::make('remarks')
                        ->label('備考')
                        ->maxLength(65535)
                        ->columnSpan(['sm' => 2]),
                ])
                ->columns([
                    'sm' => 3,
                ]),

            Forms\Components\Section::make('作成情報')
                ->schema([
                    Forms\Components\Select::make('created_user_id')
                        ->label('作成者')
                        ->relationship('creator', 'name')
                        ->searchable()
                        ->required(),

                    Forms\Components\DateTimePicker::make('created_at')
                        ->date()
                        ->native(false),
                ])
                ->columns()
                ->collapsible(),

            Forms\Components\Section::make('申請情報')
                ->schema([
                    Forms\Components\Select::make('applicant_user_id')
                        ->label('申請者')
                        ->relationship('applicant', 'name')
                        ->searchable()
                        ->required(),

                    Forms\Components\DateTimePicker::make('applied_at')
                        ->date()
                        ->native(false),
                ])
                ->columns()
                ->collapsible(),

            Forms\Components\Section::make('承認情報')
                ->schema([
                    Forms\Components\Select::make('approval_user_id')
                        ->label('承認者')
                        ->relationship('approver', 'name')
                        ->searchable()
                        ->required(),

                    Forms\Components\DateTimePicker::make('approved_at')
                        ->date()
                        ->native(false),
                ])
                ->columns()
                ->collapsible(),
        ];
    }

    public function overtimeConfirmation(): BelongsTo
    {
        return $this->belongsTo(OvertimeConfirmation::class);
    }

    public function timeDifference(): Attribute
    {
        return Attribute::make(
            get: function() {
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
