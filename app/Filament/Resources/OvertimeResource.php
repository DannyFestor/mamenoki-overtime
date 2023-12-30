<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeResource\Pages;
use App\Models\Overtime;
use Auth;
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
            ->schema(Overtime::filamentForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(Overtime::filamentTable())
            ->defaultSort('date', 'DESC')
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('年')
                    ->options(function() {
                        $v = [];
                        for ($i = now()->year; $i >= 2000; $i--) {
                            $v[$i] = $i . '年';
                        }

                        return $v;
                    }),
                Tables\Filters\SelectFilter::make('month')
                    ->label('月')
                    ->options(function() {
                        $v = [];
                        for ($i = 1; $i <= 12; $i++) {
                            $v[$i] = $i . '月';
                        }

                        return $v;
                    }),
                Tables\Filters\SelectFilter::make('creator')
                    ->label('作成者')
                    ->relationship('creator', 'name'),
                Tables\Filters\SelectFilter::make('applicant')
                    ->label('申請者')
                    ->relationship('applicant', 'name'),
                Tables\Filters\SelectFilter::make('approver')
                    ->label('承認者')
                    ->relationship('approver', 'name'),
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
