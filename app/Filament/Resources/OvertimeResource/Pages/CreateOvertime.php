<?php

namespace App\Filament\Resources\OvertimeResource\Pages;

use App\Filament\Resources\OvertimeResource;
use App\Models\OvertimeConfirmation;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOvertime extends CreateRecord
{
    protected static string $resource = OvertimeResource::class;

    protected function handleRecordCreation(array $data): Model
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

        return parent::handleRecordCreation($data);
    }
}
