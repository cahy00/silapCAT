<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Employee');
    }

    public function view(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('View:Employee');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Employee');
    }

    public function update(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('Update:Employee');
    }

    public function delete(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('Delete:Employee');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Employee');
    }

    public function restore(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('Restore:Employee');
    }

    public function forceDelete(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('ForceDelete:Employee');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Employee');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Employee');
    }

    public function replicate(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('Replicate:Employee');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Employee');
    }

}