<?php

namespace App\Filament\Resources\OvertimeResource\Pages;

use App\Filament\Resources\OvertimeResource;
use App\Models\OvertimeConfirmation;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditOvertime extends EditRecord
{
    protected static string $resource = OvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $date = Carbon::parse($data['date']);

        $previousOvertimeConfirmation = OvertimeConfirmation::query()
            ->where('year', '=', $date->year)
            ->where('month', '=', $date->clone()->subMonth()->month)
            ->where('user_id', '=', $data['applicant_user_id'])
            ->first();

        $overtimeConfirmation = OvertimeConfirmation::query()
            ->where('year', '=', $date->year)
            ->where('month', '=', $date->month)
            ->where('user_id', '=', $data['applicant_user_id'])
            ->firstOrCreate([
                'year' => $date->year,
                'month' => $date->month,
                'user_id' => $data['applicant_user_id'],
            ], [
                'remarks' => $previousOvertimeConfirmation?->transfer_remarks,
            ]);

        $data['overtime_confirmation_id'] = $overtimeConfirmation->id;

        return parent::handleRecordUpdate($record, $data);
    }
}
