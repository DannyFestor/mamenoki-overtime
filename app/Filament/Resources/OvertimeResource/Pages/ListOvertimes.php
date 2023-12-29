<?php

namespace App\Filament\Resources\OvertimeResource\Pages;

use App\Filament\Resources\OvertimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
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
            'all' => Tab::make('全件'),
            'draft' => Tab::make('未申請')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->whereNull('applied_at')
                    ->whereNull('approved_at')
                ),
            'applied' => Tab::make('申請済み')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->whereNotNull('applied_at')
                    ->whereNull('approved_at')
                ),
            'approved' => Tab::make('承認済み')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->whereNotNull('applied_at')
                    ->whereNotNull('approved_at')
                ),
        ];
    }
}
