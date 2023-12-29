<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeResource\Pages;
use App\Models\Overtime;
use Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class OvertimeResource extends Resource
{
    protected static ?string $model = Overtime::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = '残業申請系';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = '申請内容管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

                        Forms\Components\DateTimePicker::make('created_at'),
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

                        Forms\Components\DateTimePicker::make('applied_at'),
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

                        Forms\Components\DateTimePicker::make('approved_at'),
                    ])
                    ->columns()
                    ->collapsible(),

                Forms\Components\TextInput::make('overtime_confirmation_id')
                    ->required()
                    ->numeric(),

                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('remarks')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(Overtime::filamentTable())
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('選択中を承認する')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(Component $livewire) => $livewire->activeTab === 'applied')
                        ->action(fn(Collection $records) => $records->each(fn(Overtime $overtime) => $overtime->update([
                            'approved_at' => now(),
                            'approval_user_id' => Auth::id(),
                        ])))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOvertimes::route('/'),
            'create' => Pages\CreateOvertime::route('/create'),
            'edit' => Pages\EditOvertime::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->whereNull('approved_at')
            ->whereNotNull('applied_at')
            ->count();
    }
}
