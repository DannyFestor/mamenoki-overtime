<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeResource\Pages;
use App\Filament\Resources\OvertimeResource\RelationManagers;
use App\Models\Overtime;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make('overtime_confirmation_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('created_user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('applicant_user_id')
                    ->numeric(),
                Forms\Components\TextInput::make('approval_user_id')
                    ->numeric(),
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->required()
                    ->maxLength(36),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('time_from')
                    ->required(),
                Forms\Components\TextInput::make('time_until')
                    ->required(),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('remarks')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('applied_at'),
                Forms\Components\DateTimePicker::make('approved_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
