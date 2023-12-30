<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeConfirmationResource\Pages;
use App\Models\OvertimeConfirmation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OvertimeConfirmationResource extends Resource
{
    protected static ?string $model = OvertimeConfirmation::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = '残業申請系';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = '最終確認管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本情報')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('year')
                            ->label('年')
                            ->options(function(): array {
                                $v = [];
                                for ($i = now()->year; $i >= 2000; $i--) {
                                    $v[$i] = "{$i}年";
                                }

                                return $v;
                            })
                            ->required(),
                        Forms\Components\Select::make('month')
                            ->label('月')
                            ->options(function(): array {
                                $v = [];
                                for ($i = 1; $i <= 12; $i++) {
                                    $v[$i] = "{$i}月";
                                }

                                return $v;
                            })
                            ->required(),
                    ])->columns(3),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Textarea::make('remarks')
                            ->label('備考欄')
                            ->hint('当月分')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('transfer_remarks')
                            ->label('引継ぎ欄')
                            ->hint('ここに記入したことは来月の「備考欄」に反映されます')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\DateTimePicker::make('confirmed_at')
                            ->label('最終確認日')
                            ->date()
                            ->nullable()
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(OvertimeConfirmation::filamentTable())
            ->defaultSort('year', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('名前')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('confirmed_at')
                    ->label('最終確認ずみ')
                    ->placeholder('全ての件')
                    ->trueLabel('最終確認済みのみ')
                    ->falseLabel('最終確認済み以外')
                    ->queries(
                        true: fn(Builder $query) => $query->confirmed(),
                        false: fn(Builder $query) => $query->unconfirmed(),
                        blank: fn(Builder $query) => $query,
                    ),
                Tables\Filters\SelectFilter::make('year')
                    ->label('年')
                    ->options(fn() => OvertimeConfirmation::query()
                        ->selectRaw('DISTINCT(year)')
                        ->orderByDesc('year')
                        ->pluck('year')
                        ->mapWithKeys(fn(string $year): array => [$year => $year . '年'])
                    ),
                Tables\Filters\SelectFilter::make('month')
                    ->label('月')
                    ->options(fn() => OvertimeConfirmation::query()
                        ->selectRaw('DISTINCT(month)')
                        ->orderByDesc('month')
                        ->pluck('month')
                        ->mapWithKeys(fn(string $month): array => [$month => $month . '月'])
                    ),
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
            'index' => Pages\ListOvertimeConfirmations::route('/'),
            'create' => Pages\CreateOvertimeConfirmation::route('/create'),
            'edit' => Pages\EditOvertimeConfirmation::route('/{record}/edit'),
        ];
    }
}
