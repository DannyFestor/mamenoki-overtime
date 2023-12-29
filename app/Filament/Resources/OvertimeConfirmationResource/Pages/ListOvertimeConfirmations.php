<?php

namespace App\Filament\Resources\OvertimeConfirmationResource\Pages;

use App\Filament\Resources\OvertimeConfirmationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOvertimeConfirmations extends ListRecords
{
    protected static string $resource = OvertimeConfirmationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
