<?php

namespace App\Http\Controllers\Traits;

use App\Models\User;
use App\Models\Employe;
use Illuminate\Support\Facades\Auth;

trait FilterSuperAdmin
{
    protected function excludeSuperAdminsFromUsers($query)
    {
        if (!Auth::user()->hasRole('Super Admin')) {
            $query->whereDoesntHave('roles', fn ($q) => $q->where('name', 'Super Admin'));
        }
        return $query;
    }

    protected function excludeSuperAdminsFromEmployes($query)
    {
        if (!Auth::user()->hasRole('Super Admin')) {
            $query->whereHas('user', fn ($q) => $q->whereDoesntHave('roles', fn ($r) => $r->where('name', 'Super Admin')));
        }
        return $query;
    }

    protected function validateNotSuperAdminTarget($request, $userIdField = 'user_id', $employeIdField = 'employe_id')
    {
        if (Auth::user()->hasRole('Super Admin')) return;

        $userId = $request->input($userIdField);
        if ($userId) {
            $targetUser = User::find($userId);
            if ($targetUser && $targetUser->hasRole('Super Admin')) {
                abort(403, 'Action non autorisée sur un Super Administrateur.');
            }
        }

        $employeId = $request->input($employeIdField);
        if ($employeId) {
            $targetUser = Employe::find($employeId)?->user;
            if ($targetUser && $targetUser->hasRole('Super Admin')) {
                abort(403, 'Action non autorisée sur un Super Administrateur.');
            }
        }
    }

    protected function abortIfTargetIsSuperAdmin(User $targetUser): void
    {
        if (!Auth::user()->hasRole('Super Admin') && $targetUser->hasRole('Super Admin')) {
            abort(403, 'Action non autorisée sur un Super Administrateur.');
        }
    }
}
