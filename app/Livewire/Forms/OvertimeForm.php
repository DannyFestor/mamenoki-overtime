<?php

namespace App\Livewire\Forms;

use App\Enums\OvertimeReason;
use App\Models\Overtime;
use App\Models\OvertimeConfirmation;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Form;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

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

        $overtime = Overtime::query()
            ->where('overtime_confirmation_id', '=', $overtimeConfirmation->id)
            ->where('date', '=', $this->date)
            ->first();
        if ($overtime !== null && $overtime->approved_at !== null) {
            Notification::make()
                ->title( 'エラー')
                ->danger()
                ->color('danger')
                ->body('選択された日付に既に承認済みの残業が存在しています。')
                ->actions([
                    Action::make('view')
                        ->label('残業一覧へ戻る')
                        ->button()
                        ->url(route('overtime.index'), shouldOpenInNewTab: false),
                ])
                ->duration(10000)
                ->send();

            return;
        }

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

        $notification = $dateObject->format('Y年m月d日') . 'の残業を';
        if ($isApplied) {
            $notification .= '申請';
        } else {
            $notification .= '一時保存';
        }
        $notification .= 'しました。';

        $createdString = '更新';
        if ($overtime->wasRecentlyCreated) {
            $createdString = '新規作成';
        }

        Notification::make()
            ->title( $createdString.'しました。')
            ->success()
            ->color('success')
            ->body($notification)
            ->actions([
                Action::make('view')
                    ->label('残業一覧へ戻る')
                    ->button()
                    ->url(route('overtime.index'), shouldOpenInNewTab: false),
            ])
            ->duration(10000)
            ->send();
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
