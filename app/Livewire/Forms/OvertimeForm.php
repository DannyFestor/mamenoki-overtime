<?php

namespace App\Livewire\Forms;

use App\Enums\OvertimeReason;
use App\Models\Overtime;
use App\Models\OvertimeConfirmation;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class OvertimeForm extends Form
{
    public string $date = '';

    public string $timeFrom = '';

    public string $timeUntil = '';

    public int $reason = 0;

    public string $remarks = '';

    public function create(int $userId, bool $isApplied = true)
    {
        $this->validate();

        $dateObject = Carbon::parse($this->date);
        $prevDateObject = $dateObject->clone()->subMonth();

        $previousMonthOvertimeConfirmation = OvertimeConfirmation::where([
            'user_id' => $userId,
            'year' => $prevDateObject->year,
            'month' => $prevDateObject->month,
        ])->first();

        $overtimeConfirmation = OvertimeConfirmation::query()
            ->where('user_id', '=', $userId)
            ->where('year', '=', $dateObject->year)
            ->where('month', '=', $dateObject->month)
            ->firstOrCreate([
                'user_id' => $userId, // TODO: get id for applicant, not logged in user
                'year' => $dateObject->year,
                'month' => $dateObject->month,
                'remarks' => $previousMonthOvertimeConfirmation?->transfer_remarks,
            ]);

        $overtime = Overtime::updateOrCreate([
            'overtime_confirmation_id' => $overtimeConfirmation->id,
            'date' => $this->date,
        ], [
            'time_from' => $this->timeFrom,
            'time_until' => $this->timeUntil,
            'reason' => $this->reason,
            'remarks' => $this->remarks,
            'created_user_id' => $userId,
            'applicant_user_id' => $userId, // TODO: get id for applicant, not logged in user
            'applied_at' => $isApplied ? now() : null,
        ]);
    }

    public function rules()
    {
        return [
            'date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'timeFrom' => ['required', 'date_format:H:i'],
            'timeUntil' => ['required', 'date_format:H:i', 'after:timeFrom'],
            'reason' => ['required', Rule::in(array_keys(OvertimeReason::toArray()))],
            'remarks' => ['nullable', 'min:3'],
        ];
    }
}
