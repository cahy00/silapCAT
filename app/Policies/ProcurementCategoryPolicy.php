<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProcurementCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcurementCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProcurementCategory');
    }

    public function view(AuthUser $authUser, ProcurementCategory $procurementCategory): bool
    {
        return $authUser->can('View:ProcurementCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProcurementCategory');
    }

    public function update(AuthUser $authUser, ProcurementCategory $procurementCategory): bool
    {
        return $authUser->can('Update:ProcurementCategory');
    }

    public function delete(AuthUser $authUser, ProcurementCategory $procurementCategory): bool
    {
        return $authUser->can('Delete:ProcurementCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProcurementCategory');
    }

    public function restore(AuthUser $authUser, ProcurementCategory $procurementCategory): bool
    {
        return $authUser->can('Restore:ProcurementCategory');
    }

    public function forceDelete(AuthUser $authUser, ProcurementCategory $procurementCategory): bool
    {
        return $authUser->can('ForceDelete:ProcurementCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProcurementCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProcurementCategory');
    }

    public function replicate(AuthUser $authUser, ProcurementCategory $procurementCategory): bool
    {
        return $authUser->can('Replicate:ProcurementCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProcurementCategory');
    }

}