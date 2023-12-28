<?php

namespace App\Livewire\Forms;

use App\Enums\OvertimeReason;
use App\Models\Overtime;
use App\Models\OvertimeConfirmation;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Reactive;
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

    public function save(int $userId, bool $isApplied = true)
    {
        $this->validate();

        $dateObject = Carbon::parse($this->date);
        $overtimeConfirmation = $this->getOvertimeConfirmationForUserAndDate($userId, $dateObject);

        $overtime = Overtime::query()
            ->where('overtime_confirmation_id', '=', $overtimeConfirmation->id)
            ->where('date', '=', $this->date)
            ->first();
        if ($overtime !== null && $overtime->approved_at !== null) {
            $this->sendAlreadyApprovedNotification();

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
            'applicant_user_id' => $isApplied ? $userId : null, // TODO: get id for applicant, not logged in user
            'applied_at' => $isApplied ? now() : null,
        ]);

        $this->sendSuccessNotification($dateObject, $isApplied, $overtime->wasRecentlyCreated);
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

    /**
     * @param int $userId
     * @param Carbon $dateObject
     * @return OvertimeConfirmation
     */
    private function getOvertimeConfirmationForUserAndDate(int $userId, Carbon $dateObject): OvertimeConfirmation
    {
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
        return $overtimeConfirmation;
    }

    /**
     * @return void
     */
    private function sendAlreadyApprovedNotification(): void
    {
        Notification::make()
            ->title('エラー')
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
    }

    /**
     * @param Carbon $dateObject
     * @param bool $isApplied
     * @param bool $recentlyCreated
     * @return void
     */
    private function sendSuccessNotification(Carbon $dateObject, bool $isApplied, bool $recentlyCreated): void
    {
        $notification = $dateObject->format('Y年m月d日') . 'の残業を';
        if ($isApplied) {
            $notification .= '申請';
        } else {
            $notification .= '一時保存';
        }
        $notification .= 'しました。';

        $createdString = '更新';
        if ($recentlyCreated) {
            $createdString = '新規作成';
        }

        Notification::make()
            ->title($createdString . 'しました。')
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

    public function fromDate(int $userId, string $date): bool
    {
        $overtime = Overtime::query()
            ->select(['overtimes.*', 'overtime_confirmations.user_id'])
            ->leftJoin('overtime_confirmations', 'overtime_confirmations.id', '=', 'overtimes.overtime_confirmation_id')
            ->where('date', '=', $date)
            // TODO: get id in a smarter way, because admin can change ID for user (applicant_user_id)
            ->where('user_id', '=', $userId)
            ->first();

        if ($overtime === null) {
            $this->reset(['timeFrom', 'timeUntil', 'reason', 'remarks']);
            return false;
        }

        $this->date = Carbon::parse($overtime->date)->format('Y-m-d');
        $this->timeFrom = substr($overtime->time_from, 0, 5);
        $this->timeUntil = substr($overtime->time_until, 0, 5);
        $this->reason = $overtime->reason->value;
        $this->remarks = $overtime->remarks;

        return $overtime->approved_at !== null;
    }
}
