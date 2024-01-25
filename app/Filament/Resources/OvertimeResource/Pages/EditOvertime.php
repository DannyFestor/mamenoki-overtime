<?php

namespace App\Filament\Resources\OvertimeResource\Pages;

use App\Filament\Resources\OvertimeResource;
use App\Models\Overtime;
use App\Models\OvertimeConfirmation;
use Auth;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Overtime $record
 */
class EditOvertime extends EditRecord
{
    protected static string $resource = OvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('申請する')
                ->visible(fn() => $this->record->applied_at === null)
                ->color('success')
                ->icon('heroicon-o-hand-thumb-up')
                ->action(function() {
                    $this->record->applied_at = now();
                    $this->record->applicant_user_id = Auth::id();
                    $this->record->save();
                    $this->record->refresh();

                    Notification::make('success_notification')
                        ->title('申請を保存しました')
                        ->success()
                        ->send();

                    $this->redirect(route('filament.admin.resources.overtimes.edit', ['record' => $this->record]));
                })
                ->requiresConfirmation(),
            Actions\Action::make('申請を取り消す')
                ->visible(fn() => $this->record->applied_at !== null)
                ->color('warning')
                ->icon('heroicon-o-hand-thumb-up')
                ->action(function() {
                    $this->record->applied_at = null;
                    $this->record->applicant_user_id = null;
                    $this->record->approved_at = null;
                    $this->record->approval_user_id = null;
                    $this->record->save();
                    $this->record->refresh();

                    Notification::make('success_notification')
                        ->title('申請を保存しました')
                        ->success()
                        ->send();

                    $this->redirect(route('filament.admin.resources.overtimes.edit', ['record' => $this->record]));
                })
                ->requiresConfirmation(),
            Actions\Action::make('承認する')
                ->visible(fn() => $this->record->approved_at === null)
                ->color('success')
                ->icon('heroicon-o-check-badge')
                ->action(function() {
                    $this->record->approved_at = now();
                    $this->record->approval_user_id = Auth::id();
                    if ($this->record->applied_at === null) {
                        $this->record->applied_at = now();
                        $this->record->applicant_user_id = Auth::id();
                    }
                    $this->record->save();

                    Notification::make('success_notification')
                        ->title('承認を保存しました')
                        ->success()
                        ->send();

                    $this->redirect(route('filament.admin.resources.overtimes.edit', ['record' => $this->record]));
                })
                ->requiresConfirmation(),
            Actions\Action::make('承認を取り消す')
                ->visible(fn() => $this->record->approved_at !== null)
                ->color('warning')
                ->icon('heroicon-o-check-badge')
                ->action(function() {
                    $this->record->approved_at = null;
                    $this->record->approval_user_id = null;
                    $this->record->save();
                    $this->record->refresh();

                    Notification::make('success_notification')
                        ->title('承認を取り消しました')
                        ->success()
                        ->send();

                    $this->redirect(route('filament.admin.resources.overtimes.edit', ['record' => $this->record]));
                })
                ->requiresConfirmation(),
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
