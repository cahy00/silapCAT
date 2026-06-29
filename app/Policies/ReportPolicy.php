<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Report;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Report');
    }

    public function view(AuthUser $authUser, Report $report): bool
    {
        return $authUser->can('View:Report');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Report');
    }

    public function update(AuthUser $authUser, Report $report): bool
    {
        return $authUser->can('Update:Report');
    }

    public function delete(AuthUser $authUser, Report $report): bool
    {
        return $authUser->can('Delete:Report');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Report');
    }

    public function restore(AuthUser $authUser, Report $report): bool
    {
        return $authUser->can('Restore:Report');
    }

    public function forceDelete(AuthUser $authUser, Report $report): bool
    {
        return $authUser->can('ForceDelete:Report');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Report');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Report');
    }

    public function replicate(AuthUser $authUser, Report $report): bool
    {
        return $authUser->can('Replicate:Report');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Report');
    }

}