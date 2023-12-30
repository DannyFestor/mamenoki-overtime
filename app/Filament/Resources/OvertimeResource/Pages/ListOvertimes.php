<?php

namespace App\Filament\Resources\OvertimeResource\Pages;

use App\Filament\Resources\OvertimeResource;
use App\Models\Overtime;
use Auth;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->select(['overtimes.*', 'overtime_confirmations.year', 'overtime_confirmations.month', 'overtime_confirmations.user_id', 'users.name'])
                ->leftJoin('overtime_confirmations', 'overtime_confirmations.id', '=', 'overtimes.overtime_confirmation_id')
                ->leftJoin('users', 'users.id', '=', 'overtime_confirmations.user_id')
            );
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('全件')
                ->icon('heroicon-o-list-bullet'),

            'draft' => Tab::make('未申請')
                ->icon('heroicon-o-bookmark')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->whereNull('applied_at')
                    ->whereNull('approved_at')
                ),

            'applied' => Tab::make('申請済み')
                ->icon('heroicon-o-cloud')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->whereNotNull('applied_at')
                    ->whereNull('approved_at')
                )
                ->badge(Overtime::query()
                    ->whereNull('approved_at')
                    ->whereNotNull('applied_at')
                    ->count()),

            'approved' => Tab::make('承認済み')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->whereNotNull('applied_at')
                    ->whereNotNull('approved_at')
                ),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('未申請を削除する')
                ->color('danger')
                ->requiresConfirmation()
                ->modalContent(new HtmlString('<span class="text-center">確認ボタンを押すと３ヶ月前より古い未申請を削除されます</span>'))
                ->action(function() {
                    Overtime::query()
                        ->whereNull('approved_at')
                        ->whereNull('applied_at')
                        ->where('created_at', '<', now()->subMonths(3))
                        ->delete();
                }),

            Actions\Action::make('申請済みを承認する')
                ->color('success')
                ->requiresConfirmation()
                ->action(function() {
                    Overtime::query()
                        ->whereNull('approved_at')
                        ->whereNotNull('applied_at')
                        ->update([
                            'approved_at' => now(),
                            'approval_user_id' => Auth::id(),
                        ]);
                }),
            Actions\CreateAction::make(),
        ];
    }
}
