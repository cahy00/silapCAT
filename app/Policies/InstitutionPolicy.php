<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Institution;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstitutionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Institution');
    }

    public function view(AuthUser $authUser, Institution $institution): bool
    {
        return $authUser->can('View:Institution');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Institution');
    }

    public function update(AuthUser $authUser, Institution $institution): bool
    {
        return $authUser->can('Update:Institution');
    }

    public function delete(AuthUser $authUser, Institution $institution): bool
    {
        return $authUser->can('Delete:Institution');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Institution');
    }

    public function restore(AuthUser $authUser, Institution $institution): bool
    {
        return $authUser->can('Restore:Institution');
    }

    public function forceDelete(AuthUser $authUser, Institution $institution): bool
    {
        return $authUser->can('ForceDelete:Institution');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Institution');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Institution');
    }

    public function replicate(AuthUser $authUser, Institution $institution): bool
    {
        return $authUser->can('Replicate:Institution');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Institution');
    }

}