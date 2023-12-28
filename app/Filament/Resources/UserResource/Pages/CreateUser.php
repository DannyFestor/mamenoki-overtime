<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Notifications\PasswordResetNotification;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $password = \Str::random();

        $data['password'] = \Hash::make($password);

        $record = parent::handleRecordCreation($data);

        $record->notify(new PasswordResetNotification($password));

        Notification::make('password-reset')
            ->title('新しいパスワードを発行しました。')
            ->body($record->email . 'に情報を送信しました。')
            ->send();

        return $record;
    }
}
