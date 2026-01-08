<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response; // Added Response import for clarity

class AnnouncementPolicy
{
    use HandlesAuthorization;

    /**
     * Optional: Allow administrators or staff to perform any action.
     * This method runs BEFORE any other check in the policy.
     * * @param \App\Models\User $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, $ability)
    {
        // Assuming 'admin' or 'staff' roles are privileged and should bypass creator checks.
        if ($user->role === 'admin' || $user->role === 'staff') {
            return true;
        }
        return null; // Continue to the specific method (update/delete)
    }

    /**
     * Determine whether the user can update the announcement.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Announcement $announcement)
    {
        // Allow the user to update the announcement ONLY if their ID matches 
        // the author_id stored on the announcement record.
        return $user->id === $announcement->author_id 
                ? Response::allow()
                : Response::deny('You do not own this announcement and cannot edit it.');
    }
    
    /**
     * Determine whether the user can delete the announcement.
     * * @param  \App\Models\User  $user
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Announcement $announcement)
    {
        // FIX: Changed $announcement->user_id to $announcement->author_id
        return $user->id === $announcement->author_id 
                ? Response::allow()
                : Response::deny('You do not own this announcement and cannot delete it.');
    }
}