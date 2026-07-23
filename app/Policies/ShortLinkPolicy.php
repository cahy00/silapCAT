<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShortLinkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ShortLink');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:ShortLink');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ShortLink');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:ShortLink');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:ShortLink');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ShortLink');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:ShortLink');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:ShortLink');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ShortLink');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ShortLink');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:ShortLink');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ShortLink');
    }
}
