<?php

namespace App\Observers;

use App\Helpers\ActivityLogger;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        ActivityLogger::log(
            'create_user',
            auth()->user()?->name . ' kullanıcı oluşturdu.',
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
            ]
        );
    }

    public function updated(User $user): void
    {
        ActivityLogger::log(
            'update_user',
            auth()->user()?->name . ' kullanıcı güncelledi.',
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'old_role' => $user->getOriginal('role'),
                'new_role' => $user->role,
                'old_status' => $user->getOriginal('status'),
                'new_status' => $user->status,
            ]
        );
    }

    public function deleted(User $user): void
    {
        ActivityLogger::log(
            'delete_user',
            auth()->user()?->name . ' kullanıcı sildi.',
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        );
    }

    public function restored(User $user): void
    {
        //
    }

    public function forceDeleted(User $user): void
    {
        //
    }
}