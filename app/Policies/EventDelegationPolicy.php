<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EventDelegation;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventDelegationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventDelegation');
    }

    public function view(AuthUser $authUser, EventDelegation $eventDelegation): bool
    {
        return $authUser->can('View:EventDelegation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventDelegation');
    }

    public function update(AuthUser $authUser, EventDelegation $eventDelegation): bool
    {
        return $authUser->can('Update:EventDelegation');
    }

    public function delete(AuthUser $authUser, EventDelegation $eventDelegation): bool
    {
        return $authUser->can('Delete:EventDelegation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventDelegation');
    }

    public function restore(AuthUser $authUser, EventDelegation $eventDelegation): bool
    {
        return $authUser->can('Restore:EventDelegation');
    }

    public function forceDelete(AuthUser $authUser, EventDelegation $eventDelegation): bool
    {
        return $authUser->can('ForceDelete:EventDelegation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventDelegation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventDelegation');
    }

    public function replicate(AuthUser $authUser, EventDelegation $eventDelegation): bool
    {
        return $authUser->can('Replicate:EventDelegation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventDelegation');
    }

}