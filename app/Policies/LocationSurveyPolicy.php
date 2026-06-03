<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LocationSurvey;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationSurveyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LocationSurvey');
    }

    public function view(AuthUser $authUser, LocationSurvey $locationSurvey): bool
    {
        return $authUser->can('View:LocationSurvey');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LocationSurvey');
    }

    public function update(AuthUser $authUser, LocationSurvey $locationSurvey): bool
    {
        return $authUser->can('Update:LocationSurvey');
    }

    public function delete(AuthUser $authUser, LocationSurvey $locationSurvey): bool
    {
        return $authUser->can('Delete:LocationSurvey');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LocationSurvey');
    }

    public function restore(AuthUser $authUser, LocationSurvey $locationSurvey): bool
    {
        return $authUser->can('Restore:LocationSurvey');
    }

    public function forceDelete(AuthUser $authUser, LocationSurvey $locationSurvey): bool
    {
        return $authUser->can('ForceDelete:LocationSurvey');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LocationSurvey');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LocationSurvey');
    }

    public function replicate(AuthUser $authUser, LocationSurvey $locationSurvey): bool
    {
        return $authUser->can('Replicate:LocationSurvey');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LocationSurvey');
    }

}