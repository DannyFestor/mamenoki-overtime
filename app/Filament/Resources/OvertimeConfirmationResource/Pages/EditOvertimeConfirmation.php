<?php

namespace App\Filament\Resources\OvertimeConfirmationResource\Pages;

use App\Filament\Resources\OvertimeConfirmationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOvertimeConfirmation extends EditRecord
{
    protected static string $resource = OvertimeConfirmationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
