<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ExamScore;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamScorePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamScore');
    }

    public function view(AuthUser $authUser, ExamScore $examScore): bool
    {
        return $authUser->can('View:ExamScore');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamScore');
    }

    public function update(AuthUser $authUser, ExamScore $examScore): bool
    {
        return $authUser->can('Update:ExamScore');
    }

    public function delete(AuthUser $authUser, ExamScore $examScore): bool
    {
        return $authUser->can('Delete:ExamScore');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamScore');
    }

    public function restore(AuthUser $authUser, ExamScore $examScore): bool
    {
        return $authUser->can('Restore:ExamScore');
    }

    public function forceDelete(AuthUser $authUser, ExamScore $examScore): bool
    {
        return $authUser->can('ForceDelete:ExamScore');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExamScore');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExamScore');
    }

    public function replicate(AuthUser $authUser, ExamScore $examScore): bool
    {
        return $authUser->can('Replicate:ExamScore');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExamScore');
    }

}