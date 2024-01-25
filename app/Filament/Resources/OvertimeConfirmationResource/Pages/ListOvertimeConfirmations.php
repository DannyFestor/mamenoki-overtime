<?php

namespace App\Filament\Resources\OvertimeConfirmationResource\Pages;

use App\Filament\Resources\OvertimeConfirmationResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOvertimeConfirmations extends ListRecords
{
    protected static string $resource = OvertimeConfirmationResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('全件')
                ->icon('heroicon-o-list-bullet'),

            'unconfirmed' => Tab::make('最終確認済み以外')
                ->icon('heroicon-o-bookmark')
                ->modifyQueryUsing(fn(Builder $query) => $query->unconfirmed()),

            'confirmed' => Tab::make('最終確認済みのみ')
                ->icon('heroicon-o-cloud')
                ->modifyQueryUsing(fn(Builder $query) => $query->confirmed()),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
