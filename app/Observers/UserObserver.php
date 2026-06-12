<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $actor = Auth::user();
        $action = $actor ? 'Created Admin/Client' : 'Registered Account';
        
        ActivityLog::create([
            'user_id' => $actor?->id ?? $user->id,
            'action' => $action,
            'description' => "Account for '{$user->name}' ({$user->email}) was created.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $actor = Auth::user();
        if (!$actor) return; // Only log administrative updates in the portal

        $dirty = $user->getDirty();
        unset($dirty['updated_at']); // Ignore automated timestamps
        
        if (empty($dirty)) return;

        $changes = [];
        foreach ($dirty as $field => $newValue) {
            $oldValue = $user->getOriginal($field);
            if ($field === 'password') {
                $changes[] = "password was reset";
            } else {
                $changes[] = "$field changed from '{$oldValue}' to '{$newValue}'";
            }
        }

        ActivityLog::create([
            'user_id' => $actor->id,
            'action' => 'Updated Admin/Client',
            'description' => "Account for '{$user->name}' was updated: " . implode(', ', $changes),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $actor = Auth::user();
        if (!$actor) return;

        ActivityLog::create([
            'user_id' => $actor->id,
            'action' => 'Deleted Admin/Client',
            'description' => "Account for '{$user->name}' ({$user->email}) was deleted.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
