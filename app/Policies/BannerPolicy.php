<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Banner;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Banner');
    }

    public function view(AuthUser $authUser, Banner $banner): bool
    {
        return $authUser->can('View:Banner');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Banner');
    }

    public function update(AuthUser $authUser, Banner $banner): bool
    {
        return $authUser->can('Update:Banner');
    }

    public function delete(AuthUser $authUser, Banner $banner): bool
    {
        return $authUser->can('Delete:Banner');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Banner');
    }

    public function restore(AuthUser $authUser, Banner $banner): bool
    {
        return $authUser->can('Restore:Banner');
    }

    public function forceDelete(AuthUser $authUser, Banner $banner): bool
    {
        return $authUser->can('ForceDelete:Banner');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Banner');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Banner');
    }

    public function replicate(AuthUser $authUser, Banner $banner): bool
    {
        return $authUser->can('Replicate:Banner');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Banner');
    }

}