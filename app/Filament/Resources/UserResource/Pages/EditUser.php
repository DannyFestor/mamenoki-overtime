<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('新しいパスワードの発行')
                ->requiresConfirmation()
                ->action(function (User $record) {
                    $password = \Str::random();

                    $record->update([
                        'password' => \Hash::make($password),
                    ]);

                    $record->notify(new PasswordResetNotification($password));

                    Notification::make('password-reset')
                        ->title('新しいパスワードを発行しました。')
                        ->body($record->email . 'に情報を送信しました。')
                        ->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
