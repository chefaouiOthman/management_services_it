<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function authorizeRole(string ...$roles): void
    {
        if (! auth()->user() || ! auth()->user()->hasAnyRole($roles)) {
            abort(403, 'Action non autorisée pour votre profil.');
        }
    }

    protected function denyInventaireMutation(): void
    {
        if (auth()->user() && (auth()->user()->hasRole('Stagiaire') || auth()->user()->hasRole('Employe_Standard'))) {
            abort(403, 'Action non autorisée pour votre profil.');
        }
    }
}
