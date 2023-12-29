<?php

namespace App\Filament\Resources\OvertimeResource\Pages;

use App\Filament\Resources\OvertimeResource;
use App\Models\Overtime;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
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
}
