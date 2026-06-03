<?php

namespace App\Traits;

use App\Models\User;
use Filament\Notifications\Notification;

trait NotifiesOnCreate
{
    public static function bootNotifiesOnCreate()
    {
        static::created(function ($model) {
            $modelName = class_basename($model);
            $userName = auth()->user()?->name ?? 'System';
            
            // Get all users to notify (Admins)
            $users = User::all();

            foreach ($users as $user) {
                Notification::make()
                    ->title("Data {$modelName} Baru")
                    ->body("{$userName} telah menambahkan data {$modelName} baru.")
                    ->icon('heroicon-o-bell')
                    ->iconColor('success')
                    ->sendToDatabase($user);
            }
        });
    }
}
