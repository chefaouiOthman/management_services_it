<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ExcludeSuperAdminScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (app()->runningInConsole() || !app()->bound('auth') || !auth()->hasUser()) {
            return;
        }

        if (!auth()->user()->hasRole('Super Admin')) {
            if ($model instanceof \App\Models\User) {
                $builder->whereDoesntHave('roles', fn ($q) => $q->where('name', 'Super Admin'));
            } elseif ($model instanceof \App\Models\Employe) {
                $builder->whereHas('user', fn ($q) => $q->whereDoesntHave('roles', fn ($r) => $r->where('name', 'Super Admin')));
            }
        }
    }
}
