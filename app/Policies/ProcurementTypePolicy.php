<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProcurementType;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcurementTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProcurementType');
    }

    public function view(AuthUser $authUser, ProcurementType $procurementType): bool
    {
        return $authUser->can('View:ProcurementType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProcurementType');
    }

    public function update(AuthUser $authUser, ProcurementType $procurementType): bool
    {
        return $authUser->can('Update:ProcurementType');
    }

    public function delete(AuthUser $authUser, ProcurementType $procurementType): bool
    {
        return $authUser->can('Delete:ProcurementType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProcurementType');
    }

    public function restore(AuthUser $authUser, ProcurementType $procurementType): bool
    {
        return $authUser->can('Restore:ProcurementType');
    }

    public function forceDelete(AuthUser $authUser, ProcurementType $procurementType): bool
    {
        return $authUser->can('ForceDelete:ProcurementType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProcurementType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProcurementType');
    }

    public function replicate(AuthUser $authUser, ProcurementType $procurementType): bool
    {
        return $authUser->can('Replicate:ProcurementType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProcurementType');
    }

}